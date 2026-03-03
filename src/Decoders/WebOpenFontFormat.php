<?php

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\BinaryReader;
use Lsa\Font\Finder\Contracts\FontDecoder;
use RuntimeException;

class WebOpenFontFormat implements FontDecoder
{
    public static function extractFontMeta(string $raw, string $filename): array
    {
        $raw = self::decodeWoff($raw);
        return TrueTypeFont::extractFontMeta($raw, $filename);
    }

    private static function decodeWoff(string $raw): string
    {
        $r = new BinaryReader($raw);

        $signature = $r->read(4);
        if ($signature !== "wOFF") {
            throw new RuntimeException("Not a WOFF file");
        }

        $flavor        = $r->readUInt32();
        $length        = $r->readUInt32();
        $numTables     = $r->readUInt16();
        $r->readUInt16(); // reserved
        $totalSfntSize = $r->readUInt32();
        $r->readUInt16(); // majorVersion
        $r->readUInt16(); // minorVersion
        $r->readUInt32(); // metaOffset
        $r->readUInt32(); // metaLength
        $r->readUInt32(); // metaOrigLength
        $r->readUInt32(); // privOffset
        $r->readUInt32(); // privLength

        // Table directory
        $tables = [];
        for ($i = 0; $i < $numTables; $i++) {
            $tag        = $r->read(4);
            $offset     = $r->readUInt32();
            $compLength = $r->readUInt32();
            $origLength = $r->readUInt32();
            $checksum   = $r->readUInt32();

            $tables[] = compact('tag', 'offset', 'compLength', 'origLength', 'checksum');
        }

        // Build SFNT header
        $searchRange   = pow(2, floor(log($numTables, 2))) * 16;
        $entrySelector = floor(log($numTables, 2));
        $rangeShift    = $numTables * 16 - $searchRange;

        $sfnt  = pack('N', $flavor);
        $sfnt .= pack('n', $numTables);
        $sfnt .= pack('n', $searchRange);
        $sfnt .= pack('n', $entrySelector);
        $sfnt .= pack('n', $rangeShift);

        // Table records + data
        $tableRecords = '';
        $tableData    = '';
        $offset       = 12 + 16 * $numTables;

        foreach ($tables as $t) {
            $chunk = substr($raw, $t['offset'], $t['compLength']);

            if ($t['compLength'] !== $t['origLength']) {
                // Essayer ZLIB (normal pour WOFF)
                $chunk2 = @gzuncompress($chunk);

                if ($chunk2 === false) {
                    // Essayer DEFLATE brut (rare)
                    $chunk2 = @gzinflate($chunk);
                }

                if ($chunk2 === false) {
                    // Debug BSD
                    error_log(sprintf(
                        "TABLE %s: offset=%d compLength=%d origLength=%d",
                        $t['tag'],
                        $t['offset'],
                        $t['compLength'],
                        $t['origLength']
                    ));
                    if ($t['compLength'] !== $t['origLength']) {
                        $prefix = bin2hex(substr($raw, $t['offset'], 4));
                        error_log(sprintf(
                            "COMPRESSED? %s: compLength=%d origLength=%d prefix=%s",
                            $t['tag'],
                            $t['compLength'],
                            $t['origLength'],
                            $prefix
                        ));
                    }
                    // End Debug BSD
                    throw new RuntimeException("Unable to decompress WOFF table {$t['tag']}");
                }

                $chunk = $chunk2;
            }


            $pad = (4 - ($t['origLength'] % 4)) % 4;

            $tableRecords .= pack(
                'a4NNN',
                $t['tag'],
                $t['checksum'],
                $offset,
                $t['origLength']
            );

            $tableData .= $chunk . str_repeat("\0", $pad);
            $offset += $t['origLength'] + $pad;
        }

        return $sfnt . $tableRecords . $tableData;
    }
}
