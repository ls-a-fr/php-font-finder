<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;

/**
 * PostScript Type1 Fonts
 *
 * @see https://adobe-type-tools.github.io/font-tech-notes/pdfs/T1_SPEC.pdf
 */
class Type1 implements FontDecoder
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
