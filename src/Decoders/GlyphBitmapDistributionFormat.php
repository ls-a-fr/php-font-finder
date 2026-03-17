<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Font;

/**
 * Glyph Bitmap Distribution Format decoder (BDF files).
 *
 * @see https://en.wikipedia.org/wiki/Glyph_Bitmap_Distribution_Format
 */
class GlyphBitmapDistributionFormat implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        return substr($raw, 0, 9) === 'STARTFONT';
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        // Sanitize raw content for regular expressions
        $raw = str_replace("\r", "\n", $raw);
        $family = null;
        $weight = null;
        $italic = null;
        $bold = null;

        if (preg_match('/^FONT\s+(.+)$/m', $raw, $m) === 1) {
            $value = trim($m[1]);
            [$family, $weight, $italic, $bold] = DecoderUtils::parseXlfd($value);
        }

        // Fallback: check WEIGHT_NAME if FONT is not XLFD
        if ($weight === null && preg_match('/^WEIGHT_NAME\s+(.+)$/m', $raw, $m) === 1) {
            $value = strtolower(trim($m[1]));
            if (isset(DecoderUtils::$weightMap[$value]) === true) {
                $weight = DecoderUtils::$weightMap[$value];
                // phpcs:disable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
                $bold = ($weight >= 700);
            }
        }

        // Fallback: check SLANT if FONT is not XLFD
        if ($italic === null && preg_match('/^SLANT\s+(.+)$/m', $raw, $m) === 1) {
            $value = strtoupper(trim($m[1]));
            if ($value === 'I' || $value === 'O') {
                $italic = true;
            }
        }

        // Fallback: infer font name from file path if nothing found
        if ($family === null) {
            $family = DecoderUtils::getFontNameFromFilePath($filename, 'bdf');
        }

        return [
            new Font([
                'name' => $family,
                'filename' => $filename,
                'weight' => ($weight ?? 400),
                'italic' => (bool) $italic,
                'bold' => (bool) $bold,
            ]),
        ];
    }
}
