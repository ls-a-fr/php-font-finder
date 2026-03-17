<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders\Lib;

/**
 * TrueTypeUtils contains various methods to help TrueType-related decoders
 */
class TrueTypeUtils
{
    /**
     * Name ID: Typographic name
     */
    public const ID_TYPOGRAPHIC_NAME = 16;

    /**
     * Name ID: Family name
     */
    public const ID_FAMILY_NAME = 1;

    /**
     * Name ID: PostScript name
     */
    public const ID_POSTSCRIPT_NAME = 6;

    /**
     * Name ID: Full name
     */
    public const ID_FULL_NAME = 4;

    // Platform definitions
    public const PLATFORM_UNICODE = 0;

    public const PLATFORM_MACINTOSH = 1;

    public const PLATFORM_ISO = 2;

    public const PLATFORM_WINDOWS = 3;

    public const PLATFORM_CUSTOM = 4;

    /**
     * Helper method to gather needed information in TrueType:
     * - Italic
     * - Bold
     * - Weight
     * - FamilyName
     *
     * @param  array<string, array{0: int, 1: int}>  $tables  TrueType tables
     * @param  string  $raw  Raw binary content
     * @return array{italic:bool,bold:bool,weight:int,name:string}
     */
    public static function getTrueTypeInformation(array $tables, string $raw): array
    {
        $records = self::getRecords($tables['name'], $raw);
        $weight = 400;
        $italic = false;
        $bold = false;

        if (isset($tables['OS/2']) === true) {
            // Search in OS/2 table. You may find weight, italic and bold.
            [$offset] = $tables['OS/2'];

            // Get weight (offset 4)
            $weight = DecoderUtils::unpackInt('n', $raw, ($offset + 4));
            // Get fsSelection (offset 62)
            $fsSelection = DecoderUtils::unpackInt('n', $raw, ($offset + 62));

            // Get information
            // phpcs:disable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
            $italic = ($fsSelection & 0x01) !== 0;
            $bold = ($fsSelection & 0x20) !== 0;
            // phpcs:enable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
        } elseif (isset($tables['head']) === true) {
            // Search in head table. You may find italic and bold.
            [$offset] = $tables['head'];

            // Get macStyle (offset 44)
            $macStyle = DecoderUtils::unpackInt('n', $raw, ($offset + 44));
            // phpcs:disable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
            $italic = ($macStyle & 0x0002) !== 0;
            $bold = ($macStyle & 0x0001) !== 0;
            // phpcs:enable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
        }

        // Get family name
        $family = self::getFamilyName($records);

        // And now, fallbacks.
        // First fallback is for italic: check italicAngle offset
        if ($italic === false && isset($tables['post']) === true) {
            [$offset] = $tables['post'];

            // Read italicAngle (offset 4, fixed 16.16)
            $rawItalicAngle = DecoderUtils::unpackInt('N', $raw, ($offset + 4));
            // Convert 16.16 to float
            $italicAngle = ($rawItalicAngle / 65536.0);

            if ($italicAngle !== 0.0) {
                $italic = true;
            }
        }

        // Second fallback is for bold: check on weight
        if ($bold === false && $weight >= 700) {
            $bold = true;
        }

        // Third fallback is font name: check for italic or oblique
        if ($italic === false) {
            $lName = \strtolower($family);
            if (\str_contains($lName, 'italic') === true || \str_contains($lName, 'oblique') === true) {
                $italic = true;
            }
        }

        return [
            'italic' => $italic,
            'bold' => $bold,
            'weight' => $weight,
            'name' => $family,
        ];
    }

    /**
     * Helper method to check if fond font is a real one. A font may be virtual, meaning
     * it only contains common glyphs for other fonts in same file.
     *
     * @param  array<string,array{0:int,1:int}>  $tables  TrueType tables
     * @return bool True if this is a real font, false otherwise.
     */
    public static function isRealFont(array $tables): bool
    {
        // Mandatory tables
        if (isset($tables['hhea']) === false || isset($tables['hmtx']) === false) {
            return false;
        }

        // Must at least contain a glyph table
        if (
            isset($tables['glyf']) === false
            && isset($tables['CFF ']) === false
            && isset($tables['CFF2']) === false
            && isset($tables['sbix']) === false
            && isset($tables['CBDT']) === false
            && isset($tables['COLR']) === false
        ) {
            return false;
        }

        // Table cmap is not mandatory. But if available, must not be empty
        if (isset($tables['cmap']) === true && $tables['cmap'][1] < 4) {
            return false;
        }

        return true;
    }

    /**
     * Get TrueType tables for a font, based on a specific offset.
     *
     * @param  string  $raw  Raw binary content
     * @param  int  $numTables  Table count in this font
     * @param  int  $offset  Default 0, but may be different in files containing several fonts (TTC, OTC, DFONT, ...)
     * @return array<string,array{0:int,1:int}> TrueType tables
     */
    public static function getTables(string $raw, int $numTables, int $offset = 0): array
    {
        $tables = [];
        for ($i = 0; $i < $numTables; $i++) {
            // TTF Table layout:
            // Offset  Size  Description
            // 0       4     tag
            // 4       4     checksum
            // 8       4     tableOffset
            // 12      4     tableLength
            $tag = DecoderUtils::unpackString('a4', $raw, $offset);
            $tableOffset = DecoderUtils::unpackInt('N', $raw, ($offset + 8));
            $tableLength = DecoderUtils::unpackInt('N', $raw, ($offset + 12));

            $offset += 16;
            $tables[$tag] = [$tableOffset, $tableLength];
        }

        return $tables;
    }

    /**
     * Ensures this name record is a valid one.
     *
     * @param  array{platformID:self::PLATFORM_*,encodingID:int,languageID:int,nameID:self::ID_*,offset:int,length:int}  $record  Name record
     * @param  int  $stringStorageLength  Length of string storage byte
     * @return bool True if this record is a valid one, false otherwise
     */
    protected static function isValidRecord(array $record, int $stringStorageLength): bool
    {
        /**
         * Key platformID SHOULD BE one of defined constants. But don't be so sure.
         *
         * @phpstan-ignore smallerOrEqual.alwaysTrue, booleanAnd.alwaysTrue
         */
        return $record['platformID'] <= 4
            /**
             * Key nameID SHOULD BE one of defined constants. But don't be so sure.
             *
             * @phpstan-ignore smallerOrEqual.alwaysTrue
             */
            && $record['nameID'] <= 25
            && $stringStorageLength >= ($record['offset'] + $record['length']);
    }

    /**
     * Get name records in name table.
     *
     * @param  array{0: int,1: int}  $nameTable  The name TrueType table
     * @param  string  $raw  Raw binary content
     * @return array<self::ID_*,array{value:string,platform:int,language:int,best:?bool}> Name records
     */
    protected static function getRecords(array $nameTable, string $raw): array
    {
        [$offset, $length] = $nameTable;

        // Skip format (2 bytes), get count
        $count = DecoderUtils::unpackInt('n', $raw, ($offset + 2));
        // Get string offset
        $stringOffset = DecoderUtils::unpackInt('n', $raw, ($offset + 4));
        // StringStorage length: length of area where lies UTF-8 or UTF-16BE strings
        $stringStorageLength = ($length - $stringOffset);

        /**
         * Record structure
         *
         * @var array<self::ID_*,array{value:string,platform:self::PLATFORM_*,language:int,best:?bool}> $records
         */
        $records = [];
        for ($i = 0; $i < $count; $i++) {
            // Get name data
            $nameData = self::readNameTable($raw, ($offset + 6 + ($i * 12)), $stringStorageLength);
            // Invalid name data, continue
            if ($nameData === null) {
                continue;
            }

            // Unused data, continue
            // phpcs:disable PEAR.ControlStructures.MultiLineCondition.Alignment
            if (
                /**
                 * Value for nameID should be one of these, but don't be so sure
                 *
                 * @phpstan-ignore booleanAnd.alwaysFalse
                 */
                $nameData['nameID'] !== self::ID_TYPOGRAPHIC_NAME
                && $nameData['nameID'] !== self::ID_FAMILY_NAME
                && $nameData['nameID'] !== self::ID_POSTSCRIPT_NAME
                /**
                 * Value for nameID should be one of these, but don't be so sure
                 *
                 * @phpstan-ignore notIdentical.alwaysFalse
                 */
                && $nameData['nameID'] !== self::ID_FULL_NAME
            ) {
                continue;
            }
            // phpcs:enable PEAR.ControlStructures.MultiLineCondition.Alignment
            [$shouldOverride, $best] = self::shouldOverride($records, $nameData);

            // Current name record is better, skip this one
            if ($shouldOverride === false) {
                continue;
            }

            // Get value
            $value = substr(
                $raw,
                ($offset + $stringOffset + $nameData['offset']),
                $nameData['length']
            );
            if ($value === '') {
                // Value is invalid, continue
                continue;
            }

            // UTF-16BE ?
            if ($nameData['platformID'] === self::PLATFORM_WINDOWS) {
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-16BE');
            }
            $value = trim($value);

            // Override record
            $records[$nameData['nameID']] = [
                'value' => $value,
                'platform' => $nameData['platformID'],
                'language' => $nameData['languageID'],
                'best' => $best,
            ];
        }

        return $records;
    }

    /**
     * Reads a name table.
     * Some fonts have bugs in them, endianess bugs.
     * Specification asks for BigEndian records, but some fonts have little-endian or mixed-endian records.
     * This method tests first for BigEndian records.
     * If record in invalid, continue with LittleEndian and MixedEndian.
     *
     * @param  string  $raw  Raw binary content
     * @param  int  $pos  Current position in raw
     * @param  int  $stringStorageLength  Length of string storage byte. Forwarded to isValidRecord
     * @return null|array{platformID:self::PLATFORM_*,encodingID:int,languageID:int,nameID:self::ID_*,offset:int,length:int} Name record
     */
    protected static function readNameTable(string $raw, int $pos, int $stringStorageLength): ?array
    {
        /**
         * BigEndian hypothesis
         *
         * @var array{platformID:self::PLATFORM_*,encodingID:int,languageID:int,nameID:self::ID_*,offset:int,length:int} $be
         */
        $be = [
            'platformID' => DecoderUtils::unpackInt('n', $raw, ($pos + 0)),
            'encodingID' => DecoderUtils::unpackInt('n', $raw, ($pos + 2)),
            'languageID' => DecoderUtils::unpackInt('n', $raw, ($pos + 4)),
            'nameID' => DecoderUtils::unpackInt('n', $raw, ($pos + 6)),
            'length' => DecoderUtils::unpackInt('n', $raw, ($pos + 8)),
            'offset' => DecoderUtils::unpackInt('n', $raw, ($pos + 10)),
        ];
        if (self::isValidRecord($be, $stringStorageLength) === true) {
            return $be;
        }

        /**
         * LittleEndian hypothesis
         *
         * @var array{platformID:self::PLATFORM_*,encodingID:int,languageID:int,nameID:self::ID_*,offset:int,length:int} $le
         */
        $le = [
            'platformID' => DecoderUtils::unpackInt('v', $raw, ($pos + 0)),
            'encodingID' => DecoderUtils::unpackInt('v', $raw, ($pos + 2)),
            'languageID' => DecoderUtils::unpackInt('v', $raw, ($pos + 4)),
            'nameID' => DecoderUtils::unpackInt('v', $raw, ($pos + 6)),
            'length' => DecoderUtils::unpackInt('v', $raw, ($pos + 8)),
            'offset' => DecoderUtils::unpackInt('v', $raw, ($pos + 10)),
        ];
        if (self::isValidRecord($le, $stringStorageLength) === true) {
            return $le;
        }

        /**
         * MixedEndian hypothesis
         *
         * @var array{platformID:self::PLATFORM_*,encodingID:int,languageID:int,nameID:self::ID_*,offset:int,length:int} $mixed
         */
        $mixed = [
            'platformID' => DecoderUtils::unpackInt('v', $raw, ($pos + 0)),
            'encodingID' => DecoderUtils::unpackInt('v', $raw, ($pos + 2)),
            'languageID' => DecoderUtils::unpackInt('v', $raw, ($pos + 4)),
            'nameID' => DecoderUtils::unpackInt('v', $raw, ($pos + 6)),
            'length' => DecoderUtils::unpackInt('n', $raw, ($pos + 8)),
            'offset' => DecoderUtils::unpackInt('n', $raw, ($pos + 10)),
        ];
        if (self::isValidRecord($mixed, $stringStorageLength) === true) {
            return $mixed;
        }

        // This name record is invalid
        return null;
    }

    /**
     * Helper method to get font family name.
     * Priority is:
     * - Typographic name
     * - Family name
     * - PostScript name
     * - Full name
     *
     * @param  array<self::ID_*,array{value:string,platform:int,language:int,best:?bool}>  $records  Name records
     * @return string Family name, or "Unknown" if nothing found.
     */
    protected static function getFamilyName(array $records): string
    {
        // phpcs:disable Squiz.Formatting.OperatorBracket.MissingBrackets, Squiz.WhiteSpace.OperatorSpacing.SpacingAfter
        return $records[self::ID_TYPOGRAPHIC_NAME]['value'] ??
            $records[self::ID_FAMILY_NAME]['value'] ??
            $records[self::ID_POSTSCRIPT_NAME]['value'] ??
            $records[self::ID_FULL_NAME]['value'] ??
            'Unknown';
        // phpcs:enable Squiz.Formatting.OperatorBracket.MissingBrackets, Squiz.WhiteSpace.OperatorSpacing.SpacingAfter
    }

    /**
     * Uses priorities to determine if a nameData should be stored in records.
     * This method uses current found records, new record, and will return:
     * - Should override: true/false
     * - Is this the best available record in font: true/false
     *
     * @param  array<self::ID_*,array{value:string,platform:self::PLATFORM_*,language:int,best:?bool}>  $records  Current found records
     * @param  array{platformID:self::PLATFORM_*,encodingID:int,languageID:int,nameID:self::ID_*,offset:int,length:int}  $nameData  Candidate new record
     * @return array{0:bool,1:?bool} First cell for shouldOverride, second for best
     */
    protected static function shouldOverride($records, $nameData): array
    {
        if (isset($records[$nameData['nameID']]) === false) {
            $newNameRecord = [
                $nameData['platformID'],
                $nameData['languageID'],
            ];
            $newOrder = self::orderWinner($newNameRecord);

            return [true, $newOrder[1]];
        }
        $currentNameRecord = [
            $records[$nameData['nameID']]['platform'],
            $records[$nameData['nameID']]['language'],
        ];
        $newNameRecord = [
            $nameData['platformID'],
            $nameData['languageID'],
        ];

        $currentOrder = self::orderWinner($currentNameRecord);
        $newOrder = self::orderWinner($newNameRecord);
        // Lowest wins, equality means current
        if ($currentOrder[0] > $newOrder[0]) {
            return [true, $newOrder[1]];
        }

        return [false, null];
    }

    /**
     * Gets an ordinal position for a record.
     * Based on priorities, will return:
     * - Priority index
     * - Is this the best we can hope for this fond
     *
     * @param  array{0:self::PLATFORM_*,1:int}  $record  Platform and language for current record
     * @return array{0:int,1:bool} First cell for priority index, second for best
     */
    private static function orderWinner(array $record): array
    {
        foreach (self::getPriorities() as $i => $priority) {
            if ($record[0] !== $priority[0]) {
                continue;
            }
            foreach ($priority[1] as $lang) {
                if ($record[1] === $lang) {
                    return [$i, $priority[2]];
                }
                if ($lang === null) {
                    return [$i, $priority[2]];
                }
            }
        }

        return [-1, false];
    }

    /**
     * This method returns priorities for name records.
     *
     * When storing name records, priority is important. Specification says:
     * - PlatformID Windows + English languageID
     * - PlatformID Windows + any languageID
     * - PlatformID Macintosh + Roman languageID
     * - PlatformID Macintosh + any languageID
     * - PlatformID Unicode + English languageID
     * - PlatformID Unicode + any languageID
     * - PlatformID ISO + English languageID
     * - PlatformID ISO + any languageID
     * - PlatformID Custom + English languageID
     * - PlatformID Custom + any languageID
     *
     * @return list<array{0:self::PLATFORM_*,1:list<?int>,2:bool}> Priorities
     */
    protected static function getPriorities(): array
    {
        $windowsEnglishLanguageCode = self::getWindowsLanguageCode('en_us');
        $darwinEnglishLanguageCode = self::getDarwinLanguageCode('en_us');

        return [
            [
                self::PLATFORM_WINDOWS,
                [
                    $windowsEnglishLanguageCode,
                    null,
                ],
                true,
            ],
            [
                self::PLATFORM_MACINTOSH,
                [
                    $darwinEnglishLanguageCode,
                    null,
                ],
                true,
            ],
            [
                self::PLATFORM_UNICODE,
                [
                    $windowsEnglishLanguageCode,
                    null,
                ],
                false,
            ],
            [
                self::PLATFORM_ISO,
                [
                    $windowsEnglishLanguageCode,
                    null,
                ],
                false,
            ],
            [
                self::PLATFORM_CUSTOM,
                [
                    $windowsEnglishLanguageCode,
                    null,
                ],
                false,
            ],
        ];
    }

    /**
     * Define encoding for windows language code. Default is en_us
     *
     * @param  string  $lang  language name (ex: en_US)
     * @return int Language code Windows
     */
    protected static function getWindowsLanguageCode(string $lang): int
    {
        switch ($lang) {
            case 'zh_cn':
            case 'ZH_CN':
                return 2052;
            case 'zh_tw':
            case 'ZH_TW':
                return 1028;
            case 'en_US':
            case 'EN_US':
            default:
                return 1033;
        }
    }

    /**
     * Define encoding for windows language code. Default is en_us
     *
     * @param  string  $lang  language name (ex: en_US)
     * @return int Language code Darwin
     */
    protected static function getDarwinLanguageCode(string $lang): int
    {
        switch ($lang) {
            case 'en_US':
            case 'EN_US':
            default:
                return 0;
        }
    }
}
