<?php

namespace Lsa\Font\Finder;

use DirectoryIterator;
use Lsa\Font\Finder\Platform\Bsd;
use Lsa\Font\Finder\Platform\Darwin;
use Lsa\Font\Finder\Platform\Solaris;
use Lsa\Font\Finder\Platform\Unix;
use Lsa\Font\Finder\Platform\Windows;
use RuntimeException;

class FontFileFinder
{
    private bool $autoDetect = false;
    private array $directories = [];

    private function __construct()
    {
        
    }

    public static function init() {
        return new self();
    }

    public function addSystemFonts(): FontFileFinder
    {
        $this->autoDetect = true;
        return $this;
    }

    public function addDirectory(string $directory): FontFileFinder
    {
        if(!\file_exists($directory)) {
            throw new RuntimeException('Directory ' . $directory . ' does not seem to exist');
        }
        if(!\is_dir($directory)) {
            throw new RuntimeException('Path ' . $directory . ' is not a directory');
        }
        $this->directories = \array_unique($this->directories);
        return $this;
    }

    public function addDirectoryRecursive(string $directory): FontFileFinder
    {
        $this->directories = \array_unique($this->doAddDirectoryRecursive($directory));
        return $this;
    }

    private function doAddDirectoryRecursive(string $directory): array
    {
        $directory = rtrim($directory, \DIRECTORY_SEPARATOR);
        $it = new DirectoryIterator($directory);

        $directories = [];
        foreach($it as $fileInfo) {
            if($fileInfo->isDot()) {
                continue;
            }
            if($fileInfo->isDir()) {
                $directories = array_merge(
                    $directories, 
                    [$directory . \DIRECTORY_SEPARATOR . $fileInfo->getFilename()],
                    $this->doAddDirectoryRecursive(
                        $directory . \DIRECTORY_SEPARATOR . $fileInfo->getFilename()
                    )
                );
            }
        }
        return $directories;
    }

    public function get(): array
    {
        $systemFonts = ($this->autoDetect === true ? self::getSystemFonts() : []);
        
        $fontFiles = [];
        var_dump($this->directories);
        foreach ($this->directories as $directory) {
            $fontFiles = \array_merge($fontFiles, self::getFontsInDirectory($directory));
        }

        return \array_merge($systemFonts, self::extractFontsMetadata($fontFiles));
    }

    public static function load(array $config): array
    {
        $autoDetect = $config['autoDetect'] ?? false;
        $directories = $config['directories'] ?? [];

        $inst = new self();
        if($autoDetect === true) {
            $inst->addSystemFonts();
        }
        foreach($directories as $directory) {
            if(\is_string($directory)) {
                $inst->addDirectory($directory);
            } else if(\is_array($directory) && $directory['recursive'] ?? false) {
                $inst->addDirectoryRecursive($directory['path']);
            } else {
                throw new RuntimeException('Invalid directory supplied');
            }
        }
        return $inst->get();
    }

    private static function getFontsInDirectory(string $directory): array
    {
        $fontFiles = [];
        $iterator = new DirectoryIterator($directory);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $fontFiles[] = $directory . \DIRECTORY_SEPARATOR . $fileInfo->getFilename();
        }
        return $fontFiles;
    }

    private static function extractFontsMetadata(array $fontFiles): array
    {
        $fonts = [];
        foreach($fontFiles as $fontFile) {
            $fd = new FontDecoder();
            try {
                [$family, $weight, $italic, $bold] = $fd->extractFontMeta($fontFile);
                if(!isset($fonts[$family])) {
                    $fonts[$family] = [];
                }
                $fonts[$family][] = new Font([
                    'filename' => $fontFile,
                    'weight' => $weight,
                    'italic' => $italic,
                    'bold' => $bold
                ]);
            } catch(RuntimeException) {
                continue;
            }
        }
        return $fonts;
    }

    public static function getSystemFonts(): array
    {
        $fontFiles = [];
        $directories = self::getFontDirectories();
        foreach ($directories as $directory) {
            $fontFiles = \array_merge($fontFiles, self::getFontsInDirectory($directory));
        }

        return self::extractFontsMetadata($fontFiles);
    }

    protected static function getFontDirectories(): array
    {
        switch (PHP_OS_FAMILY) {
            case 'Windows':
                return Windows::getFontDirectories();
            case 'Darwin':
                return Darwin::getFontDirectories();
            case 'Linux':
                return Unix::getFontDirectories();
            case 'Solaris':
                return Solaris::getFontDirectories();
            case 'BSD':
                return Bsd::getFontDirectories();
            default:
                return [];
        }
    }
}
