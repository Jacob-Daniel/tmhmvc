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
        echo "Error saving token: " . $stmt->error;
        exit;
    }

    $stmt->close();

    echo "Success!";

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