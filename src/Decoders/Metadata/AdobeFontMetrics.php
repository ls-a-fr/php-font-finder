<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders\Metadata;

use Lsa\Font\Finder\Contracts\MetadataParser;

/**
 * AdobeFontMetrics contains font metric information.
 *
 * @see https://adobe-type-tools.github.io/font-tech-notes/pdfs/5004.AFM_Spec.pdf
 */
class AdobeFontMetrics implements MetadataParser
{
    /**
     * An AFM file will contain weight as string, not integer.
     * This map allows to infer an integer based on found string.
     *
     * @var array<string,int>
     */
    protected static $weightMap = [
        'thin' => 100,
        'extralight' => 200,
        'ultralight' => 200,
        'light' => 300,
        'book' => 350,
        'regular' => 400,
        'medium' => 400,
        'demi' => 600,
        'semibold' => 600,
        'bold' => 700,
        'black' => 900,
        'heavy' => 900,
        // Adobe specification says "All" as a weight, fallback
        'all' => 400,
    ];

    public static function getExtensions(): array
    {
        return ['afm', 'acfm', 'amfm'];
    }

    public static function parse(string $raw): array
    {
        $lines = preg_split("/\r\n|\n|\r/", $raw);
        if ($lines === false) {
            return [];
        }

        return self::doParse($lines);
    }

    /**
     * Helper method to parse an AFM file.
     *
     * @param  list<string>  $lines  AFM contents, line by line
     * @return array{name?:non-empty-string,weight?:int,italic?:bool} Found metadata information
     */
    // Complexity 11. Personal opinion: that's readable enough.
    // If you think complexity should be reduced, please provide a pull request!
    // This ignore statement is only here to get exit code 0, allowing pipeline to succeed.
    // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
    private static function doParse(array $lines): array
    {
        $meta = [];

        $fullName = null;
        $fontName = null;

        $maxLines = count($lines);
        $lineIndex = 0;
        $excluded = [];
        do {
            $line = $lines[$lineIndex];

            [$key, $value] = self::parseLine($line, $excluded);
            switch ($key) {
                case 'FamilyName':
                    /**
                     * FamilyName is a non-empty-string
                     *
                     * @var non-empty-string $value
                     */
                    $meta['name'] = $value;
                    $excluded = [
                        ...$excluded,
                        'FamilyName',
                        'FontName',
                        'FullName',
                    ];
                    break;
                case 'FontName':
                    /**
                     * FontName is a non-empty-string
                     *
                     * @var non-empty-string $value
                     */
                    $fontName = $value;
                    $excluded[] = 'FontName';
                    break;
                case 'FullName':
                    /**
                     * FullName is a non-empty-string
                     *
                     * @var non-empty-string $value
                     */
                    $fullName = $value;
                    $excluded[] = 'FullName';
                    break;
                case 'Weight':
                    /**
                     * Weight is an integer
                     *
                     * @var int $value
                     */
                    $meta['weight'] = $value;
                    $excluded[] = 'Weight';
                    break;
                case 'ItalicAngle':
                    /**
                     * Italic is a bool
                     *
                     * @var bool $value
                     */
                    $meta['italic'] = $value;
                    $excluded[] = 'ItalicAngle';
                    break;
                default:
                    // Nothing found
                    break;
            }

            $lineIndex++;
        } while (
            // phpcs:disable Generic.WhiteSpace.ArbitraryParenthesesSpacing.SpaceAfterOpen
            // phpcs:disable Generic.WhiteSpace.ArbitraryParenthesesSpacing.SpaceBeforeClose
            $lineIndex < $maxLines
            && (
                isset($meta['name']) === false
                || isset($meta['italic']) === false
                || isset($meta['weight']) === false
            )
            // phpcs:enable Generic.WhiteSpace.ArbitraryParenthesesSpacing.SpaceAfterOpen
            // phpcs:enable Generic.WhiteSpace.ArbitraryParenthesesSpacing.SpaceBeforeClose
        );

        // Normalize
        $bestName = ($meta['name'] ?? $fullName ?? $fontName);
        if ($bestName !== null) {
            $meta['name'] = $bestName;
        }

        return $meta;
    }

    /**
     * Helper method to parse a line. Will check for these data:
     * - FamilyName
     * - FullName
     * - FontName
     * - Weight
     * - ItalicAngle
     *
     * Returns key and value if found, null for both otherwise.
     *
     * @param  string  $line  Current AFM line
     * @param  list<string>  $excluded  Excluded keys
     * @return array{0:string|null,1:string|int|bool|null} First cell is a key, second is corresponding value.
     */
    protected static function parseLine(string $line, array $excluded = []): array
    {
        $calls = [
            'FamilyName' => 'searchFamilyNameInLine',
            'FullName' => 'searchFullNameInLine',
            'FontName' => 'searchFontNameInLine',
            'Weight' => 'searchWeightInLine',
            'ItalicAngle' => 'searchItalicAngleInLine',
        ];
        foreach ($calls as $key => $call) {
            if (\in_array($key, $excluded) === false) {
                $value = self::$call($line);
                if ($value !== null) {
                    return [$key, $value];
                }
            }
        }

        return [null, null];
    }

    /**
     * Utility method to parse an AFM line (FamilyName).
     *
     * @param  string  $line  Current line
     * @return string|null String for FamilyName if found, null otherwise.
     */
    private static function searchFamilyNameInLine(string $line): ?string
    {
        if (preg_match('/^FamilyName\s+(.+)$/', $line, $m) === 1) {
            return trim($m[1]);
        }

        return null;
    }

    /**
     * Utility method to parse an AFM line (FullName).
     *
     * @param  string  $line  Current line
     * @return string|null String for FullName if found, null otherwise.
     */
    private static function searchFullNameInLine(string $line): ?string
    {
        if (preg_match('/^FullName\s+(.+)$/', $line, $m) === 1) {
            return trim($m[1]);
        }

        return null;
    }

    /**
     * Utility method to parse an AFM line (FontName).
     *
     * @param  string  $line  Current line
     * @return string|null String for FontName if found, null otherwise.
     */
    private static function searchFontNameInLine(string $line): ?string
    {
        if (preg_match('/^FontName\s+(.+)$/', $line, $m) === 1) {
            return trim($m[1]);
        }

        return null;
    }

    /**
     * Utility method to parse an AFM line (Weight).
     *
     * @param  string  $line  Current line
     * @return int|null Bool for weight if found, null otherwise.
     */
    private static function searchWeightInLine(string $line): ?int
    {
        if (preg_match('/^Weight\s+(.+)$/', $line, $m) === 1) {
            // Pint removes brackets here
            // phpcs:ignore Squiz.Formatting.OperatorBracket.MissingBrackets
            return self::$weightMap[strtolower(trim($m[1]))] ?? 400;
        }

        return null;
    }

    /**
     * Utility method to parse an AFM line (ItalicAngle).
     *
     * @param  string  $line  Current line
     * @return bool|null Bool for italic if found, null otherwise.
     */
    private static function searchItalicAngleInLine(string $line): ?bool
    {
        if (preg_match('/^ItalicAngle\s+(-?\d+(\.\d+)?)$/', $line, $m) === 1) {
            return floatval($m[1]) !== floatval(0);
        }

        return null;
    }
}
