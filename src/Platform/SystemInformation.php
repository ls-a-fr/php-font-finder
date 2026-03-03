<?php

namespace Lsa\Font\Finder\Platform;

use RuntimeException;

class SystemInformation {
    const OS_WINDOWS = 'windows';
    const OS_DARWIN = 'darwin';
    const OS_LINUX = 'linux';
    const OS_BSD = 'bsd';
    const OS_SOLARIS = 'solaris';

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

    public function isWindows(): bool
    {
        return $this->operatingSystem == self::OS_WINDOWS;
    }

    public function isDarwin(): bool
    {
        return $this->operatingSystem == self::OS_DARWIN;
    }

    public function isLinux(): bool
    {
        return $this->operatingSystem == self::OS_LINUX;
    }

    public function isBsd(): bool
    {
        return $this->operatingSystem == self::OS_BSD;
    }

    public function isSolaris(): bool
    {
        return $this->operatingSystem == self::OS_SOLARIS;
    }

    public static function getCurrent(): SystemInformation
    {
        switch (PHP_OS_FAMILY) {
            case 'Windows':
                return Windows::getSystemInformation();
            case 'Darwin':
                return Darwin::getSystemInformation();
            case 'Linux':
                return Linux::getSystemInformation();
            case 'Solaris':
                return Solaris::getSystemInformation();
            case 'BSD':
                return Bsd::getSystemInformation();
            default:
                throw new RuntimeException('Could not detect current system information');
        }
    }
}