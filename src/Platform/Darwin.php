<?php

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Contracts\FontPlatform;

class Darwin implements FontPlatform {
    public static function getFontDirectories(): array
    {
        $homeDir = getenv('HOME');
        return [
            $homeDir . \DIRECTORY_SEPARATOR . 'Library' . \DIRECTORY_SEPARATOR . 'Fonts', // user
            "/Library/Fonts/", // local
            "/System/Library/Fonts/", // system
            "/Network/Library/Fonts/" // network
        ];
    }
}