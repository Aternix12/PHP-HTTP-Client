<?php

/**
 * Lightweight HTTP client to handle basic HTTP requests and JSON payloads.
 */
class HttpClient
{
    /**
     * Base URL for the HTTP requests.
     *
     * @var string
     */
    private $baseUrl;

    /**
     * Array of HTTP headers to send with the request.
     *
     * @var array
     */
    private $headers = [];

    /**
     * Constructor to initialize the base URL.
     *
     * @param string $baseUrl Base URL for the requests.
     */
    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Set a custom HTTP header.
     *
     * @param string $name Header name.
     * @param string $value Header value.
     */
    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    /**
     * Send a GET request to the specified endpoint.
     *
     * @param string $endpoint
     * @return array
     * @throws Exception
     */
    public function get(string $endpoint): array
    {
        return $this->sendRequest('GET', $endpoint);
    }

    /**
     * Send a POST request to the specified endpoint with a JSON payload.
     *
     * @param string $endpoint
     * @param array $payload
     * @return array
     * @throws Exception
     */
    public function post(string $endpoint, array $payload): array
    {
        return $this->sendRequest('POST', $endpoint, $payload);
    }


    /**
     * Public method to send HTTP requests.
     *
     * @param string $method HTTP method (GET, POST, etc.).
     * @param string $endpoint API endpoint.
     * @param array|null $payload JSON payload.
     * @return array
     * @throws Exception
     */
    public function sendRequest(string $method, string $endpoint, array $payload = null): array
    {
        return $this->send($method, $endpoint, $payload);
    }

    /**
     * Core method to send HTTP requests (Renamed to avoid name conflict).
     *
     * @param string $method HTTP method (GET, POST, etc.).
     * @param string $endpoint API endpoint.
     * @param array|null $payload JSON payload.
     * @return array
     * @throws Exception
     */
    private function send(string $method, string $endpoint, array $payload = null): array
    {
        // Initialize the HTTP context options
        $options = [
            'http' => [
                'method' => $method,
                'header' => $this->formatHeaders(),
                'ignore_errors' => true // Capture error messages in response
            ]
        ];

        if ($payload) {
            $jsonPayload = json_encode($payload);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON payload: ' . json_last_error_msg());
            }
            $options['http']['content'] = $jsonPayload;
            $this->setHeader('Content-Type', 'application/json');
        }

        $context = stream_context_create($options);
        $url = $this->baseUrl . $endpoint;
        $response = file_get_contents($url, false, $context);

        // Check for HTTP errors
        $httpCode = $this->getHttpResponseCode($http_response_header);
        if ($httpCode >= 400) {
            throw new Exception("HTTP error $httpCode: $response");
        }

        return $this->parseJson($response);
    }

    /**
     * Format headers into a string format for HTTP context.
     *
     * @return string
     */
    private function formatHeaders(): string
    {
        $headers = [];
        foreach ($this->headers as $name => $value) {
            $headers[] = "$name: $value";
        }
        return implode("\r\n", $headers);
    }

    /**
     * Parse the JSON response.
     *
     * @param string $response
     * @return array
     * @throws Exception
     */
    private function parseJson(string $response): array
    {
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response: ' . json_last_error_msg());
        }
        return $data;
    }

    /**
     * Retrieve the HTTP response code from headers.
     *
     * @param array $headers
     * @return int
     */
    private function getHttpResponseCode(array $headers): int
    {
        if (isset($headers[0]) && preg_match('/HTTP\/\d\.\d\s+(\d+)/', $headers[0], $matches)) {
            return (int)$matches[1];
        }
        return 0;
    }
}
