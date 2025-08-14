# Pdfy PHP SDK

A modern PHP SDK for the Pdfy.app PDF Generation API, built with Laravel's HTTP Client.

## Requirements

- PHP 8.4+
- Laravel 11+ or 12+ (for Laravel integration)

## Installation

Install the package via Composer:

```bash
composer require pdfy/php-sdk
```

## Features

- ✅ **Modern PHP 8.4+** - Uses latest PHP features (strict types, readonly classes, attributes)
- ✅ **Laravel 12 Ready** - Full Laravel 11+ and 12+ support with auto-discovery
- ✅ **Type Safe** - Comprehensive type hints and strict typing
- ✅ **Async & Sync** - Support for both asynchronous and synchronous workflows
- ✅ **Rich Error Handling** - Detailed exceptions with user-friendly messages
- ✅ **Multiple APIs** - Support for both JSON and HTML headers APIs
- ✅ **Framework Agnostic** - Works with any PHP project
- ✅ **Well Tested** - Comprehensive PHPUnit test suite
- ✅ **PSR Compliant** - Follows PHP standards and best practices

## Laravel Integration

If you're using Laravel, the package will auto-register. Publish the config file:

```bash
php artisan vendor:publish --tag=pdfy-config
```

Add your API key to your `.env` file:

```env
PDFY_API_KEY=your_api_key_here
```

Override the `BASE_URL` if you need:

```env
PDFY_BASE_URL=https://pdfy.app/api/v1
```

You can set the timeout for how long the synchronous "create and download" method should wait for the PDF to be generated:

```env
PDFY_TIMEOUT=30
```

## Usage

### Standalone Usage (without Laravel)

```php
use Pdfy\Sdk\PdfyClient;
use Pdfy\Sdk\DataObjects\PdfOptions;

$client = new PdfyClient('your_api_key_here');

// Simple PDF generation
$html = '<h1>Hello World</h1><p>This is a test PDF.</p>';
$job = $client->pdfs()->create($html, 'test.pdf');

echo "Job ID: " . $job->jobId . "\n";
echo "Status: " . $job->status . "\n";

// Wait for completion and download
$pdfContent = $client->pdfs()->createAndDownload($html, 'test.pdf');
file_put_contents('test.pdf', $pdfContent);
```

### Laravel Usage (with Facade)

```php
use Pdfy\Sdk\Facades\Pdfy;
use Pdfy\Sdk\DataObjects\PdfOptions;

// Simple PDF generation
$html = '<h1>Hello World</h1><p>This is a test PDF.</p>';
$job = Pdfy::pdfs()->create($html, 'test.pdf');

// With custom options
$options = PdfOptions::a4Portrait();
$job = Pdfy::pdfs()->create($html, 'report.pdf', $options);

// Generate and download immediately
$pdfContent = Pdfy::pdfs()->createAndDownload($html, 'invoice.pdf');
return response($pdfContent, 200, [
    'Content-Type' => 'application/pdf',
    'Content-Disposition' => 'attachment; filename="invoice.pdf"',
]);
```

### Advanced Usage

```php
use Pdfy\Sdk\DataObjects\PdfOptions;

// Custom PDF options
$options = new PdfOptions(
    format: 'A4',
    orientation: 'landscape',
    marginTop: 2.0,
    marginRight: 1.5,
    marginBottom: 2.0,
    marginLeft: 1.5,
    marginUnit: 'cm',
    printBackground: true
);

// Create PDF with options
$job = $client->pdfs()->create($html, 'landscape.pdf', $options);

// Check status
$job = $client->pdfs()->status($job->jobId);
if ($job->isCompleted()) {
    $pdfContent = $client->pdfs()->download($job->jobId);
    // Save or return PDF
}

// Wait for completion with custom timeout
$job = $client->pdfs()->waitFor($job->jobId, maxWaitSeconds: 120);
```

### HTML Headers API

```php
// Using HTML content-type with headers
$html = '<h1>Invoice #12345</h1><p>Amount: $100.00</p>';

$headers = [
    'X-PDF-Filename' => 'invoice-12345.pdf',
    'X-PDF-Format' => 'A4',
    'X-PDF-Orientation' => 'portrait',
    'X-PDF-Margin-Top' => '2.0',
    'X-PDF-Margin-Right' => '1.5',
    'X-PDF-Margin-Bottom' => '2.0',
    'X-PDF-Margin-Left' => '1.5',
    'X-PDF-Margin-Unit' => 'cm',
];

$job = $client->pdfs()->createFromHtml($html, $headers);
```

### Error Handling

```php
use Pdfy\Sdk\Exceptions\PdfyException;

try {
    $job = $client->pdfs()->create($html, 'test.pdf');
    $pdfContent = $client->pdfs()->createAndDownload($html);
} catch (PdfyException $e) {
    if ($e->isQuotaExceeded()) {
        echo "Quota exceeded: " . $e->getUserMessage();
    } elseif ($e->isRateLimited()) {
        echo "Rate limited: " . $e->getUserMessage();
    } else {
        echo "Error: " . $e->getMessage();
        echo "Error Code: " . $e->getErrorCode();
        echo "HTTP Status: " . $e->getHttpStatus();
    }
}
```

## API Reference

### PdfyClient

- `pdfs()` - Get PDF resource for operations
- `request()` - Get authenticated HTTP client
- `postHtml()` - Send HTML content with headers

### PdfResource

- `create(string $html, ?string $filename, ?PdfOptions $options)` - Create PDF job
- `createFromHtml(string $html, array $headers)` - Create PDF using HTML headers API
- `status(string $jobId)` - Get job status
- `download(string $jobId)` - Download PDF content
- `waitFor(string $jobId, int $maxWaitSeconds)` - Wait for job completion
- `createAndWait()` - Create and wait for completion
- `createAndDownload()` - Create, wait, and download

### PdfOptions

- `PdfOptions::a4Portrait()` - A4 portrait with 1cm margins
- `PdfOptions::a4Landscape()` - A4 landscape with 1cm margins
- `PdfOptions::withMargins()` - Custom margins
- `PdfOptions::noMargins()` - No margins

### PdfJob

- `isCompleted()` - Check if job is completed
- `isFailed()` - Check if job failed
- `isProcessing()` - Check if job is still processing
- `getStatusLabel()` - Get human-readable status

## Development

### Running Tests

```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test
vendor/bin/phpunit --filter=client_initialization
```

### Code Style

```bash
# Format code
composer format

# Check code style
composer format-test
```

### Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run tests (`composer test`)
5. Format code (`composer format`)
6. Commit your changes (`git commit -am 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

## License

MIT License
