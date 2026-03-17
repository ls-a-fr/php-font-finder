<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Decoders\Lib\TrueTypeUtils;
use Lsa\Font\Finder\Exceptions\InvalidOperationException;
use Lsa\Font\Finder\Font;

/**
 * Help from https://github.com/mu2019/getfontname
 */
class TrueTypeCollection implements FontDecoder
{
    // Hack for old Microsoft Chinese fonts
    public const LOCALMAP = ['cp936' => 'zh_cn'];

    /**
     * Unpack format for a FONTENTRY
     */
    public const FONT_ENTRY_FORMAT_STR = 'nmajor/nminor/nnumTables/nsearchRange/nentrySelector/nrangeShift';

    /**
     * Unpack format for a TABLEENTRY
     */
    public const TABLE_ENTRY_FORMAT_STR = 'a4tag/Nchecksum/Noffset/Nlength';

    /**
     * Unpack format for a NAMETABLE header
     */
    public const NAME_TABLE_HEADER_FORMAT_STR = 'nselector/ncount/nstringOffset';

    /**
     * Unpack format for a NAMERECORD
     */
    public const NAME_RECORD_FORMAT_STR = 'nplatformID/nencodingID/nlangID/nnameID/nlength/noffset';

    /**
     * Unpack format for a TrueTypeCollection header
     */
    public const TTC_HEADER_FMT = 'A4tag/Nmver/Nnumfonts';

    /**
     * NameID map for readability
     */
    public const FONTNAMEMAP = [
        'Copyright' => 0,
        'Family' => 1,
        'SubFamily' => 2,
        'Id' => 3,
        'FullName' => 4,
        'Version' => 5,
        'PostscriptName' => 6,
        'TradeMark' => 7,
        'Manufacturer' => 8,
        'Designer' => 9,
        'TypographicName' => 16,
        'TypographicSubName' => 17,
    ];

    public static function canExecute(string $raw): bool
    {
        return \substr($raw, 0, 4) === 'ttcf';
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        if (substr($raw, 0, 4) !== 'ttcf') {
            throw new InvalidOperationException('Invalid TTC file. Did you call canExecute first?');
        }

        // Create tables
        $fontOffset = DecoderUtils::calcsize(self::TTC_HEADER_FMT);
        $info = DecoderUtils::unpackArray(self::TTC_HEADER_FMT, $raw);

        $major = (((int) $info['mver']) >> 16);

        if ($major !== 1 && $major !== 2) {
            throw new InvalidOperationException('Could not detect valid version in TTC file');
        }

        $fonts = [];
        for ($i = 0; $i < $info['numfonts']; $i++) {
            $taboffset = DecoderUtils::unpackInt('N', $raw, $fontOffset);
            $fontEntry = DecoderUtils::unpackArray(self::FONT_ENTRY_FORMAT_STR, $raw, $taboffset);

            $tables = TrueTypeUtils::getTables(
                $raw,
                ((int) $fontEntry['numTables']),
                ($taboffset + DecoderUtils::calcsize(self::FONT_ENTRY_FORMAT_STR))
            );

            if (TrueTypeUtils::isRealFont($tables) === false) {
                $fontOffset += 4;

                continue;
            }

            $fonts[] = new Font([
                'filename' => $filename,
                ...TrueTypeUtils::getTrueTypeInformation($tables, $raw),
            ]);
            $fontOffset += 4;
        }

        return $fonts;
    }
}
