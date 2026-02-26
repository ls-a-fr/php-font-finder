<?php

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Contracts\FontPlatform;

class Unix implements FontPlatform {
    public static function getFontDirectories(): array
    {
        $homeDir = getenv('HOME');
        return [
            $homeDir . \DIRECTORY_SEPARATOR . '.fonts', // user
            "/usr/local/fonts", // local
            "/usr/local/share/fonts", // local shared
            "/usr/share/fonts", // system
            "/usr/X11R6/lib/X11/fonts" // X
        ];
    }
}