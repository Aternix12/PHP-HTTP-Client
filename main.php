<?php

require_once __DIR__ . '/src/HttpClient.php';

// Initialize HttpClient with the base URL
$baseUrl = 'https://corednacom.corewebdna.com';
$client = new HttpClient($baseUrl);

try {
    // Retrieve Bearer Token
    $bearerToken = $client->sendRequest('OPTIONS', '/assessment-endpoint.php');

    // Set Bearer Token as Authorization Header
    $client->setHeader('Authorization', 'Bearer ' . $bearerToken);

    // Send Submission Data
    $submissionData = [
        'name' => 'Tristan Mitchell',
        'email' => 'tristanmitchell2113@gmail.com',
        'url' => 'https://github.com/Aternix12/PHP-HTTP-Client'
    ];

    $submissionResponse = $client->post('/assessment-endpoint.php', $submissionData);

    // Check if the response is empty or has a specific message
    if (empty($submissionResponse)) {
        echo "No response received from the server.\n";
    } else {
        echo "Submission Response: $submissionResponse\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
