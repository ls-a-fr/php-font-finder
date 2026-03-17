<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Font;

/**
 * PortableCompiledFormat (PCF)
 *
 * @see https://fontforge.org/docs/techref/pcf-format.html
 */
class PortableCompiledFormat implements FontDecoder
{
    /**
     * "PROPERTIES" table type: sole table used, other constants are given for readability
     */
    public const TABLE_PROPERTIES_TYPE = 1;

    /**
     * "ACCELERATORS" table type: only defined for readability
     */
    public const TABLE_ACCELERATORS_TYPE = 2;

    /**
     * "METRICS" table type: only defined for readability
     */
    public const TABLE_METRICS_TYPE = 4;

    /**
     * "BITMAPS" table type: only defined for readability
     */
    public const TABLE_BITMAPS_TYPE = 8;

    /**
     * "INK_METRICS" table type: only defined for readability
     */
    public const TABLE_INK_METRICS_TYPE = 16;

    /**
     * "BDF_ENCODINGS" table type: only defined for readability
     */
    public const TABLE_BDF_ENCODINGS_TYPE = 32;

    /**
     * "SWIDTHS" table type: only defined for readability
     */
    public const TABLE_SWIDTHS_TYPE = 64;

    /**
     * "GLYPH_NAMES_ENCODINGS" table type: only defined for readability
     */
    public const TABLE_GLYPH_NAMES_ENCODINGS_TYPE = 128;

    public static function canExecute(string $raw): bool
    {
        if (strlen($raw) < 4) {
            return false;
        }

        // PCF magic is "pcf\x01" = 70 63 66 01
        // But some files store it little-endian: x01 x66 x63 x70
        $magic = substr($raw, 0, 4);

        // Check both endian variants, just in case
        // Little-endian
        if ($magic === "\x01\x66\x63\x70") {
            return true;
        }
        // Big-endian
        if ($magic === "pcf\x01") {
            return true;
        }

        return false;
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        $props = self::readProperties($raw);

        // Properties gathered
        $name = ($props['FONT'] ?? $props['FAMILY_NAME'] ?? DecoderUtils::getFontNameFromFilePath($filename, 'pcf'));
        $weightName = null;
        if (isset($props['WEIGHT_NAME']) === true) {
            $weightName = strtolower($props['WEIGHT_NAME']);
        }

        $slant = null;
        if (isset($props['SLANT']) === true) {
            $slant = strtolower($props['SLANT']);
        }

        // Check XLFD (X Logical Font Description) as a fallback
        // Ensures found value is XLFD
        if (isset($name[0]) === true && $name[0] === '-') {
            [$family, $weight, $italic, $bold] = DecoderUtils::parseXlfd($name);
        } else {
            // FONT is not XLFD, set it as family name
            $family = $name;
        }

        // phpcs:disable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
        // phpcs:ignore Squiz.PHP.DisallowInlineIf.Found
        $italic = ($slant === 'i' || $slant === 'o') ?: false;
        $weight = (DecoderUtils::$weightMap[$weightName] ?? 400);
        $bold = ($weight >= 700);
        // phpcs:enable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison

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

    /**
     * Read properties inside PCF format. Note that properties are in little-endian
     *
     * @param  string  $raw  Raw binary content
     * @return array<string,string> Properties
     */
    private static function readProperties(string $raw): array
    {
        $props = [];

        if (strlen($raw) < 16) {
            return $props;
        }

        // Get tables count
        $count = DecoderUtils::unpackInt('V', $raw, 4);

        // Directory starts at offset 8. Layout:
        // Type     Table name
        // 1        PROPERTIES
        // 2        ACCELERATORS
        // 4        METRICS
        // 8        BITMAPS
        // 16       INK_METRICS
        // 32       BDF_ENCODINGS
        // 64       SWIDTHS
        // 128      GLYPH_NAMES
        // We only care about PROPERTIES table.
        $propertiesTableOffset = null;
        $directoryOffset = 8;
        for ($i = 0; $i < $count; $i++) {
            // Table layout:
            // Offset   Size    Description
            // 0        4       Type (see layout)
            // 4        4       Format
            // 8        4       Size
            // 12       4       Offset
            $tableType = DecoderUtils::unpackInt('V', $raw, $directoryOffset);
            if ($tableType === self::TABLE_PROPERTIES_TYPE) {
                $propertiesTableOffset = DecoderUtils::unpackInt('V', $raw, ($directoryOffset + 12));
                break;
            }
            $directoryOffset += 16;
        }

        // Must find PROPERTIES table to continue
        if ($propertiesTableOffset === null) {
            return $props;
        }

        // Get PROPERTIES table offset
        $offset = $propertiesTableOffset;
        // Get table format. This format specify if PROPERTIES are stored in little-endian or big-endian.
        $tableFormat = DecoderUtils::unpackInt('V', $raw, $offset);

        // Set unpack format: N for BigEndian, V for LittleEndian
        // phpcs:ignore Squiz.PHP.DisallowComparisonAssignment.AssignedComparison, Squiz.PHP.DisallowInlineIf.Found
        $unpackFormat = (($tableFormat & 0x04) !== 0) ? 'N' : 'V';

        // Get property count
        $nProps = DecoderUtils::unpackInt($unpackFormat, $raw, ($offset + 4));

        $names = [];
        $values = [];

        // Put offset at right place, as we read through PROPERTIES table
        $offset = ($offset + 8);
        for ($i = 0; $i < $nProps; $i++) {
            // PROPERTIES table layout:
            // Offset   Size    Description
            // 0        4       Name offset
            // 4        1       isStringProp
            // 5        4       Value
            $names[] = DecoderUtils::unpackInt($unpackFormat, $raw, $offset);
            // phpcs:ignore Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
            $values[] = [
                'isString' => ord($raw[($offset + 4)]) !== 0,
                'value' => DecoderUtils::unpackInt($unpackFormat, $raw, ($offset + 5)),
            ];
            $offset += 9;
        }

        // Handle padding
        if (($offset % 4) !== 0) {
            $offset = ($offset + (4 - ($offset % 4)));
        }

        // Skip stringSize field
        $offset += 4;

        // Read through strings
        $stringTableOffset = $offset;
        foreach ($names as $i => $nameOffset) {
            $offset = ($stringTableOffset + $nameOffset);
            [$name, $offset] = DecoderUtils::readNullPaddedString($raw, $offset);

            if ($values[$i]['isString'] === true) {
                $offset = ($stringTableOffset + $values[$i]['value']);
                [$value, $offset] = DecoderUtils::readNullPaddedString($raw, $offset);
            } else {
                $value = (string) $values[$i]['value'];
            }

            $props[$name] = $value;
        }

        return $props;
    }
}
