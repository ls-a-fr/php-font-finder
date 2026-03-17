<?php

declare(strict_types=1);

namespace Lsa\Font\Finder;

/**
 * Main object to store decoder results
 */
class Font
{
    /**
     * Absolute path to file
     */
    public readonly string $filename;

    /**
     * Weight in CSS-style (100: Ultra-Light, 900: Black)
     */
    public readonly int $weight;

    /**
     * Is this font declared as italic
     */
    public readonly bool $italic;

    /**
     * Is this font declared as bold
     */
    public readonly bool $bold;

    /**
     * Font family name
     */
    public readonly string $name;

    /**
     * Creates a new Font object
     *
     * @param  array{filename:string,weight:int,italic:bool,bold:bool,name:string}  $fontInfo  Font information
     */
    public function __construct(array $fontInfo)
    {
        $this->filename = str_replace('/', \DIRECTORY_SEPARATOR, $fontInfo['filename']);
        $this->weight = $fontInfo['weight'];
        $this->italic = $fontInfo['italic'];
        $this->bold = $fontInfo['bold'];
        $this->name = $fontInfo['name'];
    }
}
