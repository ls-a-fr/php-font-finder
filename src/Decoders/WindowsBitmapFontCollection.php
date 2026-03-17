<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Exceptions\InvalidOperationException;
use Lsa\Font\Finder\Font;

/**
 * Windows Bitmap Font Collection (FON files, Windows)
 *
 * @see New executable (NE) format: https://web.archive.org/web/20070222091910/support.microsoft.com/kb/65122
 * @see MZ format: https://wiki.osdev.org/MZ
 */
class WindowsBitmapFontCollection implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        // MZ / NE detection
        $neOffset = self::getNewExecutableOffset($raw);
        if ($neOffset === null) {
            return false;
        }
        // Check NE header
        $sig = DecoderUtils::unpackString('a2', $raw, $neOffset);
        if ($sig !== 'NE') {
            return false;
        }

        return true;
    }

    /**
     * Finds NE starting offset
     *
     * @param  string  $raw  Raw binary content
     * @return ?int Offset if found, null otherwise.
     */
    protected static function getNewExecutableOffset(string $raw): ?int
    {
        $magicBytes = DecoderUtils::unpackString('a2', $raw, 0);
        switch ($magicBytes) {
            case 'MZ':
                // EXE with DOS stub
                return DecoderUtils::unpackInt('V', $raw, 60);
            case 'NE':
                // No MZ stub
                return 0;
            default:
                return null;
        }
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        // FON New Executable (NE) header layout
        // Offset   Size    Description
        // 0        2       Signature word. "N" is low-order byte. "E" is high-order byte.
        // 2        1       Version number of the linker.
        // 3        1       Revision number of the linker.
        // 4        2       Entry Table file offset, relative to the beginning of the segmented EXE header.
        // 6        2       Number of bytes in the entry table.
        // 8        4       32-bit CRC of entire contents of file. These words are taken as 00 during the calculation.
        // 12       2       Flag word.
        // 14       2       Segment number of automatic data segment.
        // 16       2       Initial size, in bytes, of dynamic heap added to the data segment.
        // 18       2       Initial size, in bytes, of stack added to the data segment.
        // 20       4       Segment number:offset of CS:IP.
        // 24       4       Segment number:offset of SS:SP.
        // 28       2       Number of entries in the Segment Table.
        // 30       2       Number of entries in the Module Reference Table.
        // 32       2       Number of bytes in the Non-Resident Name Table.
        // 34       2       Segment Table file offset, relative to the beginning of the segmented EXE header.
        // 36       2       Resource Table file offset, relative to the beginning of the segmented EXE header.
        // 38       2       Resident Name Table file offset, relative to the beginning of the segmented EXE header.
        // 40       2       Module Reference Table file offset, relative to the beginning of the segmented EXE header.
        // 42       2       Imported Names Table file offset, relative to the beginning of the segmented EXE header.
        // 44       4       Non-Resident Name Table offset, relative to the beginning of the file.
        // 48       2       Number of movable entries in the Entry Table.
        // 50       2       Logical sector alignment shift count, log(base 2) of the segment sector size (default 9).
        // 52       2       Number of resource entries.
        // 54       1       Executable type, used by loader. 02h = WINDOWS
        // 55-63    9       Reserved, currently 0's.
        //
        // FON MZ header layout
        // Offset   Size    Description
        // 0        2       Signature 0x5A4D (ASCII for 'M' and 'Z')
        // 2        2       Extra bytes. Number of bytes in the last page.
        // 4        2       Pages. Number of whole/partial pages.
        // 6        2       Relocation items. Number of entries in the relocation table.
        // 8        2       Header size. The number of paragraphs taken up by the header.
        // 10       2       Minimum allocation.  The number of paragraphs required by the program, excluding the PSP and program image.
        // 12       2       Maximum allocation. The number of paragraphs requested by the program.
        // 14       2       Initial SS. Relocatable segment address for SS.
        // 16       2       Initial SP. Initial value for SP.
        // 18       2       Checksum. When added to the sum of all other words in the file, the result should be zero.
        // 20       2       Initial IP. Initial value for IP.
        // 22       2       Initial CS. Relocatable segment address for CS.
        // 24       2       Relocation table. The (absolute) offset to the relocation table.
        // 26       2       Overlay. Value used for overlay management. If zero, this is the main executable.
        // 28       8       Reserved
        // 36       2       OEM identifier. Defined by name but no other information is given; typically zeroes
        // 38       2       OEM info. Defined by name but no other information is given; typically zeroes
        // 40       20      Reserved
        // 60       4       PE header start. Starting address of the extended header
        $fonts = [];

        // MZ / NE detection, get neOffset
        $neOffset = self::getNewExecutableOffset($raw);
        // Resource table offset
        $tableOffset = ($neOffset + 36);

        // Resource Table offset
        $rsrcOffset = ($neOffset + DecoderUtils::unpackInt('v', $raw, $tableOffset));

        // Resource table layout
        // Offset   Size    Description
        // 0        2       AlignmentShiftCount;
        $alignShift = DecoderUtils::unpackInt('v', $raw, $rsrcOffset);

        // Ressource type walk
        $tableOffset = ($rsrcOffset + 2);
        while (true) {
            // phpcs:disable Squiz.Commenting.InlineComment.SpacingBefore
            // Offset   Size    Description
            // 0        2       Type ID. This is an integer type if the high-order bit is set (8000h);
            //                  otherwise, it is an offset to the type string, the offset is relative to
            //                  the beginning of the resource table. A zero type ID marks the end of the
            //                  resource type information blocks.
            // 2        2       Number of resources for this type.
            // 4        4       Reserved
            // phpcs:enable Squiz.Commenting.InlineComment.SpacingBefore
            $typeID = DecoderUtils::unpackInt('v', $raw, $tableOffset);
            if ($typeID === 0) {
                // Table end
                break;
            }

            $count = DecoderUtils::unpackInt('v', $raw, ($tableOffset + 2));

            // phpcs:disable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
            // phpcs:disable Squiz.PHP.DisallowInlineIf.Found
            // Value is of integer type if TypeID has high-order bit set
            $isIntegerType = ($typeID & 0x8000) !== 0;
            // Remove high-order bit
            $typeVal = ($isIntegerType === true) ? ($typeID & 0x7FFF) : null;
            // Check for font
            $isFontType = ($typeVal === 8) || ($typeID === 8);
            // phpcs:enable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
            // phpcs:enable Squiz.PHP.DisallowInlineIf.Found

            // Walk in entries
            $entryOffset = ($tableOffset + 8);
            for ($i = 0; $i < $count; $i++) {
                // Offset   Size    Description
                // 0        2       File offset to the contents of the resource data, relative to beginning of file.
                // 2        2       Length of the resource in the file (in bytes).
                // 4        2       Flag word. (MOVEABLE, PURE, PRELOAD)
                // 6        2       Resource ID.
                // 8        4       Reserved
                $start = DecoderUtils::unpackInt('v', $raw, ($entryOffset + ($i * 12)));

                if ($isFontType === true) {
                    $fontOffset = ($start << $alignShift);
                    $fonts[] = self::readFntResource($raw, $fontOffset, $filename);
                }
            }

            $tableOffset += (8 + ($count * 12));
        }

        if (empty($fonts) === true) {
            throw new InvalidOperationException('No FONT resources found in FON file');
        }

        return $fonts;
    }

    /**
     * Read an FNT resource
     */
    private static function readFntResource(string $raw, int $offset, string $filename): Font
    {
        // FNT header (BITMAPFONT)
        // Very close to PFMHEADER structure. Equivalent for first 115 bytes, except dfSize is four bytes.
        // Offset   Size    Description
        // 0        2       dfVersion. 2 bytes specifying the version (0200H or 0300H) of the file.
        // 2        4       dfSize. 4 bytes specifying the total size of the file in bytes.
        // 6        60      dfCopyright. 60 bytes specifying copyright information.
        // 66       2       dfType. 2 bytes specifying the type of font file.
        // 68       2       dfPoints. 2 bytes specifying the nominal point size at which this character set looks best.
        // 70       2       dfVertRes. 2 bytes specifying the nominal vertical resolution (dots-per-inch) at which this character set was digitized.
        // 72       2       dfHorizRes. 2 bytes specifying the nominal horizontal resolution (dots-per-inch) at which this character set was digitized.
        // 74       2       dfAscent. 2 bytes specifying the distance from the top of a character definition cell to the baseline of the typographical font.
        // 76       2       dfInternalLeading. Specifies the amount of leading inside the bounds set by dfPixHeight.
        // 78       2       dfExternalLeading. Specifies the amount of extra leading that the designer requests the application add between rows.
        // 80       1       dfItalic. 1 (one) byte specifying whether or not the character definition data represent an italic font.
        // 81       1       dfUnderline. 1 byte specifying whether or not the character definition data represent an underlined font.
        // 82       1       dfStrikeOut. 1 byte specifying whether or not the character definition data represent a struckout font.
        // 83       2       dfWeight. 2 bytes specifying the weight of the characters in the character definition data, on a scale of 1 to 1000.
        // 85       1       dfCharSet. 1 byte specifying the character set defined by this font.
        // 86       2       dfPixWidth. 2 bytes.
        // 88       2       dfPixHeight. 2 bytes specifying the height of the character bitmap (raster fonts), or the height of the grid on which a vector font was digitized.
        // 90       1       dfPitchAndFamily Specifies the pitch and font family.
        // 91       2       dfAvgWidth. 2 bytes specifying the width of characters in the font.
        // 93       2       dfMaxWidth. 2 bytes specifying the maximum pixel width of any character in the font.
        // 95       1       dfFirstChar. 1 byte specifying the first character code defined by this font.
        // 96       1       dfLastChar. 1 byte specifying the last character code defined by this font.
        // 97       1       dfDefaultChar. 1 byte specifying the character to substitute whenever a string contains a character out of the range.
        // 98       1       dfBreakChar. 1 byte specifying the character that will define word breaks.
        // 99       2       dfWidthBytes. 2 bytes specifying the number of bytes in each row of the bitmap.
        // 101      4       dfDevice. 4 bytes specifying the offset in the file to the string giving the device name. For a generic font, this value is zero.
        // 105      4       dfFace. 4 bytes specifying the offset in the file to the null-terminated string that names the face.
        // 109      4       dfBitsPointer. 4 bytes specifying the absolute machine address of the bitmap.
        // 113      4       dfBitsOffset. 4 bytes specifying the offset in the file to the beginning of the bitmap information.
        // 117      1       dfReserved. 1 byte, not used.
        // 118      4       dfFlags. 4 bytes specifying the bits flags, which are additional flags that define the format of the Glyph bitmap.
        // 122      2       dfAspace. 2 bytes specifying the global A space, if any.
        // 124      2       dfBspace. 2 bytes specifying the global B space, if any.
        // 126      2       dfCspace. 2 bytes specifying the global C space, if any.
        // 128      4       dfColorPointer. 4 bytes specifying the offset to the color table for color fonts, if any.
        // 132      16      dfReserved1. 16 bytes, not used.
        $dfItalic = ord($raw[($offset + 80)]);
        $dfWeight = DecoderUtils::unpackInt('v', $raw, ($offset + 83));
        $dfFace = DecoderUtils::unpackInt('V', $raw, ($offset + 105));

        // phpcs:disable Squiz.Commenting.InlineComment.SpacingBefore
        // Family name : dfFace is an offset
        // <facename>     An ASCII character string specifying the name of the
        //                font face. The size of this field is the length of the
        //                string plus a NULL terminator.
        // phpcs:enable Squiz.Commenting.InlineComment.SpacingBefore
        [$family] = DecoderUtils::readNullPaddedString($raw, ($offset + $dfFace));

        // phpcs:disable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
        $italic = ($dfItalic !== 0);
        $weight = $dfWeight;
        // No real bold flag in FNT
        $bold = ($weight >= 700);
        // phpcs:enable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison

        // Fallbacks: weight and italic are sometimes defined not by headers but by file name.
        $lowerFile = strtolower(basename($filename));

        if (
            \str_ends_with($lowerFile, 'b.fon') === true
            || \str_ends_with($lowerFile, 'bd.fon') === true
            || str_contains($lowerFile, 'bold') === true
        ) {
            $bold = true;
        }

        if (
            \str_ends_with($lowerFile, 'i.fon') === true
            || \str_ends_with($lowerFile, 'it.fon') === true
            || str_contains($lowerFile, 'italic') === true
        ) {
            $italic = true;
        }

        return new Font([
            'filename' => $filename,
            'weight' => $weight,
            'italic' => $italic,
            'bold' => $bold,
            'name' => $family,
        ]);
    }
}
