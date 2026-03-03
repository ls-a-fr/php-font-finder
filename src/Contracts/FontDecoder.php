<?php

namespace Lsa\Font\Finder\Contracts;

use Lsa\Font\Finder\Font;

interface FontDecoder {
    /**
     * @return Font[]
     */
    public static function extractFontMeta(string $raw, string $filename): array;
}