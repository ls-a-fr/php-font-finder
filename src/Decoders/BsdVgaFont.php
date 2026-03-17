<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Exceptions\InvalidOperationException;
use Lsa\Font\Finder\Font;

/**
 * BSD VGA text mode fonts
 *
 * @see https://en.wikipedia.org/wiki/VGA_text_mode#Fonts
 */
class BsdVgaFont implements FontDecoder
{
    public static function canExecute(string $signature): bool
    {
        // VFNT v1 header: ASCII "VFNT0001"
        // VFNT v2 header: ASCII "VFNT0002"
        return strncmp($signature, 'VFNT0002', 8) === 0 ||
            strncmp($signature, 'VFNT0001', 8) === 0;
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        if (strlen($raw) < 16) {
            throw new InvalidOperationException('File too small to be a valid VFNT font');
        }

        // Read width: 1 byte, big endian
        $width = ord($raw[8]);
        if ($width < 1) {
            throw new InvalidOperationException('Invalid VFNT width');
        }

        // Read height: 1 byte, big endian
        $height = ord($raw[9]);
        if ($height < 1) {
            throw new InvalidOperationException('Invalid VFNT height');
        }

        // Skip 2 padding bytes, read glyph count: 4 bytes, big endian
        $glyphCount = DecoderUtils::unpackInt('N', $raw, 12);
        if ($glyphCount < 1 || $glyphCount > 65536) {
            throw new InvalidOperationException('Invalid VFNT glyph count');
        }

        // Family name is not defined in .fnt files. Get family from filename
        $family = DecoderUtils::getFontNameFromFilePath($filename, 'fnt');

        return [
            new Font([
                'filename' => $filename,
                'weight' => 400,
                'italic' => false,
                'bold' => false,
                'name' => $family,
            ]),
        ];
    }
}
