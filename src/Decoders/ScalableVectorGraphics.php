<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Font;

/**
 * Scalable Vector Graphics (SVG) files.
 * SVG were once used for fonts.
 *
 * @see https://www.w3.org/TR/SVG11/fonts.html
 */
class ScalableVectorGraphics implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        return str_contains($raw, '<font-face') === true;
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        if (preg_match('/<font-face\b([^>]+)>/i', $raw, $m) !== 1) {
            return [];
        }

        $attrs = $m[1];

        // Regular expressions are MUCH faster than XPath
        preg_match('/font-family="([^"]+)"/i', $attrs, $familyMatches);
        preg_match('/font-weight="([^"]+)"/i', $attrs, $weightMatches);
        preg_match('/font-style="([^"]+)"/i', $attrs, $styleMatches);

        $family = ($familyMatches[1] ?? '');

        $weight = 400;
        if (isset($weightMatches[1]) === true) {
            $weight = ((int) $weightMatches[1]);
        }
        $style = strtolower(($styleMatches[1] ?? 'normal'));

        // Fallback 1: use font-face-name
        if ($family === '') {
            preg_match('/font-face-name="([^"]+)"/i', $attrs, $fontFaceNameMatches);
            $family = ($fontFaceNameMatches[1] ?? '');
        }

        // Fallback 2: file name
        if ($family === '') {
            $family = DecoderUtils::getFontNameFromFilePath($filename, 'svg');
        }

        return [
            new Font([
                'filename' => $filename,
                'name' => $family,
                'weight' => $weight,
                'bold' => $weight >= 700,
                'italic' => $style === 'italic',
            ]),
        ];
    }
}
