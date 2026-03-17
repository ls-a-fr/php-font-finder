<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Contracts;

interface MetadataParser
{
    /**
     * Lists all known extensions for this parser
     *
     * @return string[] Known extensions
     */
    public static function getExtensions(): array;

    /**
     * Parses metadata file
     *
     * @param  string  $raw  Raw binary content
     * @return array{
     *     name?: non-empty-string,
     *     weight?: int,
     *     italic?: bool
     * } Various information in this metadata file
     */
    public static function parse(string $raw): array;
}
