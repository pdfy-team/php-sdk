<?php

declare(strict_types=1);

namespace Pdfy\Sdk\DataObjects;

readonly class PdfJob
{
    public function __construct(
        public string $jobId,
        public string $status,
        public ?string $message = null,
        public ?string $fileName = null,
        public ?int $fileSize = null,
        public ?string $downloadUrl = null,
        public ?string $errorMessage = null,
        public ?string $errorCode = null,
        public ?string $createdAt = null,
        public ?string $completedAt = null,
        public ?string $failedAt = null,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            jobId: $data['job_id'],
            status: $data['status'],
            message: $data['message'] ?? null,
            fileName: $data['filename'] ?? null,
            fileSize: $data['file_size'] ?? null,
            downloadUrl: $data['download_url'] ?? null,
            errorMessage: $data['error_message'] ?? null,
            errorCode: $data['error_code'] ?? null,
            createdAt: $data['created_at'] ?? null,
            completedAt: $data['completed_at'] ?? null,
            failedAt: $data['failed_at'] ?? null,
        );
    }

    /**
     * Check if the job is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the job failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the job is still processing.
     */
    public function isProcessing(): bool
    {
        return in_array($this->status, ['pending', 'queued', 'processing']);
    }

    /**
     * Get status label.
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'queued' => 'Queued',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            default => ucfirst($this->status),
        };
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'job_id' => $this->jobId,
            'status' => $this->status,
            'message' => $this->message,
            'file_path' => $this->fileName,
            'file_size' => $this->fileSize,
            'download_url' => $this->downloadUrl,
            'error_message' => $this->errorMessage,
            'error_code' => $this->errorCode,
            'created_at' => $this->createdAt,
            'completed_at' => $this->completedAt,
            'failed_at' => $this->failedAt,
        ];
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }
}
