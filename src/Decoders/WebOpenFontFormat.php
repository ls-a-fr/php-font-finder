<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Exceptions\InvalidOperationException;
use Lsa\Font\Finder\Exceptions\ZlibException;

/**
 * Web Open Font Format files (WOFF)
 *
 * @see https://www.w3.org/TR/WOFF/
 */
class WebOpenFontFormat implements FontDecoder
{
    public const TABLE_ENTRY_FORMAT_STR = 'a4tag/Noffset/NcompLength/NorigLength/Nchecksum';

    public static function canExecute(string $raw): bool
    {
        $signature = \substr($raw, 0, 4);

        return $signature === 'wOFF';
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        $raw = self::decodeWoff($raw);

        return TrueTypeFont::extractFontMeta($raw, $filename);
    }

    /**
     * Utility method to decode WOFF files.
     *
     * @param  string  $raw  Raw binary content
     * @return string Decoded TrueTypeFont content
     *
     * @throws InvalidOperationException Not a WOFF file
     * @throws ZlibException Unable to decompress WOFF table
     */
    private static function decodeWoff(string $raw): string
    {
        // WOFFHeader (44 bytes)
        // Offset  Size  Description
        // 0       4     signature = 'wOFF'
        // 4       4     flavor (SFNT type: 0x00010000, 'OTTO', 'true', 'typ1', 'ttcf')
        // 8       4     length (total WOFF file size)
        // 12      2     numTables
        // 14      2     reserved (must be 0)
        // 16      4     totalSfntSize (uncompressed size)
        // 20      2     majorVersion
        // 22      2     minorVersion
        // 24      4     metaOffset
        // 28      4     metaLength
        // 32      4     metaOrigLength
        // 36      4     privOffset
        // 40      4     privLength
        $signature = DecoderUtils::unpackString('a4', $raw);
        if ($signature !== 'wOFF') {
            throw new InvalidOperationException('Not a WOFF file');
        }

        $flavor = DecoderUtils::unpackInt('N', $raw, 4);
        $numTables = DecoderUtils::unpackInt('n', $raw, 12);

        $offset = 44;
        // Table directory
        $tables = [];
        for ($i = 0; $i < $numTables; $i++) {
            // TableDirectoryEntry (20 bytes)
            // Offset  Size  Description
            // 0       4     tag (ex: 'cmap', 'head', 'glyf')
            // 4       4     offset (in WOFF file)
            // 8       4     compLength (compressed length)
            // 12      4     origLength (uncompressed length)
            // 16      4     checksum
            $tables[] = DecoderUtils::unpackArray(self::TABLE_ENTRY_FORMAT_STR, $raw, $offset);
            $offset += 20;
        }

        // Build SFNT header
        $searchRange = (pow(2, floor(log($numTables, 2))) * 16);
        $entrySelector = floor(log($numTables, 2));
        $rangeShift = (($numTables * 16) - $searchRange);

        $sfnt = pack(
            'Nnnnn',
            $flavor,
            $numTables,
            $searchRange,
            $entrySelector,
            $rangeShift
        );

        // Table records + data
        $tableRecords = '';
        $tableData = '';
        $offset = (12 + (16 * $numTables));

        foreach ($tables as $t) {
            $chunk = substr($raw, (int) $t['offset'], (int) $t['compLength']);

            if ($t['compLength'] !== $t['origLength']) {
                // Try zlib first, then raw deflate. gzinflate call should not happen
                // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
                $chunk2 = @gzuncompress($chunk);
                if ($chunk2 === false) {
                    // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
                    $chunk2 = @gzinflate($chunk);
                }

                if ($chunk2 === false) {
                    // Nothing can be done, this file seems corrupted
                    throw new ZlibException('Unable to decompress WOFF table '.$t['tag']);
                }
                $chunk = $chunk2;
            }

            // TableRecord (16 bytes)
            // Offset  Size  Description
            // 0       4     tag
            // 4       4     checksum
            // 8       4     offset
            // 12      4     length
            $tableRecords .= pack(
                'a4NNN',
                $t['tag'],
                $t['checksum'],
                $offset,
                $t['origLength']
            );

            $tableData .= $chunk;

            // Padding 4 bytes
            $pad = ((4 - (((int) $t['origLength']) % 4)) % 4);

            if ($pad !== 0) {
                $tableData .= str_repeat("\0", $pad);
            }

            $offset += (((int) $t['origLength']) + $pad);
        }

        return $sfnt.$tableRecords.$tableData;
    }
}
