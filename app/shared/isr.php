<?php
declare(strict_types=1);

function revalidateISR(array $paths, string $secret, string $baseUrl): array
{
    $responses = [];

    foreach ($paths as $path) {
        $url = $baseUrl . '/api/revalidate'
             . '?path='   . urlencode($path)
             . '&secret=' . urlencode($secret);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_NOBODY         => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_SSL_VERIFYPEER => true,   // false only in dev
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $result = curl_exec($ch);
        $error  = curl_errno($ch) ? curl_error($ch) : null;
        curl_close($ch);

        $responses[] = $error
            ? ['error' => true,  'message' => $error]
            : ['error' => false, 'message' => $result];
    }

    // Return first error, or last success
    foreach ($responses as $r) {
        if ($r['error']) return $r;
    }

    return end($responses);
}