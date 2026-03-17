<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;

/**
 * OpenTypeFont files (OTF)
 *
 * @see https://en.wikipedia.org/wiki/OpenType
 */
class OpenTypeFont implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        // An OTF file is a TrueTypeFont file
        return TrueTypeFont::canExecute($raw);
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        // Delegate to TrueTypeFont decoder
        return TrueTypeFont::extractFontMeta($raw, $filename);
    }
}
