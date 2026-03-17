<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Exceptions\InvalidOperationException;

/**
 * PrinterFontBinary format (PFB files)
 *
 * @see https://adobe-type-tools.github.io/font-tech-notes/pdfs/5040.Download_Fonts.pdf
 */
class PrinterFontBinary implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        // PFB always starts with 0x8001 or 0x8002
        // See FreeType `read_pfb_tag` function:
        // - https://github.com/freetype/freetype/blob/264b5fbf5b912b39f98d038bf75d39be0a73f21b/src/type1/t1parse.c#L69
        if (isset($raw[0]) === true && ord($raw[0]) !== 0x80) {
            return false;
        }

        // However, for this library, we do not care about 0x02, because they contain binary data about
        // characters, not font formation
        if (isset($raw[1]) === true && ord($raw[1]) === 0x01) {
            return true;
        }

        // Do not accept Type 0x02 for second byte
        return false;
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        $fileLength = strlen($raw);
        $offset = 0;
        $ascii = '';

        // PFB works with segments. For every segment, a type is defined, then a value. Below, the table:
        // Type 1: ASCII
        // Type 2: Binary
        // Type 3: End of file
        while ($offset < $fileLength) {
            // Segment type
            $marker = ord($raw[$offset++]);
            if ($marker !== 0x80) {
                throw new InvalidOperationException('Invalid PFB, did you call canExecute first?');
            }

            $type = ord($raw[$offset++]);

            // Type sanity check
            switch ($type) {
                case 0x01:
                case 0x02:
                    break;
                case 0x03:
                    // End of file
                    break 2;
                default:
                    // Unknown type
                    throw new InvalidOperationException('Corrupted PFB file');
            }

            // Type should be 0x01 or 0x02
            // Note that we still need to handle length for 0x02 to advance binary pointer
            try {
                $length = DecoderUtils::unpackInt('V', $raw, $offset);
            } catch (InvalidOperationException $e) {
                throw new InvalidOperationException('Corrupted PFB file: cannot read block length', 0, $e);
            }

            if ($type === 0x01) {
                // ASCII data, what we need
                $ascii .= substr($raw, $offset, $length);
            }

            // Length offset
            $offset += 4;
            // Data offset
            $offset += $length;
        }

        if ($ascii === '') {
            return [];
        }

        // Delegate to PFA decoder
        return PrinterFontAscii::extractFontMeta($ascii, $filename);
    }
}
