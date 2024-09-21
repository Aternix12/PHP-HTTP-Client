<?php

require_once __DIR__ . '/src/HttpClient.php';

// Initialize HttpClient with the base URL
$baseUrl = 'https://corednacom.corewebdna.com';
$client = new HttpClient($baseUrl);

try {
    // Step 1: Request Bearer Token
    $optionsResponse = $client->sendRequest('OPTIONS', '/assessment-endpoint.php');

    if (!isset($optionsResponse['token'])) {
        throw new Exception('Failed to retrieve the Bearer token.');
    }

    $bearerToken = $optionsResponse['token'];
    echo "Bearer Token Retrieved: $bearerToken\n";

    // Set Bearer Token as Authorization Header
    $client->setHeader('Authorization', 'Bearer ' . $bearerToken);

    // Step 2: Send Submission Data
    $submissionData = [
        'name' => 'Tristan Mitchell',
        'email' => 'tristanmitchell2113@gmail.com',
        'url' => 'https://github.com/Aternix12/PHP-HTTP-Client'
    ];

    $submissionResponse = $client->post('/assessment-endpoint.php', $submissionData);
    echo "Submission Response: " . json_encode($submissionResponse, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
