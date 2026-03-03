<?php

namespace Lsa\Font\Finder;

use Exception;
use Lsa\Font\Finder\Decoders\TrueTypeFont;
use Lsa\Font\Finder\Decoders\WebOpenFontFormat;
use Lsa\Font\Finder\Decoders\WebOpenFontFormat2;
use RuntimeException;

class FontDecoder
{
    public function extractFontMeta(string $path): array
    {
        $raw = file_get_contents($path);

        // Format detection
        $signature = substr($raw, 0, 4);

        switch ($signature) {
            case "\x00\x01\x00\x00": // TTF
            case "OTTO": // OTF CFF
            case "true": // Apple TTF
            case "typ1": // CFF Type 1
                return TrueTypeFont::extractFontMeta($raw, $path);
            case "wOFF":
                return WebOpenFontFormat::extractFontMeta($raw, $path);
            case "wOF2":
                return WebOpenFontFormat2::extractFontMeta($raw, $path);
            default:
                // FON, bitmap, corrupted, etc.
                throw new RuntimeException("Unknown file format");
        }
    }
}
