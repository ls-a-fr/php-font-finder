<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Decoders\Lib\TrueTypeUtils;
use Lsa\Font\Finder\Font;

/**
 * TrueTypeFont files (TTF)
 */
class TrueTypeFont implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        $signature = \substr($raw, 0, 4);
        switch ($signature) {
            // TTF
            case "\x00\x01\x00\x00":
                // OTF CFF
            case 'OTTO':
                // Apple TTF
            case 'true':
                // CFF Type 1
            case 'typ1':
                return true;
            default:
                return false;
        }
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        // TTF layout:
        // Offset  Size  Description
        // 0       4     scaler type (0x00010000 or 'OTTO')
        // 4       2     numTables
        // 6       2     searchRange
        // 8       2     entrySelector
        // 10      2     rangeShift
        // 12      ...   table records (numTables x 16 bytes)
        //
        // Get number of tables in file
        $numTables = DecoderUtils::unpackInt('n', $raw, 4);

        // Tables always start at offset 12
        $tables = TrueTypeUtils::getTables($raw, $numTables, 12);

        return [
            new Font([
                'filename' => $filename,
                ...TrueTypeUtils::getTrueTypeInformation($tables, $raw),
            ]),
        ];
    }
}
