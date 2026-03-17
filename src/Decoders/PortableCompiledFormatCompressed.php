<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\ZlibDecoder;
use Lsa\Font\Finder\Exceptions\ZlibException;

/**
 * PortableCompilerFormat, but Gunzipped
 */
class PortableCompiledFormatCompressed implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        try {
            $decoded = ZlibDecoder::decode($raw);

            return PortableCompiledFormat::canExecute($decoded);
        } catch (ZlibException) {
            // If file is not compressed, return false
            return false;
        }
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        $decoded = ZlibDecoder::decode($raw);

        return PortableCompiledFormat::extractFontMeta($decoded, $filename);
    }
}
