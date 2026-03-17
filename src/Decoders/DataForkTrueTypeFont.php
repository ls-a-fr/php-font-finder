<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;

/**
 * DFONT files
 *
 * @see https://en.wikipedia.org/wiki/Fonts_on_Macintosh#Mac_OS_X_/_macOS
 */
class DataForkTrueTypeFont implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        // DFONT header layout
        // Offset  Size  Description
        // 0       4     dataOffset
        // 4       4     mapOffset
        // 8       4     dataLength
        // 12      4     mapLength
        if (strlen($raw) < 16) {
            return false;
        }

        $dataOffset = DecoderUtils::unpackInt('N', $raw, 0);
        $mapOffset = DecoderUtils::unpackInt('N', $raw, 4);
        $mapLength = DecoderUtils::unpackInt('N', $raw, 12);

        // Check offsets consistency
        if ($dataOffset <= 0) {
            return false;
        }

        if ($mapOffset <= $dataOffset) {
            return false;
        }

        // Check this file actually contains a font. It also helps detection, because 16 bytes
        // may create false-positives.
        $map = substr($raw, $mapOffset, $mapLength);

        if (
            strpos($map, 'sfnt') !== false
            || strpos($map, 'FONT') !== false
            || strpos($map, 'FOND') !== false
        ) {
            return true;
        }

        return false;
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        $fonts = [];

        $dataOffset = DecoderUtils::unpackInt('N', $raw, 0);
        $mapOffset = DecoderUtils::unpackInt('N', $raw, 4);

        // Map layout (relative to mapOffset):
        // Offset  Size  Description
        // 0       16    header
        // 16      2     reserved
        // 18      2     reserved
        // 20      2     reserved
        // 22      2     reserved
        // 24      2     type list offset
        // 26      2     name list offset
        //
        // TypeList offset (relative to map start)
        $typeListOffset = DecoderUtils::unpackInt('n', $raw, ($mapOffset + 24));
        $typeListPos = ($mapOffset + $typeListOffset);

        // Jump to TypeList
        // TypeList type layout:
        // 0–1 : typeCountMinus1
        $typeCount = (DecoderUtils::unpackInt('n', $raw, $typeListPos) + 1);

        $offset = ($typeListPos + 2);
        for ($i = 0; $i < $typeCount; $i++) {
            // DFONT Resource type layout:
            // Offset  Size  Description
            // 0       4     resource type (ex: 'sfnt', 'FOND', 'FONT')
            // 4       2     resource count (minus 1)
            // 6       2     resource list offset
            $type = DecoderUtils::unpackString('a4', $raw, $offset);
            $offset += 4;

            // Skip resource count
            $resourceCount = (DecoderUtils::unpackInt('n', $raw, $offset) + 1);
            $offset += 2;
            $resourceListOffset = DecoderUtils::unpackInt('n', $raw, $offset);
            $offset += 2;

            if ($type !== 'sfnt') {
                continue;
            }

            // Jump to ResourceList
            $listOffset = ($typeListPos + $resourceListOffset);

            for ($r = 0; $r < $resourceCount; $r++) {
                // RefOffset: start of this ResourceList
                $refOffset = ($listOffset + ($r * 12));
                // ResourceReference (12 bytes)
                // Offset  Size  Description
                // 0       2     resource ID (signed int16)
                // 2       2     nameOffset (relative to name list, or 0xFFFF if nameless)
                // 4       1     attributes (bitfield)
                // 5       3     dataOffset (24 bits, relative to data zone)
                // 8       4     reserved (always 0)
                // Skip resource ID, nameOffset and attributes. We only need dataOffset
                // Data offset is 24-bit not 16, not 32: resolving with ord and byte resolution
                // phpcs:disable Squiz.Formatting.OperatorBracket.MissingBrackets
                // phpcs:disable Squiz.WhiteSpace.OperatorSpacing.SpacingBefore
                $dataOffset24
                    = (ord($raw[($refOffset + 5)]) << 16)
                    | (ord($raw[($refOffset + 6)]) << 8)
                    | (ord($raw[($refOffset + 7)]));
                // phpcs:enable Squiz.Formatting.OperatorBracket.MissingBrackets
                // phpcs:enable Squiz.WhiteSpace.OperatorSpacing.SpacingBefore

                // Jump to DataBlock
                $dataBlockOffset = ($dataOffset + $dataOffset24);

                // DataBlock
                // Offset  Size  Description
                // 0       4     dataSize (big endian)
                // 4       N     data bytes
                $dataSize = DecoderUtils::unpackInt('N', $raw, $dataBlockOffset);
                $signature = DecoderUtils::unpackString('a4', $raw, ($dataBlockOffset + 4));

                if (TrueTypeCollection::canExecute($signature) === true) {
                    $fonts = \array_merge($fonts, TrueTypeCollection::extractFontMeta(
                        // TTC font binary content
                        \substr($raw, ($dataBlockOffset + 4), $dataSize),
                        $filename
                    ));
                } elseif (TrueTypeFont::canExecute($signature) === true) {
                    $fonts = \array_merge($fonts, TrueTypeFont::extractFontMeta(
                        // TTF font binary content
                        \substr($raw, ($dataBlockOffset + 4), $dataSize),
                        $filename
                    ));
                }
            }
        }

        return $fonts;
    }
}
