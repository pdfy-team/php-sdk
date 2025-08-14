<?php

declare(strict_types=1);

namespace Pdfy\Sdk;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Pdfy\Sdk\Exceptions\PdfyException;
use Pdfy\Sdk\Resources\PdfResource;

class PdfyClient
{
    private HttpFactory $http;

    private string $apiKey;

    private string $baseUrl;

    private int $timeout;

    public function __construct(
        string $apiKey,
        string $baseUrl = 'https://pdfy.app/api/v1',
        int $timeout = 30,
    ) {
        $this->http = new HttpFactory;
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
    }

    /**
     * Get PDF resource for PDF operations.
     */
    public function pdfs(): PdfResource
    {
        return new PdfResource($this);
    }

    /**
     * Make an authenticated HTTP request.
     */
    public function request(): PendingRequest
    {
        return $this->http
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->baseUrl($this->baseUrl);
    }

    /**
     * Make a POST request with HTML content.
     */
    public function postHtml(string $endpoint, string $html, array $headers = []): Response
    {
        return $this->http
            ->withHeaders(array_merge([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'text/html',
            ], $headers))
            ->timeout($this->timeout)
            ->baseUrl($this->baseUrl)
            ->withBody($html, 'text/html')
            ->post($endpoint);
    }

    /**
     * Handle API response and throw exceptions for errors.
     */
    public function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            return $response->json();
        }

        $error = $response->json();
        $message = $error['message'] ?? 'API request failed';
        $errorCode = $error['error_code'] ?? 'UNKNOWN_ERROR';

        throw new PdfyException($message, $response->status(), $errorCode, $error);
    }

    /**
     * Get the base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the API key.
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
