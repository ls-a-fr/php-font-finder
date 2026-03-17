<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Contracts;

use Lsa\Font\Finder\Exceptions\InvalidOperationException;
use Lsa\Font\Finder\Font;

interface FontDecoder
{
    /**
     * Validates if this decoder can be executed on specified binary content.
     *
     * @param  string  $raw  Raw binary content
     * @return bool True if this decoder can be executed, false otherwise.
     */
    public static function canExecute(string $raw): bool;

    /**
     * Extracts metadata from font file
     *
     * @return Font[] Fonts found in font file
     *
     * @throws InvalidOperationException Invalid file. You may not have called canExecute, or this file is corrupted
     */
    public static function extractFontMeta(string $raw, string $filename): array;
}
