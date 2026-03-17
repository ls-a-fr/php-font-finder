<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;

/**
 * OpenType Bitmap files (OTB)
 *
 * @see https://people.mpi-inf.mpg.de/~uwe/misc/uw-ttyp0/
 */
class OpenTypeBitmap implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        // An OTB file is an OpenType file
        return OpenTypeFont::canExecute($raw);
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        // Delegate to OpenTypeFont decoder
        return OpenTypeFont::extractFontMeta($raw, $filename);
    }
}
