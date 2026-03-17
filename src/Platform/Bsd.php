<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Contracts\FontPlatform;

/**
 * *BSD Operating System definition
 */
class Bsd implements FontPlatform
{
    public static function getFontDirectories(): array
    {
        $homeDir = getenv('HOME');

        return [
            // User
            $homeDir.\DIRECTORY_SEPARATOR.'.fonts',
            $homeDir.\DIRECTORY_SEPARATOR.'.local/share/fonts',
            // Local
            '/usr/local/share/fonts',
            // X11 local
            '/usr/local/lib/X11/fonts',
            // System
            '/usr/share/fonts',
            // X11 R6 legacy
            '/usr/X11R6/lib/X11/fonts',
        ];
    }

    public static function getSystemInformation(): SystemInformation
    {
        $sub = strtolower(php_uname('s'));
        switch ($sub) {
            case 'freebsd':
            case 'openbsd':
                break;
            default:
                // FreeBSD is the most common out there, hopefully this unknown
                // architecture is compatible with FreeBSD.
                \trigger_error('Could not detect BSD system, fallback to freebsd');
                $sub = 'freebsd';
                break;
        }
        $arch = strtolower(php_uname('m'));

        switch ($arch) {
            case 'amd64':
                return new SystemInformation(SystemInformation::OS_BSD, $sub, 'amd64');
            case 'aarch64':
            case 'arm64':
                // FreeBSD: aarch64
                // OpenBSD: arm64
                return new SystemInformation(SystemInformation::OS_BSD, $sub, 'arm64');
            default:
                \trigger_error('Could not detect BSD arch, fallback to amd64');

                // Fallback to amd64
                return new SystemInformation(SystemInformation::OS_BSD, $sub, 'amd64');
        }
    }
}
