<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;

/**
 * PostScript fonts (PS files) Type 1
 * This library does not yet support other types
 *
 * @see https://en.wikipedia.org/wiki/PostScript_fonts
 */
class PostScript implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        return PrinterFontAscii::canExecute($raw);
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        return PrinterFontAscii::extractFontMeta($raw, $filename);
    }
}
