<?php

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\BinaryReader;
use RuntimeException;

class WebOpenFontFormat
{
    public static function extractFontMeta(string $raw): array
    {
        $raw = self::decodeWoff($raw);
        return TrueTypeFont::extractFontMeta($raw);
    }

    private static function decodeWoff($raw): string
    {
        $reader = new BinaryReader($raw);

        $flavor = $reader->readUInt32();       // scaler type (TTF/OTF)
        $length = $reader->readUInt32();       // total length
        $numTables = $reader->readUInt16();    // number of tables
        $reader->read(6);                       // skip searchRange, entrySelector, rangeShift

        // Read table directory
        $tables = [];
        for ($i = 0; $i < $numTables; $i++) {
            $tag = $reader->read(4);
            $offset = $reader->readUInt32();
            $compLength = $reader->readUInt32();
            $origLength = $reader->readUInt32();
            $reader->readUInt32(); // checksum, ignored

            $tables[$tag] = [
                'offset' => $offset,
                'compLength' => $compLength,
                'origLength' => $origLength,
            ];
        }

        // Build minimal SFNT : header + table records
        $sfnt = '';

        // SFNT header
        $sfnt .= pack('Nnnnn', $flavor, $numTables, 0, 0, 0);
        // searchRange, entrySelector, rangeShift = 0

        // Table records
        $tableRecords = '';
        $tableData = '';
        $currentOffset = 12 + 16 * $numTables; // Data start after header + table records

        foreach ($tables as $tag => $t) {
            $offset = $t['offset'];
            $compLength = $t['compLength'];
            $origLength = $t['origLength'];

            if ($offset + $compLength > strlen($raw)) {
                throw new RuntimeException("Table $tag exceeds WOFF data length");
            }

            $chunk = substr($raw, $offset, $compLength);
            if ($compLength !== $origLength) {
                $chunk = gzuncompress($chunk);
                if ($chunk === false || strlen($chunk) !== $origLength) {
                    throw new RuntimeException("Failed to decompress table $tag");
                }
            }

            $checksum = 0;
            $tableRecords .= pack('a4NNN', $tag, $checksum, $currentOffset, $origLength);

            // padding 4 bytes
            $pad = (4 - (strlen($chunk) % 4)) % 4;
            $tableData .= $chunk . str_repeat("\0", $pad);

            $currentOffset += strlen($chunk) + $pad;
        }

        return $sfnt . $tableRecords . $tableData;
    }
}