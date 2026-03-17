<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;

/**
 * OpenTypeCollection (OTC) contains multiple OpenType fonts
 *
 * @see https://en.wikipedia.org/wiki/OpenType#Collections
 */
class OpenTypeCollection implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        // An OTC file is a TrueTypeCollection file
        return TrueTypeCollection::canExecute($raw);
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        // Delegate to TrueTypeCollection decoder
        return TrueTypeCollection::extractFontMeta($raw, $filename);
    }
}
