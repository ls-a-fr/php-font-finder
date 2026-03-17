<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Contracts\FontPlatform;

/**
 * Linux Operating System definition
 */
class Linux implements FontPlatform
{
    public static function getFontDirectories(): array
    {
        $homeDir = getenv('HOME');

        return [
            // User
            $homeDir.\DIRECTORY_SEPARATOR.'.fonts',
            $homeDir.\DIRECTORY_SEPARATOR.'.local/share/fonts',
            // Local Shared
            '/usr/local/share/fonts',
            // System
            '/usr/share/fonts',
            // X
            '/usr/X11R6/lib/X11/fonts',
        ];
    }

    public static function getSystemInformation(): SystemInformation
    {
        $output = strtolower(php_uname('m'));

        if (\str_contains($output, 'aarch64') === true) {
            return new SystemInformation(SystemInformation::OS_LINUX, null, 'arm64');
        } elseif (\str_contains($output, 'x86_64') === true) {
            return new SystemInformation(SystemInformation::OS_LINUX, null, 'amd64');
        } elseif (\str_contains($output, 'armv7') === true) {
            return new SystemInformation(SystemInformation::OS_LINUX, null, 'armv7');
        }

        \trigger_error('Could not detect architecture, fallback to amd64');

        return new SystemInformation(SystemInformation::OS_LINUX, null, 'amd64');
    }
}
