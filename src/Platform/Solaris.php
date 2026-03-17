<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Contracts\FontPlatform;

/**
 * Solaris Operating System definition
 */
class Solaris implements FontPlatform
{
    public static function getFontDirectories(): array
    {
        $homeDir = getenv('HOME');

        return [
            // User
            $homeDir.\DIRECTORY_SEPARATOR.'.fonts',
            // Solaris 10 & 11
            '/usr/lib/X11/fonts',
            '/usr/X11/lib/X11/fonts',
            // X11
            '/usr/openwin/lib/X11/fonts',
            // System
            '/usr/share/fonts',
            // X11 R6 legacy
            '/usr/X11R6/lib/X11/fonts',
            // Sun Freeware
            '/opt/sfw/lib/X11/fonts',
        ];
    }

    public static function getSystemInformation(): SystemInformation
    {
        // Note: php_uname('m') should detect i86pc
        return new SystemInformation(SystemInformation::OS_SOLARIS, null, 'amd64');
    }
}
