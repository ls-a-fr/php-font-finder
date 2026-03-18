<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Exceptions\ConfigurationException;
use Lsa\Font\Finder\Exceptions\RuntimeException;

/**
 * Represents information about current operating system
 */
class SystemInformation
{
    public const OS_WINDOWS = 'windows';

    public const OS_DARWIN = 'darwin';

    public const OS_LINUX = 'linux';

    public const OS_BSD = 'bsd';

    public const OS_SOLARIS = 'solaris';

    public const FORMAT_DEPS = 1;

    /**
     * Operating system name
     */
    public readonly string $operatingSystem;

    /**
     * Operating system subcategory (used for freebsd or openbsd)
     */
    public readonly ?string $subCategory;

    /**
     * Current architecture (amd64, arm64, etc)
     */
    public readonly string $architecture;

    /**
     * Creates a new SystemInformation
     *
     * @param  ?string  $operatingSystem  OperatingSystem
     * @param  ?string  $subCategory  SubCategory
     * @param  ?string  $architecture  Architecture
     */
    public function __construct(string $operatingSystem, ?string $subCategory, string $architecture)
    {
        $this->operatingSystem = $operatingSystem;
        $this->subCategory = $subCategory;
        $this->architecture = $architecture;
    }

    /**
     * Returns SystemInformation as a normalized string.
     * Used in deps/* folder.
     *
     * @param  self::FORMAT_*  $format  Format used. Currently only FORMAT_DEPS is allowed.
     * @return string Normalized string.
     *
     * @throws ConfigurationException In case of invalid format
     */
    public function getValue(int $format): string
    {
        switch ($format) {
            case self::FORMAT_DEPS:
                if($this->operatingSystem === '' || $this->architecture === '') {
                    throw new ConfigurationException('Invalid architecture found');
                }
                return implode('-', array_filter([
                    $this->operatingSystem,
                    $this->subCategory,
                    $this->architecture,
                ], fn ($s) => $s !== null && $s !== ''));
            default:
                throw new ConfigurationException('Invalid format');
        }
    }

    /**
     * Validates if current system is Windows.
     *
     * @return bool True if current system is Windows, false otherwise.
     */
    public function isWindows(): bool
    {
        return $this->operatingSystem == self::OS_WINDOWS;
    }

    /**
     * Validates if current system is macOS.
     *
     * @return bool True if current system is macOS, false otherwise.
     */
    public function isDarwin(): bool
    {
        return $this->operatingSystem == self::OS_DARWIN;
    }

    /**
     * Validates if current system is Linux.
     *
     * @return bool True if current system is Linux, false otherwise.
     */
    public function isLinux(): bool
    {
        return $this->operatingSystem == self::OS_LINUX;
    }

    /**
     * Validates if current system is *BSD.
     *
     * @return bool True if current system is *BSD, false otherwise.
     */
    public function isBsd(): bool
    {
        return $this->operatingSystem == self::OS_BSD;
    }

    /**
     * Validates if current system is Solaris.
     *
     * @return bool True if current system is Solaris, false otherwise.
     */
    public function isSolaris(): bool
    {
        return $this->operatingSystem == self::OS_SOLARIS;
    }

    /**
     * Get current system information.
     *
     * @throws RuntimeException Should never happen, PHP_OS_FAMILY not matched
     */
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
