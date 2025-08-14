<?php

declare(strict_types=1);

namespace Pdfy\Sdk\Resources;

use Pdfy\Sdk\DataObjects\PdfJob;
use Pdfy\Sdk\DataObjects\PdfOptions;
use Pdfy\Sdk\PdfyClient;

class PdfResource
{
    private PdfyClient $client;

    public function __construct(PdfyClient $client)
    {
        $this->client = $client;
    }

    /**
     * Generate PDF from HTML content (JSON API).
     */
    public function create(string $html, ?string $filename = null, ?PdfOptions $options = null): PdfJob
    {
        $payload = [
            'html' => $html,
        ];

        if ($filename) {
            $payload['filename'] = $filename;
        }

        if ($options) {
            $payload['options'] = $options->toArray();
        }

        $response = $this->client->request()->post('/pdfs', $payload);
        $data = $this->client->handleResponse($response);

        return PdfJob::fromArray($data['data']);
    }

    /**
     * Generate PDF from HTML content using HTML headers API.
     */
    public function createFromHtml(string $html, array $headers = []): PdfJob
    {
        $response = $this->client->postHtml('/pdfs', $html, $headers);
        $data = $this->client->handleResponse($response);

        return PdfJob::fromArray($data['data']);
    }

    /**
     * Get PDF job status.
     */
    public function status(string $jobId): PdfJob
    {
        $response = $this->client->request()->get("/pdfs/{$jobId}/status");
        $data = $this->client->handleResponse($response);

        return PdfJob::fromArray($data['data']);
    }

    /**
     * Download PDF file.
     */
    public function download(string $jobId): string
    {
        $response = $this->client->request()->get("/pdfs/{$jobId}/download");

        if (! $response->successful()) {
            $this->client->handleResponse($response);
        }

        return $response->body();
    }

    /**
     * Wait for PDF to be ready and return the job.
     */
    public function waitFor(string $jobId, int $maxWaitSeconds = 60, int $pollInterval = 2): PdfJob
    {
        $startTime = time();

        while (time() - $startTime < $maxWaitSeconds) {
            $job = $this->status($jobId);

            if ($job->isCompleted()) {
                return $job;
            }

            if ($job->isFailed()) {
                throw new \Exception("PDF generation failed: {$job->errorMessage}");
            }

            sleep($pollInterval);
        }

        throw new \Exception("PDF generation timed out after {$maxWaitSeconds} seconds");
    }

    /**
     * Generate PDF and wait for completion.
     */
    public function createAndWait(
        string $html,
        ?string $filename = null,
        ?PdfOptions $options = null,
        int $maxWaitSeconds = 60,
    ): PdfJob {
        $job = $this->create($html, $filename, $options);

        return $this->waitFor($job->jobId, $maxWaitSeconds);
    }

    /**
     * Generate PDF, wait for completion, and download.
     */
    public function createAndDownload(
        string $html,
        ?string $filename = null,
        ?PdfOptions $options = null,
        int $maxWaitSeconds = 60,
    ): string {
        $job = $this->createAndWait($html, $filename, $options, $maxWaitSeconds);

        return $this->download($job->jobId);
    }
}
