<?php

declare(strict_types=1);

namespace Pdfy\Sdk\Exceptions;

use Exception;

class PdfyException extends Exception
{
    private string $errorCode;

    private array $errorData;

    public function __construct(
        string $message,
        int $httpStatus = 0,
        string $errorCode = 'UNKNOWN_ERROR',
        array $errorData = [],
    ) {
        parent::__construct($message, $httpStatus);
        $this->errorCode = $errorCode;
        $this->errorData = $errorData;
    }

    /**
     * Get the error code from the API.
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Get the full error data from the API.
     */
    public function getErrorData(): array
    {
        return $this->errorData;
    }

    /**
     * Get the HTTP status code.
     */
    public function getHttpStatus(): int
    {
        return $this->getCode();
    }

    /**
     * Check if this is a quota exceeded error.
     */
    public function isQuotaExceeded(): bool
    {
        return $this->errorCode === 'QUOTA_EXCEEDED';
    }

    /**
     * Check if this is a rate limit error.
     */
    public function isRateLimited(): bool
    {
        return $this->errorCode === 'RATE_LIMIT_EXCEEDED';
    }

    /**
     * Check if this is a validation error.
     */
    public function isValidationError(): bool
    {
        return in_array($this->errorCode, ['INVALID_HTML', 'HTML_TOO_LARGE', 'SECURITY_VIOLATION']);
    }

    /**
     * Check if this is a server error.
     */
    public function isServerError(): bool
    {
        return $this->getHttpStatus() >= 500;
    }

    /**
     * Get a user-friendly error message.
     */
    public function getUserMessage(): string
    {
        return match ($this->errorCode) {
            'QUOTA_EXCEEDED' => 'You have reached your PDF generation limit. Please upgrade your plan or wait for your quota to reset.',
            'RATE_LIMIT_EXCEEDED' => 'You are making requests too quickly. Please slow down and try again.',
            'INVALID_HTML' => 'The HTML content provided is invalid or malformed.',
            'HTML_TOO_LARGE' => 'The HTML content is too large to process.',
            'SECURITY_VIOLATION' => 'The HTML content contains security violations and cannot be processed.',
            'PDF_NOT_FOUND' => 'The requested PDF could not be found.',
            'PDF_NOT_READY' => 'The PDF is still being generated. Please try again in a moment.',
            'CHROME_ERROR' => 'There was an error generating the PDF. Please try again.',
            'STORAGE_ERROR' => 'There was an error storing the PDF. Please try again.',
            'TIMEOUT_ERROR' => 'The PDF generation timed out. Please try again with simpler content.',
            'MEMORY_LIMIT_EXCEEDED' => 'The PDF generation exceeded memory limits. Please try again with simpler content.',
            default => $this->getMessage(),
        };
    }
}
