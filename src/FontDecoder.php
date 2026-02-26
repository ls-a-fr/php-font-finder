<?php

namespace Lsa\Font\Finder;

use Lsa\Font\Finder\Decoders\TrueTypeFont;
use Lsa\Font\Finder\Decoders\WebOpenFontFormat;
use RuntimeException;

class FontDecoder
{
    public function extractFontMeta(string $path): array
    {
        $raw = file_get_contents($path);

        // Format detection
        $signature = substr($raw, 0, 4);

        if ($signature === "OTTO" || $signature === "\x00\x01\x00\x00") {
            // TTF/OTF
            return TrueTypeFont::extractFontMeta($raw);
        } elseif ($signature === "wOFF") {
            // WOFF
            return WebOpenFontFormat::extractFontMeta($raw);
        } else {
            // FON, bitmap, corrupted, etc.
            throw new RuntimeException("Unknown file format");
        }
    }
}
