<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Pdfy\Sdk\Exceptions\PdfyException;
use Pdfy\Sdk\PdfyClient;

// Initialize the client
$client = new PdfyClient(
    apiKey: 'your-api-key-here',
    baseUrl: 'https://pdfy.app/api/v1',
    timeout: 30,
);

/**
 * Demonstrate comprehensive error handling with the new user-friendly messages
 */
function demonstrateErrorHandling(PdfyClient $client): void
{
    // Example 1: Invalid HTML content
    echo "=== Testing Invalid HTML ===\n";
    try {
        $invalidHtml = '<html><body><h1>Unclosed tag</body></html>';
        $job = $client->pdfs()->create($invalidHtml, 'invalid.pdf');
        echo "Job created: {$job->jobId}\n";
    } catch (PdfyException $e) {
        handlePdfyException($e);
    }

    // Example 2: HTML content that's too large
    echo "\n=== Testing Large HTML Content ===\n";
    try {
        $largeHtml = '<html><body>' . str_repeat('<p>Large content</p>', 100000) . '</body></html>';
        $job = $client->pdfs()->create($largeHtml, 'large.pdf');
        echo "Job created: {$job->jobId}\n";
    } catch (PdfyException $e) {
        handlePdfyException($e);
    }

    // Example 3: Checking job status for non-existent job
    echo "\n=== Testing Non-existent Job ===\n";
    try {
        $status = $client->pdfs()->status('non-existent-job-id');
        echo "Job status: {$status->status}\n";
    } catch (PdfyException $e) {
        handlePdfyException($e);
    }

    // Example 4: Downloading non-existent PDF
    echo "\n=== Testing Non-existent PDF Download ===\n";
    try {
        $pdfContent = $client->pdfs()->download('non-existent-job-id');
        echo "Downloaded PDF: " . strlen($pdfContent) . " bytes\n";
    } catch (PdfyException $e) {
        handlePdfyException($e);
    }

    // Example 5: Complex HTML that might cause Chrome errors
    echo "\n=== Testing Complex HTML ===\n";
    try {
        $complexHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Complex Document</title>
            <style>
                body { font-family: Arial; }
                .complex { 
                    background: linear-gradient(45deg, red, blue);
                    transform: rotate(45deg);
                    animation: spin 2s infinite;
                }
                @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            </style>
            <script>
                // This JavaScript will be stripped during processing
                console.log("This will be removed");
                function complexFunction() {
                    return new Function("return Math.random()");
                }
            </script>
        </head>
        <body>
            <h1>Complex Document</h1>
            <div class="complex">Complex styling</div>
            <img src="https://external-site.com/image.jpg" alt="External image">
        </body>
        </html>';
        
        $job = $client->pdfs()->create($complexHtml, 'complex.pdf');
        echo "Complex job created: {$job->jobId}\n";
    } catch (PdfyException $e) {
        handlePdfyException($e);
    }
}

/**
 * Comprehensive error handler that demonstrates all the new error checking methods
 */
function handlePdfyException(PdfyException $e): void
{
    echo "❌ Error occurred:\n";
    echo "   Message: {$e->getMessage()}\n";
    echo "   Error Code: {$e->getErrorCode()}\n";
    echo "   HTTP Status: {$e->getHttpStatus()}\n";
    echo "   User-Friendly Message: {$e->getUserMessage()}\n";

    // Categorize the error type
    echo "   Error Type: ";
    
    if ($e->isValidationError()) {
        echo "Validation Error - Check your HTML content\n";
    } elseif ($e->isQuotaExceeded()) {
        echo "Quota Exceeded - Upgrade your plan or wait for reset\n";
    } elseif ($e->isRateLimited()) {
        echo "Rate Limited - Slow down your requests\n";
    } elseif ($e->isChromeError()) {
        echo "Chrome Error - PDF generation service issue\n";
    } elseif ($e->isTimeoutError()) {
        echo "Timeout Error - Content too complex or service overloaded\n";
    } elseif ($e->isMemoryLimitError()) {
        echo "Memory Limit Error - Simplify your content\n";
    } elseif ($e->isStorageError()) {
        echo "Storage Error - Temporary file system issue\n";
    } elseif ($e->isPdfNotFound()) {
        echo "PDF Not Found - Invalid job ID\n";
    } elseif ($e->isPdfNotReady()) {
        echo "PDF Not Ready - Still processing\n";
    } elseif ($e->isJobNotFound()) {
        echo "Job Not Found - Invalid job ID\n";
    } elseif ($e->isInternalError()) {
        echo "Internal Error - Server-side issue\n";
    } elseif ($e->isClientError()) {
        echo "Client Error - Check your request\n";
    } elseif ($e->isServerError()) {
        echo "Server Error - Service issue\n";
    } else {
        echo "Unknown Error Type\n";
    }

    // Provide actionable advice
    echo "   Suggested Action: ";
    if ($e->isValidationError()) {
        echo "Review and fix your HTML content\n";
    } elseif ($e->isQuotaExceeded()) {
        echo "Upgrade your plan or wait for quota reset\n";
    } elseif ($e->isRateLimited()) {
        echo "Wait before making more requests\n";
    } elseif ($e->isChromeError() || $e->isInternalError()) {
        echo "Try again later or contact support if persistent\n";
    } elseif ($e->isTimeoutError() || $e->isMemoryLimitError()) {
        echo "Simplify your HTML content and try again\n";
    } elseif ($e->isPdfNotReady()) {
        echo "Wait a moment and check status again\n";
    } elseif ($e->isPdfNotFound() || $e->isJobNotFound()) {
        echo "Verify the job ID is correct\n";
    } else {
        echo "Check the error message for details\n";
    }
    
    echo "\n";
}

/**
 * Demonstrate retry logic for transient errors
 */
function demonstrateRetryLogic(PdfyClient $client): void
{
    echo "=== Demonstrating Retry Logic ===\n";
    
    $html = '<html><body><h1>Retry Test</h1></body></html>';
    $maxRetries = 3;
    $retryDelay = 2; // seconds
    
    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        try {
            echo "Attempt {$attempt}/{$maxRetries}...\n";
            $job = $client->pdfs()->create($html, 'retry-test.pdf');
            echo "✅ Success! Job created: {$job->jobId}\n";
            break;
        } catch (PdfyException $e) {
            echo "❌ Attempt {$attempt} failed: {$e->getUserMessage()}\n";
            
            // Only retry for certain error types
            if ($e->isChromeError() || $e->isInternalError() || $e->isTimeoutError()) {
                if ($attempt < $maxRetries) {
                    echo "   Retrying in {$retryDelay} seconds...\n";
                    sleep($retryDelay);
                    $retryDelay *= 2; // Exponential backoff
                } else {
                    echo "   Max retries reached. Giving up.\n";
                }
            } else {
                echo "   Error type not suitable for retry. Giving up.\n";
                break;
            }
        }
    }
}

// Run the demonstrations
try {
    echo "🔧 PDF Generation Error Handling Demo\n";
    echo "=====================================\n\n";
    
    demonstrateErrorHandling($client);
    
    echo "\n";
    demonstrateRetryLogic($client);
    
    echo "\n✅ Error handling demonstration completed!\n";
    
} catch (Exception $e) {
    echo "💥 Unexpected error: {$e->getMessage()}\n";
}
