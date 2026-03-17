<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Font;

/**
 * Jim Hershey Fonts (JHF files)
 *
 * @see https://en.wikipedia.org/wiki/Hershey_fonts
 */
class JimHersheyFont implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        // JHF is ASCII text, first line starts with:
        // <glyph_id> <vertex_count> ...
        // Example: "  1  79  0  0  0  0"
        //
        // Format: optional spaces, integer, spaces, alphanumeric, then end-of-line
        return preg_match('/^\s*\d+\s+[A-Z0-9_\-]+\s*(\r|\n)/', $raw) === 1;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed
    public static function extractFontMeta(string $raw, string $filename): array
    {
        // JHF contains no metadata at all.
        $family = DecoderUtils::getFontNameFromFilePath($filename, 'jhf');

        return [
            new Font([
                'name' => $family,
                'filename' => $filename,
                'weight' => 400,
                'italic' => false,
                'bold' => false,
            ]),
        ];
    }
}
