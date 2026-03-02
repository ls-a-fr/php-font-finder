<?php

namespace Lsa\Font\Finder\Contracts;

use Lsa\Font\Finder\Platform\SystemInformation;

interface FontPlatform {
    public static function getFontDirectories(): array;
    public static function getSystemInformation(): SystemInformation;
}