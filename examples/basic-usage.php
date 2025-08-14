<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Pdfy\Sdk\DataObjects\PdfOptions;
use Pdfy\Sdk\Exceptions\PdfyException;
use Pdfy\Sdk\PdfyClient;

// Initialize the client with named arguments
$client = new PdfyClient(
    apiKey: 'your-api-key-here',
    baseUrl: 'https://pdfy.app/api/v1',
    timeout: 30,
);

// Basic HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Test PDF</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #333; }
        .invoice { border: 1px solid #ddd; padding: 20px; }
        .total { font-weight: bold; font-size: 18px; }
    </style>
</head>
<body>
    <h1>Invoice #12345</h1>
    <div class="invoice">
        <p><strong>Date:</strong> '.date('Y-m-d').'</p>
        <p><strong>Customer:</strong> John Doe</p>
        <hr>
        <p>Service: PDF Generation</p>
        <p>Amount: $29.99</p>
        <hr>
        <p class="total">Total: $29.99</p>
    </div>
</body>
</html>
';

try {
    echo "Creating PDF...\n";

    // Example 1: Simple PDF creation
    $job = $client->pdfs()->create($html, 'invoice.pdf');
    echo "Job created: {$job->jobId} (Status: {$job->status})\n";

    // Example 2: Create with custom options
    $options = new PdfOptions(
        format: 'A4',
        orientation: 'portrait',
        marginTop: 2.0,
        marginRight: 1.5,
        marginBottom: 2.0,
        marginLeft: 1.5,
        marginUnit: 'cm',
        printBackground: true,
    );

    $job = $client->pdfs()->create($html, 'invoice-custom.pdf', $options);
    echo "Custom job created: {$job->jobId}\n";

    // Example 3: Create and wait for completion
    echo "Creating PDF and waiting for completion...\n";
    $completedJob = $client->pdfs()->createAndWait($html, 'invoice-completed.pdf', $options, 60);
    echo "PDF completed: {$completedJob->jobId} (Status: {$completedJob->status})\n";

    if ($completedJob->isCompleted()) {
        echo "Downloading PDF...\n";
        $pdfContent = $client->pdfs()->download($completedJob->jobId);
        file_put_contents('invoice-completed.pdf', $pdfContent);
        echo 'PDF saved to invoice-completed.pdf ('.strlen($pdfContent)." bytes)\n";
    }

    // Example 4: One-liner - create, wait, and download (with named arguments)
    echo "Creating PDF with one-liner...\n";
    $pdfContent = $client->pdfs()->createAndDownload(
        html: $html,
        filename: 'invoice-oneliner.pdf',
        maxWaitSeconds: 120,
    );
    file_put_contents('invoice-oneliner.pdf', $pdfContent);
    echo 'PDF saved to invoice-oneliner.pdf ('.strlen($pdfContent)." bytes)\n";

} catch (PdfyException $e) {
    echo 'Error: '.$e->getMessage()."\n";
    echo 'Error Code: '.$e->getErrorCode()."\n";
    echo 'HTTP Status: '.$e->getHttpStatus()."\n";
    echo 'User Message: '.$e->getUserMessage()."\n";

    if ($e->isQuotaExceeded()) {
        echo "You've reached your quota limit!\n";
    } elseif ($e->isRateLimited()) {
        echo "You're being rate limited!\n";
    }
} catch (Exception $e) {
    echo 'Unexpected error: '.$e->getMessage()."\n";
}
