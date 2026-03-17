<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders\Metadata;

use Lsa\Font\Finder\Contracts\MetadataParser;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Exceptions\InvalidOperationException;

/**
 * PrinterFontMetrics contains font metric information.
 *
 * @see https://library.thedatadungeon.com/msdn-1992-09/ddk31/html/ddk34qll.htm
 */
class PrinterFontMetrics implements MetadataParser
{
    public static function getExtensions(): array
    {
        return ['pfm'];
    }

    public static function parse(string $raw): array
    {
        // PFM Header layout
        // Note: The PFMHEADER structure is identical to the Windows 2.x FONTINFO structure
        // Offset   Size    Description
        // 0        2       dfVersion
        // 2        2       dfSize
        // 4        60      dfCopyright
        // 64       2       dfType
        // 66       2       dfPoints
        // 68       2       dfVertRes
        // 70       2       dfHorizRes
        // 72       2       dfAscent
        // 74       2       dfInternalLeading
        // 76       2       dfExternalLeading
        // 78       1       dfItalic
        // 79       1       dfUnderline
        // 80       1       dfStrikeOut
        // 81       2       dfWeight
        // 83       1       dfCharSet
        // 84       2       dfPixWidth
        // 86       2       dfPixHeight
        // 88       1       dfPitchAndFamily
        // 89       2       dfAvgWidth
        // 91       2       dfMaxWidth
        // 93       1       dfFirstChar
        // 94       1       dfLastChar
        // 95       1       dfDefaultChar
        // 96       1       dfBreakChar
        // 97       2       dfWidthBytes
        // 99       4       dfDevice
        // 103      4       dfFace
        // 107      4       dfBitsPointer
        // 111      4       dfBitsOffset
        //
        // Note that you also may encounter FONTDIRENTRY header:
        // Offset   Size    Description
        // 0        2       dfVersion;
        // 2        4       dfSize;
        // 6        60      dfCopyright[60];
        // 66       2       dfType;
        // 68       2       dfPoints;
        // 70       2       dfVertRes;
        // 72       2       dfHorizRes;
        // 74       2       dfAscent;
        // 76       2       dfInternalLeading;
        // 78       2       dfExternalLeading;
        // 80       1       dfItalic;
        // 81       1       dfUnderline;
        // 82       1       dfStrikeOut;
        // 83       2       dfWeight;
        // 85       1       dfCharSet;
        // 86       2       dfPixWidth;
        // 88       2       dfPixHeight;
        // 90       1       dfPitchAndFamily;
        // 91       2       dfAvgWidth;
        // 93       2       dfMaxWidth;
        // 95       1       dfFirstChar;
        // 96       1       dfLastChar;
        // 97       1       dfDefaultChar;
        // 98       1       dfBreakChar;
        // 99       2       dfWidthBytes;
        // 101      4       dfDevice;
        // 105      4       dfFace;
        // 109      4       dfReserved;
        //
        // Check PFM format: PFMHEADER or FONTDIRENTRY:
        $len = strlen($raw);
        $sizeDword = DecoderUtils::unpackInt('V', $raw, 2);
        $sizeWord = DecoderUtils::unpackInt('v', $raw, 2);

        if ($sizeDword === $len) {
            // FONTDIRENTRY
            $dfItalicOffset = 80;
            $dfWeightOffset = 83;
            $dfFaceOffset = 105;
            $minLength = 113;
        } elseif ($sizeWord === $len) {
            // PFMHEADER
            $dfItalicOffset = 78;
            $dfWeightOffset = 81;
            $dfFaceOffset = 103;
            $minLength = 115;
        } else {
            throw new InvalidOperationException('Invalid PFM Header, or corrupted file');
        }

        $meta = [];
        // PFM header is at least that length bytes long
        if ($len < $minLength) {
            return $meta;
        }

        // Get dfItalic offset (2 bytes, little-endian)
        $italic = ord($raw[$dfItalicOffset]);
        // phpcs:disable Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
        $meta['italic'] = ($italic !== 0);

        // Get dfWeight offset (2 bytes, little-endian)
        $weight = DecoderUtils::unpackInt('v', $raw, $dfWeightOffset);
        if ($weight > 0) {
            $meta['weight'] = $weight;
        }

        // Get dfFace offset (4 bytes, little-endian)
        // Points to char FaceName[] field later in file
        $faceOffset = DecoderUtils::unpackInt('V', $raw, $dfFaceOffset);
        if ($faceOffset > 0 && $faceOffset < $len) {
            $name = '';
            for ($i = $faceOffset; $i < $len; $i++) {
                if ($raw[$i] === "\0") {
                    break;
                }
                $name .= $raw[$i];
            }
            if ($name !== '') {
                $meta['name'] = $name;
            }
        }

        return $meta;
    }
}
