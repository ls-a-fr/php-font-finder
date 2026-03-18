<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Exceptions\NonReadableContentException;
use Lsa\Font\Finder\Exceptions\RuntimeException;
use Lsa\Font\Finder\Platform\SystemInformation;
use Symfony\Component\Process\Process;

/**
 * Web Open Font Format 2 files (WOFF2)
 *
 * @see https://www.w3.org/TR/WOFF2/
 */
class WebOpenFontFormat2 implements FontDecoder
{
    /**
     * Cached WOFF2 path, based on current system information
     */
    private static ?string $cachedWoff2Path = null;

    public static function canExecute(string $raw): bool
    {
        $signature = \substr($raw, 0, 4);

        return $signature === 'wOF2';
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        $raw = self::decodeWoff2($raw);

        return TrueTypeFont::extractFontMeta($raw, $filename);
    }

    /**
     * Utility method to decode WOFF2
     *
     * @param  string  $raw  Raw binary content
     * @return string Decoded TrueTypeFont
     *
     * @throws RuntimeException Could not execute woff2_decompress
     * @throws NonReadableContentException Could not find TTF file, or read TTF file
     */
    protected static function decodeWoff2(string $raw): string
    {
        // Create a temporary file, because woff2_decompress does not allow direct stream input
        $tempFile = tempnam(sys_get_temp_dir(), 'lff');
        \file_put_contents($tempFile, $raw);

        $process = self::runWoff2Decompress($tempFile);
        if ($process->isSuccessful() === false) {
            throw new RuntimeException('Could not execute woff2_decompress: ' . $process->getErrorOutput());
        }

        // Rebuild output file name
        $ttfFile = $tempFile . '.ttf';
        if (\str_contains($tempFile, '.') === true) {
            $ttfFileParts = explode('.', $tempFile);
            \array_pop($ttfFileParts);
            $ttfFileParts[] = 'ttf';
            $ttfFile = implode('.', $ttfFileParts);
        }

        if (\file_exists($ttfFile) === false) {
            throw new NonReadableContentException('Could not find ttf file');
        }

        // Get contents and clean temporary files
        $ttfContents = \file_get_contents($ttfFile);

        if ($ttfContents === false) {
            throw new NonReadableContentException('Could not get ttf contents, check your permissions');
        }

        unlink($tempFile);
        unlink($ttfFile);

        return $ttfContents;
    }

    protected static function runWoff2Decompress(string $tempFile): Process
    {
        // Create shell command
        $process = new Process([
            self::getWoff2Executable(),
            $tempFile,
        ]);

        $process->run();
        return $process;
    }

    /**
     * Utility method to build woff2_decompress executable absolute path in this library.
     * This path is cached to prevent multiple calls to `realpath` and `SystemInformation::getCurrent()`.
     *
     * @return string Woff2 executable path
     *
     * @throws RuntimeException Util woff2_decompress could not be found
     */
    protected static function getWoff2Executable(): string
    {
        // Get cached path if exists
        if (self::$cachedWoff2Path !== null) {
            return self::$cachedWoff2Path;
        }

        // Get current system to get path, plus suffix woff2_decompress for Windows
        $sysInfo = SystemInformation::getCurrent();
        $executableName = 'woff2_decompress';
        if ($sysInfo->isWindows() === true) {
            $executableName .= '.exe';
        }
        // Build path
        $path = implode(\DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            '..',
            'deps',
            $sysInfo->getValue(SystemInformation::FORMAT_DEPS),
            $executableName,
        ]);

        // Check path exists
        $realpath = \realpath($path);
        if ($realpath === false) {
            throw new RuntimeException('Util woff2_decompress could not be found, check your install. Path: ' . $path);
        }

        // Check and maybe change permissions before storing cachedWoff2Path
        if (\is_executable($realpath) === false && SystemInformation::getCurrent()->isWindows() === false) {
            // We have nothing to lose here
            $process = new Process([
                "chmod",
                "+x",
                $realpath
            ]);
            $process->run();
            if($process->isSuccessful() === false) {
                throw new RuntimeException('Util woff2_decompress is not executable, check your install. Path: ' . $realpath);
            }
        }

        self::$cachedWoff2Path = $realpath;

        return self::$cachedWoff2Path;
    }
}
