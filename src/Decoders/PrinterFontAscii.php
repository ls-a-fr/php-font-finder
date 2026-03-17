<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Decoders\Metadata\AdobeFontMetrics;
use Lsa\Font\Finder\Decoders\Metadata\PrinterFontMetrics;
use Lsa\Font\Finder\Exceptions\NonReadableContentException;
use Lsa\Font\Finder\Font;

/**
 * PrinterFontAscii files (PFA)
 * Used by PrinterFontBinary decoder (PFB)
 *
 * @see https://adobe-type-tools.github.io/font-tech-notes/pdfs/5040.Download_Fonts.pdf
 */
class PrinterFontAscii implements FontDecoder
{
    /**
     * A PSA file will contain weight as string, not integer.
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
    ];

    public static function canExecute(string $raw): bool
    {
        // PFA always starts with a PostScript header
        return str_starts_with($raw, '%!PS-AdobeFont-1.0')
            || str_starts_with($raw, '%!FontType1');
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        $meta = self::findMetadataInExtraFile($filename);

        // Get information from PFA/PFB only if information is missing
        if (
            $meta['weight'] === null ||
            $meta['italic'] === null ||
            $meta['name'] === null
        ) {
            $meta = self::includeMetadataInFile($raw, $meta);
        }

        // Fallbacks: bold
        if ($meta['weight'] !== null) {
            // phpcs:ignore Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
            $meta['bold'] = $meta['weight'] >= 700;
        } else {
            $meta['weight'] = 400;
            $meta['bold'] = false;
        }

        // Fallbacks: family name
        if ($meta['name'] === null) {
            if (\str_ends_with($filename, 'pfa') === true) {
                $meta['name'] = DecoderUtils::getFontNameFromFilePath($filename, 'pfa');
            } elseif (\str_ends_with($filename, 'pfb') === true) {
                $meta['name'] = DecoderUtils::getFontNameFromFilePath($filename, 'pfb');
            } else {
                $meta['name'] = DecoderUtils::getFontNameFromFilePath($filename);
            }
        }

        // Fallbacks: italic based on name
        if ($meta['italic'] === null) {
            /**
             * Cannot find where $familyName may be bool|int|string. Overriding PHPStan.
             *
             * @var string $familyName
             */
            $familyName = $meta['name'];
            $lname = strtolower($familyName);
            if (str_contains($lname, 'italic') === true || str_contains($lname, 'oblique') === true) {
                $meta['italic'] = true;
            } else {
                $meta['italic'] = false;
            }
        }

        return [
            new Font([
                'filename' => $filename,
                ...$meta,
            ]),
        ];
    }

    /**
     * Get metadata from extra files: AFM (Adobe Font Metrics) or PFM (Printer Font Metrics)
     * PrinterFontAscii does not always have metadata information stored in file.
     * Information can be found in:
     * 1. AFM file: Adobe Font Metrics: best choice
     * 2. PFM : Printer Font Metrics: second best
     * 3. Font file
     *
     * @return array{name:null|string,weight:null|int,italic:null|bool,bold:null|bool} $meta
     */
    protected static function findMetadataInExtraFile(string $filename): array
    {
        $filenameWithoutExtension = $filename;
        if (\str_contains($filename, '.') === true) {
            // Removes extension
            $filenameParts = explode('.', $filename);
            array_pop($filenameParts);
            $filenameWithoutExtension = implode('.', $filenameParts);
        }

        // Get information from AFM first
        $meta = (self::extractAfmData($filenameWithoutExtension) ?? []);

        // Check any data is missing before reading PFM
        if (
            isset($meta['name']) === false ||
            isset($meta['weight']) === false ||
            isset($meta['italic']) === false
        ) {
            // PFM will not override AFM information
            $meta = [
                ...(self::extractPfmData($filenameWithoutExtension) ?? []),
                ...$meta,
            ];
        }

        // Normalize
        $meta['name'] = ($meta['name'] ?? null);
        $meta['weight'] = ($meta['weight'] ?? null);
        $meta['italic'] = ($meta['italic'] ?? null);
        if ($meta['weight'] !== null) {
            // phpcs:ignore Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
            $meta['bold'] = ($meta['weight'] >= 700);
        } else {
            $meta['bold'] = null;
        }

        /**
         * We just defined every key, so these cannot be "unset" anymore.
         *
         * @var array{name:null|string,weight:null|int,italic:null|bool,bold:null|bool} $meta
         */
        return $meta;
    }

    /**
     * Helper method to extract AFM data
     *
     * @param  string  $filenameWithoutExtension  Font filename, without extension
     * @return array{name?:non-empty-string,weight?:int,italic?:bool}|null
     *
     * @throws NonReadableContentException File exists but is not readable
     */
    private static function extractAfmData(string $filenameWithoutExtension): ?array
    {
        // AFM filename
        $afm = $filenameWithoutExtension.'.afm';

        // Get info from AFM file
        if (\file_exists($afm) === false) {
            return null;
        }

        $afmContents = \file_get_contents($afm);
        if ($afmContents === false) {
            throw new NonReadableContentException('Could not get AFM contents, check your permissions');
        }

        return AdobeFontMetrics::parse($afmContents);
    }

    /**
     * Helper method to extract PFM data
     *
     * @param  string  $filenameWithoutExtension  Font filename, without extension
     * @return array{name?:non-empty-string,weight?:int,italic?:bool}|null
     *
     * @throws NonReadableContentException File exists but is not readable
     */
    private static function extractPfmData(string $filenameWithoutExtension): ?array
    {
        $pfm = $filenameWithoutExtension.'.pfm';
        if (\file_exists($pfm) === false) {
            return null;
        }
        $pfmContents = \file_get_contents($pfm);
        if ($pfmContents === false) {
            throw new NonReadableContentException('Could not get PFM contents, check your permissions');
        }

        return PrinterFontMetrics::parse($pfmContents);
    }

    /**
     * Get metadata from PFA or PFB file.
     * Metadata can be found in header: /FamilyName, /FontName, /ItalicAngle and /Weight.
     *
     * @param  string  $raw  Raw binary content
     * @param  array{name:null|string,weight:null|int,italic:null|bool,bold:null|bool}  $meta
     * @return array{name:null|string,weight:null|int,italic:null|bool,bold:null|bool}
     */
    protected static function includeMetadataInFile(string $raw, array $meta): array
    {
        $fontName = null;
        // FamilyName if it exists
        if ($meta['name'] === null && preg_match('/^\/FamilyName\s*\(([^)]+)\)/m', $raw, $m) === 1) {
            $meta['name'] = $m[1];
        }
        // FontName
        if ($meta['name'] === null && preg_match('/^\/FontName\s*\/([A-Za-z0-9\-_]+)/m', $raw, $m) === 1) {
            $fontName = $m[1];
        }

        // ItalicAngle
        if ($meta['italic'] === null && preg_match('/^\/ItalicAngle\s+(-?\d+(\.\d+)?)/m', $raw, $m) === 1) {
            $angle = floatval($m[1]);
            if ($angle !== 0.0) {
                $meta['italic'] = true;
            }
        }

        // Weight
        if ($meta['weight'] === null && preg_match('/^\/Weight\s*\(([^)]+)\)/m', $raw, $m) === 1) {
            $w = strtolower($m[1]);
            if (isset(self::$weightMap[$w]) === true) {
                $meta['weight'] = self::$weightMap[$w];
                if ($meta['weight'] >= 700) {
                    $meta['bold'] = true;
                }
            }
        }

        $meta['name'] ??= $fontName;

        return $meta;
    }
}
