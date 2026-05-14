<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class SupportApiService
{

    protected string $baseUrl;
    protected ?string $token = null;

    public function __construct()
    {
        $this->baseUrl = config('services.support_api.url');
    }

    /**
     * Set the Bearer token for the current request cycle.
     */
    public function withToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    // Add this to BackendAPIAssetsBackendService.php
    public function login(array $credentials): ?string
    {
        try {
            $response = Http::baseUrl($this->baseUrl)->post('login', $credentials);
            if ($response->successful()) {
                // Adjust 'token' based on your API response key
                return $response->json()['data']['token'] ?? $response->json()['token'];
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Internal helper to build the HTTP client with headers.
     */
    protected function getHttpClient(): PendingRequest
    {
        if (!$this->token) {
            $this->token = $this->login([
                'username' => config('services.support_api.username'),
                'password' => config('services.support_api.password'),
            ]);
        }

        $client = Http::baseUrl($this->baseUrl)
                      ->withHeaders([
                          'Accept' => 'application/json',
                          'Content-Type' => 'application/json',
                      ]);

        if ($this->token) {
            $client->withToken($this->token);
        }

        return $client;
    }

    /**
     * Perform GET request
     */
    public function get(string $endpoint, array $query = []): array|null
    {
        try {
            $response = $this->getHttpClient()->get($endpoint, $query);
            return $response->throw()->json();
        } catch (RequestException $e) {
            $this->logError('GET', $endpoint, $e);
            return null;
        }
    }

    /**
     * Perform POST request
     */
    public function post(string $endpoint, array $data = []): array|null
    {
        try {
            $response = $this->getHttpClient()->post($endpoint, $data);
            return $response->throw()->json();
        } catch (RequestException $e) {
            if ($e->response?->status() === 422) {
                return ['validation_errors' => $e->response->json()];
            }
            $this->logError('POST', $endpoint, $e);
            return null;
        }
    }

    private function logError(string $method, string $endpoint, RequestException $e): void
    {
        Log::error("API Error [$method] $endpoint: " . $e->getMessage(), [
            'status' => $e->response?->status(),
            'body'   => $e->response?->body()
        ]);
    }
}
