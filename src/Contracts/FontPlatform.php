<?php

namespace Lsa\Font\Finder\Contracts;

interface FontPlatform {
    public static function getFontDirectories(): array;
}