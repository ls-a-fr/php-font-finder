<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Exceptions\InvalidOperationException;
use Lsa\Font\Finder\Font;

/**
 * Pc Screen Font format (PSF)
 *
 * @see https://en.wikipedia.org/wiki/PC_Screen_Font
 */
class PcScreenFont implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        if (
            // Minimal header size for PSF1
            strlen($raw) >= 3 &&
            // PSF1 magic : 0x36 0x04
            $raw[0] === "\x36" &&
            $raw[1] === "\x04"
        ) {
            $mode = ord($raw[2]);

            // Bit 1: Unicode table
            return ($mode & 0x02) === 0;
        }

        if (
            // Minimal header size for PSF2
            strlen($raw) >= 32 &&
            // PSF2 magic : 0x72 0xB5 0x4A 0x86
            $raw[0] === "\x72" &&
            $raw[1] === "\xB5" &&
            $raw[2] === "\x4A" &&
            $raw[3] === "\x86"
        ) {
            // Read headersize (offset 8)
            $headersize = DecoderUtils::unpackInt('V', $raw, 8);

            if (strlen($raw) < $headersize) {
                throw new InvalidOperationException('File seems to be corrupted');
            }

            // Read flags (offset 12)
            $flags = DecoderUtils::unpackInt('V', $raw, 12);

            // If b0 is set to 1, it's an unicode table (PSFU), not a PSF file.
            return ($flags & 0x01) === 0;
        }

        return false;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed
    public static function extractFontMeta(string $raw, string $filename): array
    {
        // PSF files have no style definition.
        $family = DecoderUtils::getFontNameFromFilePath($filename, 'psf');
        $bold = false;
        $italic = false;
        $weight = 400;

        return [
            new Font([
                'filename' => $filename,
                'weight' => $weight,
                'italic' => $italic,
                'bold' => $bold,
                'name' => $family,
            ]),
        ];
    }
}
