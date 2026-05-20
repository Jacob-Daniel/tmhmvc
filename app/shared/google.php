<?php
function getGoogleOAuthUrl(): string {
    require_once __DIR__ . '/../../vendor/autoload.php';	
    $client = new Google_Client();
    $client->setClientId(GOOGLEAPI_CLIENT_ID);
    $client->setClientSecret(GOOGLEAPI_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLEAPI_REDIRECT_URI);
    $client->setAccessType('offline');
    $client->setPrompt('consent');
    $client->addScope("https://www.googleapis.com/auth/gmail.send");

    return $client->createAuthUrl();
}

function handleGoogleOAuthTokenFlow(mysqli $db)
{
    require_once __DIR__ . '/../../vendor/autoload.php';

    $client = new Google_Client();
    $client->setClientId(GOOGLEAPI_CLIENT_ID);
    $client->setClientSecret(GOOGLEAPI_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLEAPI_REDIRECT_URI);
    $client->setAccessType('offline'); // Needed for refresh token
    $client->setPrompt('consent'); // Force consent screen to get refresh_token
    $client->addScope("https://www.googleapis.com/auth/gmail.send");

    // Handle the Google redirect with `code` param
	if (isset($_GET['code'])) {
	    try {
	        $client->authenticate($_GET['code']);
	        $token = $client->getAccessToken();

	        // Restore old refresh_token if missing in the new token
	        if (empty($token['refresh_token']) && isset($_SESSION['access_token']['refresh_token'])) {
	            $token['refresh_token'] = $_SESSION['access_token']['refresh_token'];
	        }

	        $_SESSION['access_token'] = $token;
	    } catch (Exception $e) {
	        echo 'Error authenticating: ' . $e->getMessage();
	        exit;
	    }
	}

    // If access token was not set, abort silently (this page is only meant to receive redirects)
    if (!isset($_SESSION['access_token']) || !$_SESSION['access_token']) {
        http_response_code(400);
        echo "No token found in session.";
        return;
    }

    $client->setAccessToken($_SESSION['access_token']);
    $access_token = $client->getAccessToken();

    if (!$access_token) {
        echo "Token not retrieved.";
        return;
    }

    $accessToken = $access_token['access_token'];
    $expiresIn = $access_token['expires_in'];
    $expiresAt = time() + $expiresIn;

    // Load existing refresh_token from DB if needed
    $existing_refresh_token = null;
    $query = "SELECT refresh_token FROM oauth_tokens WHERE id = 1";
    $result = $db->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $existing_refresh_token = $row['refresh_token'];
    }

    // Use new refresh_token if returned, or fallback
    $refresh_token = !empty($access_token['refresh_token']) 
        ? $access_token['refresh_token']
        : $existing_refresh_token;

    // Save to database
    if ($result && $result->num_rows > 0) {
        $stmt = $db->prepare("UPDATE oauth_tokens SET access_token = ?, refresh_token = ?, expires_at = ? WHERE id = 1");
    } else {
        $stmt = $db->prepare("INSERT INTO oauth_tokens (access_token, refresh_token, expires_at) VALUES (?, ?, ?)");
    }

    $stmt->bind_param("ssi", $accessToken, $refresh_token, $expiresAt);

    if (!$stmt->execute()) {
        $_SESSION['access_grant'] = false;
        $_SESSION['access_grant_message'] = "Error saving token: " . $stmt->error;
        header('Location: /admin/index.php');        
        exit;
    }

    $stmt->close();
    $_SESSION['access_grant'] = true;    
    $_SESSION['access_grant_message'] = 'Google access granted successfully.';
    header('Location: /admin/index.php');
    exit;
}

function refreshGoogleAccessToken(mysqli $db, $tokenId = 1)
{
    require_once __DIR__ . '/../../vendor/autoload.php';

    $query = "SELECT refresh_token FROM oauth_tokens WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $tokenId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result || $result->num_rows === 0) {
        return false; 
    }
    
    $row = $result->fetch_assoc();
    $refreshToken = $row['refresh_token'];
    
    if (empty($refreshToken)) {
        return false; 
    }

    $client = new Google_Client();
    $client->setClientId(GOOGLEAPI_CLIENT_ID);
    $client->setClientSecret(GOOGLEAPI_CLIENT_SECRET);
    $client->setAccessType('offline');
    
    try {
        // This happens server-to-server, no browser needed!
        $client->fetchAccessTokenWithRefreshToken($refreshToken);
        $newToken = $client->getAccessToken();
        
        $accessToken = $newToken['access_token'];
        $expiresAt = time() + $newToken['expires_in'];
        
        $updateStmt = $db->prepare("UPDATE oauth_tokens SET access_token = ?, expires_at = ? WHERE id = ?");
        $updateStmt->bind_param("sii", $accessToken, $expiresAt, $tokenId);
        $updateStmt->execute();

        $message = '<div class="alert alert-success">
            <p>✓ Google token refreshed successfully!</p>
          </div>';
        
        $_SESSION['refresh_message'] = $message;

        return $accessToken;
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger">
            <p>Failed to refresh Google token!</p>
          </div>';
        
        $_SESSION['refresh_message'] = $message;
        error_log("Failed to refresh Google token: " . $e->getMessage());
        return false;
    }
}

function sendGsuite(
    $template, 
    $mailid, 
    $memberid, 
    $subject, 
    $from, 
    $mailtype, 
    string $token
): bool {
    global $db, $config;

    // Resolve recipient
    $emailto = '';
    $toname  = '';

    if ($mailtype === 'm') {
        $member = getRecord('members', 'id', $memberid);
        if (!$member || empty($member->email)) {
            error_log("sendGsuite: member {$memberid} not found or has no email.");
            return false;
        }
        $nsdoms  = [];
        $parts   = explode('@', $member->email);
        if (in_array($parts[1] ?? '', $nsdoms)) {
            error_log("sendGsuite: domain barred for member {$memberid}.");
            return false;
        }
        $emailto = $member->email;
        $toname  = $member->email;
    }

    if (empty($emailto)) {
        error_log("sendGsuite: no recipient resolved.");
        return false;
    }

    // Build body
    $body = '';
    if (is_numeric($template)) {
        $mrec = getRecord('emails', 'id', $template);
        if (!$mrec) {
            error_log("sendGsuite: template {$template} not found.");
            return false;
        }
        $body = buildEmailBody($mrec, $config, $emailto);
    } else {
        $body = $template;
    }

    // Replace placeholders
    $body = replacePlaceholders($body, $emailto, $config);

    // Send
    $client = GoogleApiClientFactory::getClient($token);
    $gmail  = new Google_Service_Gmail($client);

    $rawMessage  = "From: {$from}\r\n";
    $rawMessage .= "To: {$toname} <{$emailto}>\r\n";
    $rawMessage .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
    $rawMessage .= "MIME-Version: 1.0\r\n";
    $rawMessage .= "Return-Path: {$from}\r\n";
    $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
    $rawMessage .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
    $rawMessage .= quoted_printable_encode($body) . "\r\n";

    $mime    = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');
    $message = new Google_Service_Gmail_Message();
    $message->setRaw($mime);

    try {
        $result = $gmail->users_messages->send('me', $message);
        error_log("sendGsuite: sent to {$emailto}, thread {$result->threadId}");
        return true;
    } catch (Exception $e) {
        error_log("sendGsuite: failed for {$emailto}: " . $e->getMessage());
        return false;
    }
}

function buildEmailBody(object $mrec, object $config, string $emailto): string
{
    $usuburl = NEXTJS_BASE_URL . 'unsubscribe';
  $footer  = '<div style="padding:10px; font-size:14px; background-color:#6f866a;">';
        $footer .= '<ul style="margin:0px 0px 15px 0px; padding:0px; list-style:none;">';
        $footer .= '<li><b>' . $config->comp_name . '</b><br />';
        $footer .= $config->address . '<br />';
        $footer .= $config->postcode . '<br />';
        $footer .= $config->tel . '<br />';
        $footer .= '<a style="display:block; color:black;" href="' . NEXTJS_BASE_URL . '">' . $config->domain . '</a><br />';
        $footer .= '</li>';
        $footer .= '<li style="margin:0px; padding:0px;">';
        $footer .= '<b>Shop</b>';
        $footer .= '<a style="display:block; color:black; margin-bottom:5px;" href="https://hearingeye.org">Hearing Eye</a>';
        $footer .= '</li>';
        $footer .= '<li>';
        $footer .= '<b>Support</b>';
        $footer .= '<a style="display:block; color:black;" href="' . NEXTJS_BASE_URL . 'support">Support</a>';
        $footer .= '</li>';
        $footer .= '</ul>';
        $footer .= '<ul style="margin:0px 0px 15px 0px; padding:0px; list-style:none;">';
        $footer .= '<li>';
        $footer .= '<a style="display:block; color:black;" href="' . $config->fb_url . '">Facebook</a>';
        $footer .= '</li>';
        $footer .= '<li>';
        $footer .= '<a style="display:block; color:black;" href="' . $config->inst_url . '">Instagram</a>';
        $footer .= '</li>';
        $footer .= '<li>';
        $footer .= '<a style="color:black;" href="' . $usuburl . '">Unsubscribe</a>';
        $footer .= '</li>';
        $footer .= '</ul>';
        $footer .= '</div>';
        $body = '<div style="border:1px solid gainsboro; margin:0px 30px; max-width: 650px;">';
        $body .= '<div style="font-size:14px; padding: 15px;">'; 
        $body .= '<div style="margin-bottom:20px;"><img width="150" height="42" alt="' . $config->comp_name . '" src="' . BASE_URL_IMG_DIR . $config->imagepath . '"></div>';        
        $body .= '<h1 style="color:#e84c23;">' . $mrec->em_name . '</h1>';  // add main template body
        $body .= $mrec->em_body;  // add main template body
        $body .= '</div>';
        $body .= $footer;  // any master footer controlled by admin
        $body .= '</div>';
    return $body;
}

function replacePlaceholders(string $body, string $emailto, object $config): string
{
    // EVENT placeholders
    $pattern = '/\{EVENT_\d+_\d+\}/';
    preg_match_all($pattern, $body, $eventplaceholders);
    foreach ($eventplaceholders[0] as $v) {
        $parts    = explode('_', trim($v, '{}'));
        $event    = getRecord('events', 'id', $parts[1]);
        $cat      = getRecord('categories', 'id', $parts[2]);
        $eventurl = NEXTJS_BASE_URL . 'whats-on/' . $cat->cat_name . '/' . $event->slug;
        $html     = '<div style="margin-bottom:10px; padding:10px 0px; border-bottom:1px solid gainsboro;">'
                  . '<a href="' . $eventurl . '">' . $event->title . '</a><br />'
                  . '<img style="padding:10px 0px" height="400" width="400" src="' . BASE_URL_IMG_DIR . $event->imagepath . '"><br />'
                  . $event->summary . '<br/><br/>'
                  . '</div>';
        $body = str_replace($v, $html, $body);
    }

    // PAGE placeholders
    preg_match_all('/\{PAGE_\d+\}/', $body, $pageplaceholders);
    foreach ($pageplaceholders[0] as $v) {
        $parts  = explode('_', trim($v, '{}'));
        $page   = getRecord('pages', 'id', $parts[1]);
        $body   = str_replace($v, '<a href="' . NEXTJS_BASE_URL . $page->pagename . '">' . $page->title . '</a>', $body);
    }

    $body = str_replace('{EMAIL}',    $emailto,          $body);
    $body = str_replace('{COMPNAME}', $config->comp_name, $body);

    return $body;
}