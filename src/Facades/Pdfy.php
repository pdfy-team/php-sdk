<?php

declare(strict_types=1);

namespace Pdfy\Sdk\Facades;

use Illuminate\Support\Facades\Facade;
use Pdfy\Sdk\PdfyClient;

/**
 * @method static \Pdfy\Sdk\Resources\PdfResource pdfs()
 * @method static \Illuminate\Http\Client\PendingRequest request()
 * @method static \Illuminate\Http\Client\Response postHtml(string $endpoint, string $html, array $headers = [])
 * @method static array handleResponse(\Illuminate\Http\Client\Response $response)
 * @method static string getBaseUrl()
 * @method static string getApiKey()
 *
 * @see \Pdfy\Sdk\PdfyClient
 */
class Pdfy extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return PdfyClient::class;
    }
}
