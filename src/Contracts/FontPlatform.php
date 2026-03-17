<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Contracts;

use Lsa\Font\Finder\Platform\SystemInformation;

interface FontPlatform
{
    /**
     * List all known font directories in this platform.
     *
     * @return list<non-empty-string> Font directories
     */
    public static function getFontDirectories(): array;

    /**
     * Get system information
     */
    public static function getSystemInformation(): SystemInformation;
}
