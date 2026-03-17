<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Exceptions\InvalidOperationException;
use Lsa\Font\Finder\Font;

/**
 * EmbeddedOpenType fonts
 *
 * @see https://www.w3.org/submissions/2008/SUBM-EOT-20080305/
 */
class EmbeddedOpenType implements FontDecoder
{
    protected const EOT_VERSIONS = [
        0x00010000 => 'EOT v1',
        0x00020001 => 'EOT v2.1',
        0x00020002 => 'EOT v2.2',
    ];

    public static function canExecute(string $raw): bool
    {
        // EOT Header
        // Offset  Size  Description
        // 0       4     EOTSize (total size of the EOT file, little endian)
        // 4       4     FontDataSize (size of the embedded font data)
        // 8       4     Version (EOT version identifier)
        // ...
        // 160     4     Reserved6
        // Note that EOT Header is Little-Endian
        $len = strlen($raw);
        // We'll read in offset 84, so better check it soon
        if ($len < 84) {
            return false;
        }

        // Declared size: EOT declares a file size, that must be equals to the current file size
        $declaredSize = DecoderUtils::unpackInt('V', substr($raw, 0, 4));
        if ($declaredSize !== $len) {
            return false;
        }

        // EOT version
        $version = DecoderUtils::unpackInt('V', substr($raw, 8, 4));
        if (array_key_exists($version, self::EOT_VERSIONS) === false) {
            return false;
        }

        // MagicNumber at offset 34 (unsigned short, little-endian)
        $magic = DecoderUtils::unpackInt('v', substr($raw, 34, 2));
        // 'LP'
        if ($magic !== 0x504C) {
            return false;
        }

        return true;
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        // FontDataSize at offset 4
        $fontDataSize = DecoderUtils::unpackInt('V', substr($raw, 4, 4));

        // Flags at offset 12 (unsigned long)
        $flags = DecoderUtils::unpackInt('V', substr($raw, 12, 4));

        // Italic byte at offset 27 (1 byte)
        $italicByte = ord($raw[27]);

        // Weight at offset 28 (unsigned long)
        $weight = DecoderUtils::unpackInt('V', substr($raw, 28, 4));

        // FamilyNameSize at offset 82
        $familyNameSize = DecoderUtils::unpackInt('v', substr($raw, 82, 2));

        $familyName = '';
        // Check that offset+familyNameSize does not overflow file length
        if ($familyNameSize > 0 && (84 + $familyNameSize) <= strlen($raw)) {
            // Read at offset 84
            $familyName = DecoderUtils::readLittleEndianUtf16String($raw, 84);
        }

        $offset = (84 + $familyNameSize);
        // Padding2 field (2 bytes)
        $offset += 2;

        // StyleName field (variable, skipped)
        $styleNameSize = DecoderUtils::unpackInt('v', substr($raw, $offset, 2));
        $offset += 2;
        $offset += $styleNameSize;

        // Padding3 field (2 bytes, skipped)
        $offset += 2;

        // VersionName field (variable, skipped)
        $versionNameSize = DecoderUtils::unpackInt('v', substr($raw, $offset, 2));
        $offset += 2;
        $offset += $versionNameSize;

        // Padding4 field (2 bytes, skipped)
        $offset += 2;

        // Compute fullname
        $fullNameSize = DecoderUtils::unpackInt('v', substr($raw, $offset, 2));
        $offset += 2;
        $fullName = '';
        if ($fullNameSize > 0 && ($offset + $fullNameSize) <= strlen($raw)) {
            $fullName = DecoderUtils::readLittleEndianUtf16String($raw, $offset);
        }
        $offset += $fullNameSize;

        // There is a 4-byte block after FullName: RootStringSize + padding)
        // SFNT block (TTF/OTF) starts here
        $fontDataOffset = ($offset + 4);

        // Sanity check
        if (($fontDataOffset + $fontDataSize) > strlen($raw)) {
            // Corrupted or truncated
            throw new InvalidOperationException('Corrupted or truncated');
        }

        $fontData = substr($raw, $fontDataOffset, $fontDataSize);

        if (TrueTypeFont::canExecute($fontData) === false) {
            // Fallback based on EOT header
            // It's hard to throw an exception here: EOT file does include font data.
            // Example: sample glyphicons-halflings-regular.eot
            return self::buildFallbackFont($filename, $familyName, $fullName, $weight, $flags, $italicByte);
        }

        // Delegate to TrueTypeFont for extraction if valid font
        return TrueTypeFont::extractFontMeta($fontData, $filename);
    }

    /**
     * Minimal font object based on EOT data
     *
     * @return Font[]
     */
    private static function buildFallbackFont(
        string $filename,
        string $familyName,
        string $fullName,
        int $weight,
        int $flags,
        int $italicByte
        // phpcs:ignore Generic.Functions.OpeningFunctionBraceBsdAllman.BraceOnSameLine
    ): array {
        // Name : FullName > FamilyName > filename
        $name = null;
        if ($fullName !== '') {
            $name = $fullName;
        } elseif ($familyName !== '') {
            $name = $familyName;
        } else {
            $name = DecoderUtils::getFontNameFromFilePath($filename, 'eot');
        }

        // Weight : Fallback 400
        if ($weight === 0) {
            $weight = 400;
        }

        // Italic : dedicated byte or first bit in Flags
        // phpcs:ignore Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
        $italic = ($italicByte !== 0) || (bool) ($flags & 0x0001);

        // Bold : second bit in flags or weight >= 700
        $bold = (bool) ($flags & 0x0002) || $weight >= 700;

        return [
            new Font([
                'filename' => $filename,
                'weight' => $weight,
                'italic' => $italic,
                'bold' => $bold,
                'name' => $name,
            ]),
        ];
    }
}
