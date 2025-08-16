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
        return in_array($this->errorCode, [
            'INVALID_HTML',
            'HTML_TOO_LARGE',
            'SECURITY_VIOLATION',
        ]);
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
     * These messages now match the API's user-friendly error responses.
     */
    public function getUserMessage(): string
    {
        return match ($this->errorCode) {
            'QUOTA_EXCEEDED' => 'Daily PDF limit reached and no credits available.',
            'RATE_LIMIT_EXCEEDED' => 'Too many requests. Please wait before trying again.',
            'INVALID_HTML' => 'The provided HTML content is invalid or malformed.',
            'HTML_TOO_LARGE' => 'The HTML content is too large to process.',
            'SECURITY_VIOLATION' => 'The HTML content contains security violations.',
            'CHROME_ERROR' => 'PDF generation service is temporarily unavailable. Please try again later.',
            'TIMEOUT_ERROR' => 'PDF generation timed out. Please try with simpler content.',
            'MEMORY_LIMIT_EXCEEDED' => 'The content is too complex to process. Please simplify and try again.',
            'STORAGE_ERROR' => 'Unable to save the generated PDF. Please try again later.',
            'PDF_NOT_FOUND' => 'The requested PDF could not be found.',
            'PDF_NOT_READY' => 'The PDF is still being generated. Please try again in a moment.',
            'JOB_NOT_FOUND' => 'PDF job not found.',
            'INTERNAL_ERROR' => 'An unexpected error occurred while processing your request. Please try again later.',
            default => $this->getMessage() ?: 'An error occurred while generating your PDF. Please try again later.',
        };
    }

    /**
     * Check if this is a Chrome/PDF generation error.
     */
    public function isChromeError(): bool
    {
        return $this->errorCode === 'CHROME_ERROR';
    }

    /**
     * Check if this is a timeout error.
     */
    public function isTimeoutError(): bool
    {
        return $this->errorCode === 'TIMEOUT_ERROR';
    }

    /**
     * Check if this is a memory limit error.
     */
    public function isMemoryLimitError(): bool
    {
        return $this->errorCode === 'MEMORY_LIMIT_EXCEEDED';
    }

    /**
     * Check if this is a storage error.
     */
    public function isStorageError(): bool
    {
        return $this->errorCode === 'STORAGE_ERROR';
    }

    /**
     * Check if this is a PDF not found error.
     */
    public function isPdfNotFound(): bool
    {
        return $this->errorCode === 'PDF_NOT_FOUND';
    }

    /**
     * Check if this is a PDF not ready error.
     */
    public function isPdfNotReady(): bool
    {
        return $this->errorCode === 'PDF_NOT_READY';
    }

    /**
     * Check if this is a job not found error.
     */
    public function isJobNotFound(): bool
    {
        return $this->errorCode === 'JOB_NOT_FOUND';
    }

    /**
     * Check if this is an internal server error.
     */
    public function isInternalError(): bool
    {
        return $this->errorCode === 'INTERNAL_ERROR' || $this->isServerError();
    }
}
