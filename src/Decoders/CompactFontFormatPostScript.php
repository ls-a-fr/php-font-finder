<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Exceptions\InvalidOperationException;
use Lsa\Font\Finder\Font;

/**
 * Some CFF fonts are distributed with PostScript header
 *
 * @see https://adobe-type-tools.github.io/font-tech-notes/pdfs/5176.CFF.pdf
 */
class CompactFontFormatPostScript implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        if (
            str_starts_with($raw, '%!PS-Adobe-3.0 Resource-FontSet') === false
            && str_starts_with($raw, '%!PS-Adobe-3.0 Resource-Font') === false
            && str_starts_with($raw, '%!PS-Adobe-3.0 Resource-CIDFont') === false
        ) {
            return false;
        }

        return self::lookupFontOffset($raw) !== null;
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        $fonts = [];

        // Find any lines with this pattern: /FontName <size> StartData
        // Get font name and binary block size
        // Note: not sure StartData is always present in fonts. We may need to scan byte-by-byte to detect
        // a CFF header candidate. It's hard to find enough CFF samples to check everything.
        $match = preg_match_all('/\/([A-Za-z0-9._-]+)\s+(\d+)\s+StartData/', $raw, $matches, PREG_OFFSET_CAPTURE);
        if ($match === false) {
            throw new InvalidOperationException('No StartData blocks found in PostScript wrapper');
        }

        $countMatches = \count($matches[0]);
        for ($i = 0; $i < $countMatches; $i++) {
            $dataSize = (int) $matches[2][$i][0];
            $startPos = (int) $matches[0][$i][1];

            // Find binary data start from StartData.
            // Skip newline, binary is right here
            $binaryStart = strpos($raw, "\n", $startPos);
            if ($binaryStart === false) {
                continue;
            }
            // Skip \n
            $binaryStart++;

            // Extract <dataSize> bytes
            $cffData = substr($raw, $binaryStart, $dataSize);

            if ($cffData === '' || strlen($cffData) < 4) {
                continue;
            }

            // Check this data is a CFF embedded font
            if (CompactFontFormat::canExecute($cffData) === false) {
                continue;
            }

            $newFonts = CompactFontFormat::extractFontMeta($cffData, $filename);

            // Update name if necessary
            $newFonts = array_map(function ($font) use ($raw, $filename) {
                $name = self::refineFontNameFromTitle($raw, $font->name, $filename);
                if ($name === $font->name) {
                    return $font;
                }

                return new Font([
                    'filename' => $font->filename,
                    'weight' => $font->weight,
                    'italic' => $font->italic,
                    'bold' => $font->bold,
                    'name' => $name,
                ]);
            }, $newFonts);

            $fonts = array_merge($fonts, $newFonts);
        }

        return $fonts;
    }

    /**
     * Find font offset. When CFF is embedded in PostScript, we need to search first binary characters.
     *
     * @param  string  $raw  Raw binary content
     * @return ?int Font offset if found, null otherwise.
     */
    protected static function lookupFontOffset(string $raw): ?int
    {
        $len = strlen($raw);

        // Find first non-ASCII byte. PostScript only uses 0x20 to 0x7E, plus \n\r\t.
        for ($i = 0; $i < $len; $i++) {
            $c = ord($raw[$i]);

            // Personal opinion: code is more readable with an $isAscii variable
            // phpcs:disable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
            $isAscii
                = ($c >= 0x20 && $c <= 0x7E)
                || $c === 0x0A
                || $c === 0x0D
                || $c === 0x09;
            // phpcs:enable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison

            if ($isAscii === false) {
                // Found binary data.
                // If CFF format, return this position
                if (CompactFontFormat::canExecute(substr($raw, $i)) === true) {
                    return $i;
                }

                // Binary data found, but it is not a CFF.
                // Stop here
                return null;
            }
        }

        return null;
    }

    /**
     * Refines a name based on title. PostScript can contain font name before CFF contents, and this
     * title directive may be present while name in CFF may be absent.
     *
     * @param  string  $raw  Raw binary content
     * @param  string  $currentName  Current CFF name
     * @param  string  $filename  Current filename
     * @return string New name if found, same name otherwise.
     */
    private static function refineFontNameFromTitle(
        string $raw,
        string $currentName,
        string $filename
        // phpcs:ignore Generic.Functions.OpeningFunctionBraceBsdAllman.BraceOnSameLine
    ): string {
        // Expected name from filename
        $fileNameGuess = DecoderUtils::getFontNameFromFilePath($filename, 'cff');

        // If CFF found a better name, keep it
        if ($currentName !== $fileNameGuess) {
            return $currentName;
        }

        // Extract Title from PostScript wrapper
        // Example: %%Title: (FontSet/Garamontio Bold)
        if (preg_match('/^%%Title:\s*\((.*?)\)/m', $raw, $m) !== 1) {
            return $currentName;
        }

        $title = trim($m[1]);

        // Cleanup title: remove anything before slash character
        if (str_contains($title, '/') === true) {
            $parts = explode('/', $title);
            $title = trim(end($parts));
        }

        // If no title found, stop here
        if ($title === '' || $title === $currentName) {
            return $currentName;
        }

        return $title;
    }
}
