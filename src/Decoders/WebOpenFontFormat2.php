<?php

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\BinaryReader;
use RuntimeException;
use Symfony\Component\Process\Process;

class WebOpenFontFormat2
{
    public static function extractFontMeta(string $raw): array
    {
        $raw = self::decodeWoff2($raw);
        return TrueTypeFont::extractFontMeta($raw);
    }

    private static function decodeWoff2(string $raw) : string
    {
        switch (PHP_OS_FAMILY) {
            case 'Windows':
                return self::windowsDecodeWoff2($raw);
            case 'Darwin':
            case 'Linux':
            case 'Solaris':
            case 'BSD':
                return self::unixDecodeWoff2($raw);
            default:
                return [];
        }
    }

    private static function windowsDecodeWoff2(string $raw): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'font-finder') . '.woff2';
        \file_put_contents($tempFile, $raw);

        $process = new Process([
            'cmd.exe', 
            '/c',
            self::getBuildFolder() . \DIRECTORY_SEPARATOR . 'woff2_decompress.exe',
            $tempFile
        ]);
        
        $process->run();

        if ($process->isSuccessful() === true) {
            return $tempFile . '.ttf';
        }
    }

    private static function getBuildFolder() : string
    {
        $path = realpath(implode(\DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            '..',
            'build'
        ]));
        if($path === false) {
            throw new RuntimeException('Build folder could not be found, check your install');
        }
        return $path;
    }

}