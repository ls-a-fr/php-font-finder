<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Exceptions\InvalidOperationException;
use Lsa\Font\Finder\Font;

/**
 * BitstreamSpeedo files
 *
 * @see https://en.wikipedia.org/wiki/Bitstream_Speedo_Fonts
 */
class BitstreamSpeedo implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        // Format Identifier: "D1.0" CR LF NUL NUL
        if (strlen($raw) < 8) {
            return false;
        }

        return substr($raw, 0, 8) === "D1.0\r\n\0\0";
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        // Check data was not truncated
        if (strlen($raw) < 266) {
            throw new InvalidOperationException('Invalid speedo file');
        }

        // Full name: offset 24, 70 bytes
        $fullName = self::readNullTerminatedString($raw, 24, 70);
        if ($fullName === '') {
            $fullName = basename($filename);
        }

        // Classification Flags: offset 263 (1 byte)
        $classFlags = ord($raw[263]);

        // Bit 0 = Italic
        $italic = (bool) ($classFlags & 0x01);

        // Font Form Classification: offset 265 (1 byte)
        $form = ord($raw[265]);

        // Bits 4–7: weight class
        $weightClass = (($form >> 4) & 0x0F);

        // Mapping weight class to CSS-like weight
        $weight = self::mapWeightClassToWeight($weightClass);
        // phpcs:ignore Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
        $bold = ($weight >= 700);

        return [
            new Font([
                'filename' => $filename,
                'weight' => $weight,
                'italic' => $italic,
                'bold' => $bold,
                'name' => $fullName,
            ]),
        ];
    }

    /**
     * Utility method to read a string right-padded with \0
     *
     * @param  string  $raw  Binary raw content
     * @param  int  $offset  Start offset
     * @param  int  $length  Length to read until
     */
    private static function readNullTerminatedString(string $raw, int $offset, int $length): string
    {
        if (strlen($raw) <= $offset) {
            return '';
        }

        $chunk = substr($raw, $offset, $length);
        $zeroPos = strpos($chunk, "\0");
        if ($zeroPos !== false) {
            $chunk = substr($chunk, 0, $zeroPos);
        }

        return trim($chunk);
    }

    /**
     * Speedo has 14 weight classes. This method transforms a Speedo weight class to standard CSS weight.
     *
     * @param  int  $weightClass  Integer of weight class
     * @return int CSS Weight
     */
    // Personal opinion: this method is readable, and cyclomatic complexity algorithm is wrong in this case
    // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
    private static function mapWeightClassToWeight(int $weightClass): int
    {
        switch ($weightClass) {
            case 1:
                // 1: Thin
                return 100;
            case 2:
            case 3:
                // 2: Ultralight
                // 3: Extralight
                return 200;
            case 4:
                // 4: Light
                return 300;
            case 5:
            case 6:
                // 5: Book
                // 6: Normal
                return 400;
            case 7:
                // 7: Medium
                return 500;
            case 8:
            case 9:
                // 8: Semibold
                // 9: Demibold
                return 600;
            case 10:
                // 10: Bold
                return 700;
            case 11:
            case 12:
                // 11: Extrabold
                // 12: Ultrabold
                return 800;
            case 13:
            case 14:
                // 13: Heavy
                // 14: Black
                return 900;
            default:
                // 0 or reserved: fallback to Normal
                return 400;
        }
    }
}
