# Changelog

All notable changes to the Pdfy PHP SDK will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- New error checking helper methods in `PdfyException`:
  - `isChromeError()` - Check for PDF generation service errors
  - `isTimeoutError()` - Check for timeout errors
  - `isMemoryLimitError()` - Check for memory limit exceeded errors
  - `isStorageError()` - Check for storage/file system errors
  - `isPdfNotFound()` - Check for PDF not found errors
  - `isPdfNotReady()` - Check for PDF still processing errors
  - `isJobNotFound()` - Check for job not found errors
  - `isInternalError()` - Check for internal server errors
- New error codes:
  - `JOB_NOT_FOUND` - PDF job not found
  - `INTERNAL_ERROR` - Internal server error
- Comprehensive error handling example in `examples/error-handling.php`
- Retry logic demonstration for transient errors

### Changed
- **BREAKING**: Updated user-friendly error messages to match API improvements:
  - `QUOTA_EXCEEDED`: "Daily PDF limit reached and no credits available."
  - `RATE_LIMIT_EXCEEDED`: "Too many requests. Please wait before trying again."
  - `INVALID_HTML`: "The provided HTML content is invalid or malformed."
  - `CHROME_ERROR`: "PDF generation service is temporarily unavailable. Please try again later."
  - `TIMEOUT_ERROR`: "PDF generation timed out. Please try with simpler content."
  - `MEMORY_LIMIT_EXCEEDED`: "The content is too complex to process. Please simplify and try again."
  - `STORAGE_ERROR`: "Unable to save the generated PDF. Please try again later."
- Updated README.md with new error codes and user-friendly messages
- Updated content limits documentation:
  - HTML size limit increased to 5MB (from 1MB)
  - CSS size limit increased to 2MB (from 512KB)
  - JavaScript size limit increased to 1MB (from 256KB)
- Enhanced basic usage example with comprehensive error handling

### Fixed
- Error messages now provide clear, actionable guidance for users
- Improved error categorization and handling recommendations

## [1.0.0] - 2024-03-15

### Added
- Initial release of Pdfy PHP SDK
- Support for PDF generation from HTML
- Asynchronous and synchronous PDF creation
- Job status checking and PDF downloading
- Comprehensive error handling
- Laravel service provider integration
- Full test coverage
- Documentation and examples

### Features
- `PdfyClient` class for API interactions
- `PdfOptions` data object for PDF configuration
- `PdfyException` with detailed error information
- Support for custom PDF options (format, orientation, margins, etc.)
- Rate limiting and quota management
- Timeout handling for long-running operations

### Supported Operations
- Create PDF jobs from HTML content
- Check job status and progress
- Download completed PDFs
- One-liner PDF creation and download
- Batch operations support
