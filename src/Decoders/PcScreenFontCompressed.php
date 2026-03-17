<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\ZlibDecoder;
use Lsa\Font\Finder\Exceptions\ZlibException;

/**
 * PcScreenFont, but Gunzipped
 */
class PcScreenFontCompressed implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        try {
            $decoded = ZlibDecoder::decode($raw);

            return PcScreenFont::canExecute($decoded);
        } catch (ZlibException) {
            // If file is not compressed, return false
            return false;
        }
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        $decoded = ZlibDecoder::decode($raw);

        return PcScreenFont::extractFontMeta($decoded, $filename);
    }
}
