<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Pdfy API Key
    |--------------------------------------------------------------------------
    |
    | Your Pdfy API key. You can find this in your Pdfy dashboard.
    |
    */
    'api_key' => env('PDFY_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Pdfy API. Usually you don't need to change this.
    |
    */
    'base_url' => env('PDFY_BASE_URL', 'https://pdfy.app/api/v1'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout for API requests in seconds.
    |
    */
    'timeout' => env('PDFY_TIMEOUT', 30),
];
