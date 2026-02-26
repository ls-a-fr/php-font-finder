<?php

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Contracts\FontPlatform;

class Bsd implements FontPlatform {
    public static function getFontDirectories(): array
    {
        $homeDir = getenv('HOME');
        return [
            $homeDir . \DIRECTORY_SEPARATOR . '.fonts',      // user
            "/usr/local/share/fonts",                        // local
            "/usr/local/lib/X11/fonts",                      // X11 local
            "/usr/share/fonts",                              // system
            "/usr/X11R6/lib/X11/fonts"                       // X11 R6 legacy
        ];
    }
}