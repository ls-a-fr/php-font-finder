<?php

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Contracts\FontPlatform;

class Solaris implements FontPlatform {
    public static function getFontDirectories(): array
    {
        $homeDir = getenv('HOME');
        return [
            $homeDir . \DIRECTORY_SEPARATOR . '.fonts',      // user
            "/usr/openwin/lib/X11/fonts",                    // X11
            "/usr/share/fonts",                              // system
            "/usr/X11R6/lib/X11/fonts",                      // X11 R6 legacy
            "/opt/sfw/lib/X11/fonts"                         // un Freeware
        ];
    }
}