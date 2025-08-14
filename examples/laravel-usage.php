<?php

declare(strict_types=1);

// Example Laravel Controller using the Pdfy SDK

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pdfy\Sdk\DataObjects\PdfOptions;
use Pdfy\Sdk\Exceptions\PdfyException;
use Pdfy\Sdk\Facades\Pdfy;

class InvoiceController extends Controller
{
    /**
     * Generate and download an invoice PDF.
     */
    public function downloadInvoice(Request $request, int $invoiceId)
    {
        try {
            // Get invoice data (example)
            $invoice = $this->getInvoiceData($invoiceId);

            // Generate HTML content
            $html = view('invoices.pdf', compact('invoice'))->render();

            // Create PDF with custom options
            $options = PdfOptions::a4Portrait();

            // Generate PDF and download immediately
            $pdfContent = Pdfy::pdfs()->createAndDownload(
                html: $html,
                filename: "invoice-{$invoiceId}.pdf",
                options: $options,
                maxWaitSeconds: 60,
            );

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"invoice-{$invoiceId}.pdf\"",
            ]);

        } catch (PdfyException $e) {
            if ($e->isQuotaExceeded()) {
                return response()->json([
                    'error' => 'PDF generation quota exceeded',
                    'message' => 'Please upgrade your plan to generate more PDFs',
                ], 402);
            }

            return response()->json([
                'error' => 'PDF generation failed',
                'message' => $e->getUserMessage(),
            ], $e->getHttpStatus());
        }
    }

    /**
     * Generate PDF asynchronously and return job ID.
     */
    public function generateInvoiceAsync(Request $request, int $invoiceId)
    {
        try {
            $invoice = $this->getInvoiceData($invoiceId);
            $html = view('invoices.pdf', compact('invoice'))->render();

            // Create PDF job (async)
            $job = Pdfy::pdfs()->create($html, "invoice-{$invoiceId}.pdf");

            return response()->json([
                'success' => true,
                'job_id' => $job->jobId,
                'status' => $job->status,
                'message' => 'PDF generation started',
            ]);

        } catch (PdfyException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getUserMessage(),
                'error_code' => $e->getErrorCode(),
            ], $e->getHttpStatus());
        }
    }

    /**
     * Check PDF generation status.
     */
    public function checkPdfStatus(string $jobId)
    {
        try {
            $job = Pdfy::pdfs()->status($jobId);

            return response()->json([
                'job_id' => $job->jobId,
                'status' => $job->status,
                'status_label' => $job->getStatusLabel(),
                'is_completed' => $job->isCompleted(),
                'is_failed' => $job->isFailed(),
                'download_url' => $job->isCompleted() ? route('pdf.download', $jobId) : null,
                'error_message' => $job->errorMessage,
            ]);

        } catch (PdfyException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getUserMessage(),
            ], $e->getHttpStatus());
        }
    }

    /**
     * Download completed PDF.
     */
    public function downloadPdf(string $jobId)
    {
        try {
            // Check if PDF is ready
            $job = Pdfy::pdfs()->status($jobId);

            if (! $job->isCompleted()) {
                return response()->json([
                    'error' => 'PDF not ready',
                    'status' => $job->status,
                    'message' => 'PDF is still being generated',
                ], 400);
            }

            // Download PDF
            $pdfContent = Pdfy::pdfs()->download($jobId);

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="document.pdf"',
            ]);

        } catch (PdfyException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getUserMessage(),
            ], $e->getHttpStatus());
        }
    }

    /**
     * Generate PDF using HTML headers API.
     */
    public function generateWithHeaders(Request $request)
    {
        try {
            $html = $request->getContent();

            $headers = [
                'X-PDF-Filename' => 'document.pdf',
                'X-PDF-Format' => 'A4',
                'X-PDF-Orientation' => 'portrait',
                'X-PDF-Margin-Top' => '2.0',
                'X-PDF-Margin-Right' => '1.5',
                'X-PDF-Margin-Bottom' => '2.0',
                'X-PDF-Margin-Left' => '1.5',
                'X-PDF-Margin-Unit' => 'cm',
            ];

            $job = Pdfy::pdfs()->createFromHtml($html, $headers);

            return response()->json([
                'success' => true,
                'job_id' => $job->jobId,
                'status' => $job->status,
            ]);

        } catch (PdfyException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getUserMessage(),
            ], $e->getHttpStatus());
        }
    }

    private function getInvoiceData(int $invoiceId): array
    {
        // Mock invoice data
        return [
            'id' => $invoiceId,
            'number' => "INV-{$invoiceId}",
            'date' => now()->format('Y-m-d'),
            'customer' => 'John Doe',
            'items' => [
                ['description' => 'PDF Generation Service', 'amount' => 29.99],
            ],
            'total' => 29.99,
        ];
    }
}
