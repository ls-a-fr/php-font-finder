<?php

namespace Lsa\Font\Finder\Platform;

use RuntimeException;

class SystemInformation {
    public readonly ?string $operatingSystem;
    public readonly ?string $subCategory;
    public readonly ?string $architecture;

    const FORMAT_DEPS = 1;
    
    public function __construct(?string $operatingSystem, ?string $subCategory, ?string $architecture)
    {
        $this->operatingSystem = $operatingSystem;
        $this->subCategory = $subCategory;
        $this->architecture = $architecture;
    }

    public function getValue(int $format)
    {
        switch($format) {
            case self::FORMAT_DEPS:
                return implode('-', array_filter([
                    $this->operatingSystem,
                    $this->subCategory,
                    $this->architecture
                ], fn($s) => $s !== null && $s !== ''));
            default:
                throw new RuntimeException('Invalid format');
        }
    }
}