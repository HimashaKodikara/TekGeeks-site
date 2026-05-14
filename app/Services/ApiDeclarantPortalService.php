<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;

class ApiDeclarantPortalService
{
    private const SESSION_TOKEN_KEY = 'backend_api_token';

    protected string $baseUrl;

    /**
     * Constructor for the service.
     * Sets up the base URL and headers configuration.
     */
    public function __construct()
    {
        // 1. Get the base URL from your configuration file
        $this->baseUrl = config('services.ciaboc_backend_api.url');
    }

    /**
     * Creates and returns an HTTP client instance, applying the stored token if available.
     */
    protected function getHttpClient(): PendingRequest
    {
        $client = Http::baseUrl($this->baseUrl)
                      ->withHeaders([
                          'Accept' => 'application/json',
                      ]);

        // Dynamically check for a token in the session and apply it
        if ($token = Session::get(self::SESSION_TOKEN_KEY)) {
            $client->withToken($token);
        }

        return $client;
    }

    /**
     * Handles a GET request to the backend.
     * @param string $endpoint The API path (e.g., 'posts/1').
     * @return array|null The response data or null on failure.
     */
    public function get(string $endpoint, array $query = []): array|null
    {
        try {
            // Use the dynamically tokenized client
            $response = $this->getHttpClient()->get($endpoint, $query);

            $response->throw();

            return $response->json();

        } catch (RequestException $e) {
            logger()->error("Backend API Error on GET $endpoint: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Handles a POST request to the backend.
     * @param string $endpoint The API path (e.g., 'posts').
     * @param array $data The data to send.
     * @return array|null The response data or validation errors.
     */
    public function post(string $endpoint, array $data = []): array|null
    {
        try {
            // Use the dynamically tokenized client
            $response = $this->getHttpClient()->post($endpoint, $data);

            $response->throw();

            return $response->json();

        } catch (RequestException $e) {
            logger()->error("Backend API Error on POST $endpoint: " . $e->getMessage() .
                           " Response body: " . ($e->response?->body() ?? 'N/A'));

            if ($e->response && $e->response->status() === 422) {
                // Return validation errors
                return ['validation_errors' => $e->response->json()];
            }

            return null;
        }
    }
}
