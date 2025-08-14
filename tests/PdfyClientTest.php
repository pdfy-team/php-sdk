<?php

declare(strict_types=1);

namespace Pdfy\Sdk\Tests;

use Pdfy\Sdk\DataObjects\PdfOptions;
use Pdfy\Sdk\Exceptions\PdfyException;
use Pdfy\Sdk\PdfyClient;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PdfyClientTest extends TestCase
{
    private PdfyClient $client;

    protected function setUp(): void
    {
        $this->client = new PdfyClient('test-api-key', 'https://api.test.com');
    }

    #[Test]
    public function client_initialization(): void
    {
        $this->assertEquals('test-api-key', $this->client->getApiKey());
        $this->assertEquals('https://api.test.com', $this->client->getBaseUrl());
    }

    #[Test]
    public function pdf_options_a4_portrait(): void
    {
        $options = PdfOptions::a4Portrait();
        $array = $options->toArray();

        $this->assertEquals('A4', $array['format']);
        $this->assertEquals('portrait', $array['orientation']);
        $this->assertEquals(1.0, $array['margin_top']);
        $this->assertEquals('cm', $array['margin_unit']);
        $this->assertTrue($array['print_background']);
    }

    #[Test]
    public function pdf_options_with_margins(): void
    {
        $options = PdfOptions::withMargins(2.0, 1.5, 2.0, 1.5, 'mm');
        $array = $options->toArray();

        $this->assertEquals(2.0, $array['margin_top']);
        $this->assertEquals(1.5, $array['margin_right']);
        $this->assertEquals(2.0, $array['margin_bottom']);
        $this->assertEquals(1.5, $array['margin_left']);
        $this->assertEquals('mm', $array['margin_unit']);
    }

    #[Test]
    public function pdf_options_no_margins(): void
    {
        $options = PdfOptions::noMargins();
        $array = $options->toArray();

        $this->assertEquals(0.0, $array['margin_top']);
        $this->assertEquals(0.0, $array['margin_right']);
        $this->assertEquals(0.0, $array['margin_bottom']);
        $this->assertEquals(0.0, $array['margin_left']);
        $this->assertEquals('cm', $array['margin_unit']);
    }

    #[Test]
    public function pdfy_exception_methods(): void
    {
        $exception = new PdfyException(
            'Test error',
            402,
            'QUOTA_EXCEEDED',
            ['details' => 'test'],
        );

        $this->assertEquals('Test error', $exception->getMessage());
        $this->assertEquals(402, $exception->getHttpStatus());
        $this->assertEquals('QUOTA_EXCEEDED', $exception->getErrorCode());
        $this->assertEquals(['details' => 'test'], $exception->getErrorData());
        $this->assertTrue($exception->isQuotaExceeded());
        $this->assertFalse($exception->isRateLimited());
    }

    #[Test]
    public function pdfy_exception_user_messages(): void
    {
        $quotaException = new PdfyException('', 402, 'QUOTA_EXCEEDED');
        $this->assertStringContainsString('reached your PDF generation limit', $quotaException->getUserMessage());

        $rateException = new PdfyException('', 429, 'RATE_LIMIT_EXCEEDED');
        $this->assertStringContainsString('making requests too quickly', $rateException->getUserMessage());

        $validationException = new PdfyException('', 400, 'INVALID_HTML');
        $this->assertStringContainsString('HTML content provided is invalid', $validationException->getUserMessage());
    }
}
