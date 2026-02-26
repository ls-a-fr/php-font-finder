<?php

namespace Lsa\Font\Finder;

class Font {
    public readonly string $filename;
    public readonly int $weight;
    public readonly bool $italic;
    public readonly bool $bold;
    
    public function __construct(array $fontInfo)
    {
        $this->filename = $fontInfo['filename'];
        $this->weight = $fontInfo['weight'];
        $this->italic = $fontInfo['italic'];
        $this->bold = $fontInfo['bold'];
    }
}