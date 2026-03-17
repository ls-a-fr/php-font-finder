<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\ZlibDecoder;
use Lsa\Font\Finder\Exceptions\ZlibException;

/**
 * Scalable Vector Graphics (SVG) files, gunzipped.
 * SVG were once used for fonts.
 *
 * @see https://www.w3.org/TR/SVG11/fonts.html
 */
class ScalableVectorGraphicsCompressed implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        try {
            $decoded = ZlibDecoder::decode($raw);

            return ScalableVectorGraphics::canExecute($decoded);
        } catch (ZlibException) {
            return false;
        }
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        $decoded = ZlibDecoder::decode($raw);

        return ScalableVectorGraphics::extractFontMeta($decoded, $filename);
    }
}
