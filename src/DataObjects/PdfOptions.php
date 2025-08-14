<?php

declare(strict_types=1);

namespace Pdfy\Sdk\DataObjects;

readonly class PdfOptions
{
    public function __construct(
        public ?string $format = null,
        public ?string $orientation = null,
        public ?float $marginTop = null,
        public ?float $marginRight = null,
        public ?float $marginBottom = null,
        public ?float $marginLeft = null,
        public ?string $marginUnit = null,
        public ?bool $printBackground = null,
        public ?bool $displayHeaderFooter = null,
        public ?bool $preferCssPageSize = null,
    ) {}

    /**
     * Create with A4 portrait defaults.
     */
    public static function a4Portrait(): self
    {
        return new self(
            format: 'A4',
            orientation: 'portrait',
            marginTop: 1.0,
            marginRight: 1.0,
            marginBottom: 1.0,
            marginLeft: 1.0,
            marginUnit: 'cm',
            printBackground: true,
        );
    }

    /**
     * Create with A4 landscape defaults.
     */
    public static function a4Landscape(): self
    {
        return new self(
            format: 'A4',
            orientation: 'landscape',
            marginTop: 1.0,
            marginRight: 1.0,
            marginBottom: 1.0,
            marginLeft: 1.0,
            marginUnit: 'cm',
            printBackground: true,
        );
    }

    /**
     * Create with custom margins.
     */
    public static function withMargins(
        float $top,
        float $right,
        float $bottom,
        float $left,
        string $unit = 'cm',
    ): self {
        return new self(
            marginTop: $top,
            marginRight: $right,
            marginBottom: $bottom,
            marginLeft: $left,
            marginUnit: $unit,
        );
    }

    /**
     * Create with no margins.
     */
    public static function noMargins(): self
    {
        return new self(
            marginTop: 0.0,
            marginRight: 0.0,
            marginBottom: 0.0,
            marginLeft: 0.0,
            marginUnit: 'cm',
        );
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        return array_filter([
            'format' => $this->format,
            'orientation' => $this->orientation,
            'margin_top' => $this->marginTop,
            'margin_right' => $this->marginRight,
            'margin_bottom' => $this->marginBottom,
            'margin_left' => $this->marginLeft,
            'margin_unit' => $this->marginUnit,
            'print_background' => $this->printBackground,
            'display_header_footer' => $this->displayHeaderFooter,
            'prefer_css_page_size' => $this->preferCssPageSize,
        ], fn ($value) => $value !== null);
    }
}
