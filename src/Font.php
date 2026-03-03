<?php

namespace Lsa\Font\Finder;

class Font {
    public readonly string $filename;
    public readonly int $weight;
    public readonly bool $italic;
    public readonly bool $bold;
    public readonly string $name;
    
    public function __construct(array $fontInfo)
    {
        $this->filename = str_replace('/', \DIRECTORY_SEPARATOR, $fontInfo['filename']);
        $this->weight = $fontInfo['weight'];
        $this->italic = $fontInfo['italic'];
        $this->bold = $fontInfo['bold'];
        $this->name = $fontInfo['name'];
    }
}