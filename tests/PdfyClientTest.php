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
        $this->assertStringContainsString('Daily PDF limit reached and no credits available', $quotaException->getUserMessage());

        $rateException = new PdfyException('', 429, 'RATE_LIMIT_EXCEEDED');
        $this->assertStringContainsString('Too many requests. Please wait before trying again', $rateException->getUserMessage());

        $validationException = new PdfyException('', 400, 'INVALID_HTML');
        $this->assertStringContainsString('The provided HTML content is invalid or malformed', $validationException->getUserMessage());

        $chromeException = new PdfyException('', 500, 'CHROME_ERROR');
        $this->assertStringContainsString('PDF generation service is temporarily unavailable', $chromeException->getUserMessage());

        $timeoutException = new PdfyException('', 408, 'TIMEOUT_ERROR');
        $this->assertStringContainsString('PDF generation timed out. Please try with simpler content', $timeoutException->getUserMessage());
    }

    #[Test]
    public function pdfy_exception_helper_methods(): void
    {
        $chromeException = new PdfyException('', 500, 'CHROME_ERROR');
        $this->assertTrue($chromeException->isChromeError());
        $this->assertFalse($chromeException->isTimeoutError());

        $timeoutException = new PdfyException('', 408, 'TIMEOUT_ERROR');
        $this->assertTrue($timeoutException->isTimeoutError());
        $this->assertFalse($timeoutException->isChromeError());

        $memoryException = new PdfyException('', 507, 'MEMORY_LIMIT_EXCEEDED');
        $this->assertTrue($memoryException->isMemoryLimitError());
        $this->assertTrue($memoryException->isServerError());

        $storageException = new PdfyException('', 500, 'STORAGE_ERROR');
        $this->assertTrue($storageException->isStorageError());

        $notFoundException = new PdfyException('', 404, 'PDF_NOT_FOUND');
        $this->assertTrue($notFoundException->isPdfNotFound());

        $notReadyException = new PdfyException('', 202, 'PDF_NOT_READY');
        $this->assertTrue($notReadyException->isPdfNotReady());

        $jobNotFoundException = new PdfyException('', 404, 'JOB_NOT_FOUND');
        $this->assertTrue($jobNotFoundException->isJobNotFound());

        $internalException = new PdfyException('', 500, 'INTERNAL_ERROR');
        $this->assertTrue($internalException->isInternalError());
        $this->assertTrue($internalException->isServerError());
    }
}
