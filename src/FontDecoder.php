<?php

declare(strict_types=1);

namespace Lsa\Font\Finder;

use Lsa\Font\Finder\Decoders\BitstreamSpeedo;
use Lsa\Font\Finder\Decoders\BsdVgaFont;
use Lsa\Font\Finder\Decoders\CompactFontFormat;
use Lsa\Font\Finder\Decoders\CompactFontFormatPostScript;
use Lsa\Font\Finder\Decoders\DataForkTrueTypeFont;
use Lsa\Font\Finder\Decoders\EmbeddedOpenType;
use Lsa\Font\Finder\Decoders\GlyphBitmapDistributionFormat;
use Lsa\Font\Finder\Decoders\JimHersheyFont;
use Lsa\Font\Finder\Decoders\Metadata\AdobeFontMetrics;
use Lsa\Font\Finder\Decoders\Metadata\PrinterFontMetrics;
use Lsa\Font\Finder\Decoders\OpenTypeBitmap;
use Lsa\Font\Finder\Decoders\OpenTypeCollection;
use Lsa\Font\Finder\Decoders\OpenTypeFont;
use Lsa\Font\Finder\Decoders\PcScreenFont;
use Lsa\Font\Finder\Decoders\PcScreenFontCompressed;
use Lsa\Font\Finder\Decoders\PcScreenFontUnicode;
use Lsa\Font\Finder\Decoders\PcScreenFontUnicodeCompressed;
use Lsa\Font\Finder\Decoders\PortableCompiledFormat;
use Lsa\Font\Finder\Decoders\PortableCompiledFormatCompressed;
use Lsa\Font\Finder\Decoders\PostScript;
use Lsa\Font\Finder\Decoders\PrinterFontAscii;
use Lsa\Font\Finder\Decoders\PrinterFontBinary;
use Lsa\Font\Finder\Decoders\ScalableVectorGraphics;
use Lsa\Font\Finder\Decoders\ScalableVectorGraphicsCompressed;
use Lsa\Font\Finder\Decoders\TrueTypeCollection;
use Lsa\Font\Finder\Decoders\TrueTypeFont;
use Lsa\Font\Finder\Decoders\Type1;
use Lsa\Font\Finder\Decoders\WebOpenFontFormat;
use Lsa\Font\Finder\Decoders\WebOpenFontFormat2;
use Lsa\Font\Finder\Decoders\WindowsBitmapFontCollection;
use Lsa\Font\Finder\Exceptions\InvalidOperationException;
use Lsa\Font\Finder\Exceptions\NonReadableContentException;

/**
 * FontDecoder loads any registered decoder and extract font metadata from files
 */
class FontDecoder
{
    /**
     * Performance metrics data
     *
     * @var array{
     *  meta?:array{
     *      all:int|float,
     *      methods:array<string,float>,
     *      count:int
     *  },
     *  classes?:array<class-string,array<string,array{count:int,average:int|float}>>
     * }
     */
    protected static array $performanceMetrics = [];

    /**
     * Memory limit for this execution
     */
    private static ?int $memoryLimit = null;

    /**
     * Flag: should enable performance metrics
     */
    private static bool $enableMetrics = false;

    /**
     * Enable performance metrics. Will slow a little FontDecoder
     */
    public static function enablePerformanceMetrics(): void
    {
        self::$enableMetrics = true;
    }

    /**
     * Disable performance metrics. Default
     */
    public static function disablePerformanceMetrics(): void
    {
        self::$enableMetrics = false;
    }

    /**
     * Extract font metadata based on an absolute path
     *
     * @return Font[]
     *
     * @throws NonReadableContentException File not found
     * @throws InvalidOperationException No decoder found for this file
     */
    public static function extractFontMeta(string $path): array
    {
        // Ignored files are metadata files
        if (static::shouldIgnoreFile($path) === true) {
            return [];
        }

        // Normalize path
        $path = \realpath($path);
        if ($path === false || \file_exists($path) === false) {
            throw new NonReadableContentException('File does not exist');
        }

        // Get file contents
        $raw = static::getRawContentsFromFile($path);

        $decoders = static::getDecoders();

        foreach ($decoders as $className) {
            if (self::$enableMetrics === true) {
                $time = microtime(true);
            }
            $canExecute = $className::canExecute($raw);
            if (self::$enableMetrics === true) {
                /**
                 * Variable $time is always defined in this case.
                 *
                 * @phpstan-ignore variable.undefined
                 */
                static::pushPerformanceMetric($className, 'canExecute', $time);
            }

            if ($canExecute === false) {
                continue;
            }

            if (self::$enableMetrics === true) {
                $time = microtime(true);
            }
            $result = $className::extractFontMeta($raw, $path);
            if (self::$enableMetrics === true) {
                /**
                 * Variable $time is always defined in this case.
                 *
                 * @phpstan-ignore variable.undefined
                 */
                static::pushPerformanceMetric($className, 'extractFontMeta', $time);
            }

            return $result;
        }
        throw new InvalidOperationException('Unknown file format');
    }

    /**
     * Get available decoders.
     *
     * @return class-string<\Lsa\Font\Finder\Contracts\FontDecoder>[]
     */
    protected static function getDecoders()
    {
        /**
         * Note: you may wish to reorder these decoders to increase performance.
         * If you wish to, you may create a subclass of FontDecoder
         */
        return [
            JimHersheyFont::class,
            TrueTypeCollection::class,
            TrueTypeFont::class,
            WebOpenFontFormat::class,
            WebOpenFontFormat2::class,
            WindowsBitmapFontCollection::class,
            BsdVgaFont::class,
            BitstreamSpeedo::class,
            EmbeddedOpenType::class,
            PrinterFontBinary::class,
            PortableCompiledFormat::class,
            PcScreenFont::class,
            PcScreenFontUnicode::class,
            CompactFontFormat::class,
            CompactFontFormatPostScript::class,
            DataForkTrueTypeFont::class,
            ScalableVectorGraphics::class,
            GlyphBitmapDistributionFormat::class,
            PrinterFontAscii::class,
            // Compressed files at the end as ZlibDecoder can be slow
            PcScreenFontCompressed::class,
            PortableCompiledFormatCompressed::class,
            PcScreenFontUnicodeCompressed::class,
            ScalableVectorGraphicsCompressed::class,
            // Already handled by other classes
            // OpenTypeFont -> TrueTypeFont
            // OpenTypeCollection -> TrueTypeCollection
            // OpenTypeBitmap -> OpenTypeFont
            // Type1 -> PrinterFontAscii
            // PostScript -> PrinterFontAscii
            OpenTypeFont::class,
            OpenTypeCollection::class,
            OpenTypeBitmap::class,
            Type1::class,
            PostScript::class,
        ];
    }

    /**
     * Checks whether any file should be ignored or not.
     * Some files should: metadata files, such as AFM and PFM.
     *
     * @param  string  $path  Absolute path to file
     * @return bool True if file should be ignored, false otherwise.
     */
    protected static function shouldIgnoreFile(string $path): bool
    {
        $metadata = [
            AdobeFontMetrics::class,
            PrinterFontMetrics::class,
        ];

        // Skip metadata files
        foreach ($metadata as $metaProvider) {
            foreach ($metaProvider::getExtensions() as $extension) {
                if (\str_ends_with($path, '.'.$extension) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get binary contents from file.
     *
     * @param  string  $path  Absolute path to file
     * @return string File contents
     *
     * @throws NonReadableContentException File issues: does not exist, too big to load, permission issues
     */
    protected static function getRawContentsFromFile(string $path): string
    {
        if (\filesize($path) > self::getMaximumMemoryLimit()) {
            throw new NonReadableContentException(
                'File too big to load, skipping. To check this file, increase your memory_limit configuration key'
            );
        }

        $raw = file_get_contents($path);

        if ($raw === false) {
            throw new NonReadableContentException('Could not get contents for file');
        }

        return $raw;
    }

    /**
     * Retrieves performance metrics
     *
     * @return array{
     *  meta?:array{
     *      all:int|float,
     *      methods:array<string,float>,
     *      count:int
     *  },
     *  classes?:array<class-string,array<string,array{count:int,average:int|float}>>
     * }
     */
    public static function getPerformanceMetrics(): array
    {
        return self::$performanceMetrics;
    }

    /**
     * Empties performance metrics. Useful in performance test to isolate iterations.
     */
    public static function resetPerformanceMetrics(): void
    {
        self::$performanceMetrics = [];
    }

    /**
     * Adds a new performance metric.
     *
     * @param  class-string  $className  Called decoder
     * @param  string  $method  Called method
     * @param  float  $timeBefore  Previous time, before call
     */
    protected static function pushPerformanceMetric(string $className, string $method, float $timeBefore): void
    {
        // Computes delta between previous time and current time
        $computationDelta = round((microtime(true) - $timeBefore), 6);

        // Initializes array structure
        if (isset(self::$performanceMetrics['classes']) === false) {
            self::$performanceMetrics['classes'] = [];
        }
        if (isset(self::$performanceMetrics['classes'][$className]) === false) {
            self::$performanceMetrics['classes'][$className] = [];
        }
        if (isset(self::$performanceMetrics['classes'][$className][$method]) === false) {
            self::$performanceMetrics['classes'][$className][$method] = [
                'count' => 0,
                'average' => 0,
            ];
        }
        if (isset(self::$performanceMetrics['meta']) === false) {
            self::$performanceMetrics['meta'] = [
                'all' => 0,
                'count' => 0,
                'methods' => [],
            ];
        }
        if (isset(self::$performanceMetrics['meta']['methods'][$method]) === false) {
            self::$performanceMetrics['meta']['methods'][$method] = 0;
        }

        // Average time for this method
        // phpcs:disable Generic.WhiteSpace.ArbitraryParenthesesSpacing.SpaceAfterOpen
        // phpcs:disable Generic.WhiteSpace.ArbitraryParenthesesSpacing.SpaceBeforeClose
        // phpcs:disable Squiz.WhiteSpace.OperatorSpacing.SpacingBefore
        // phpcs:disable Squiz.Formatting.OperatorBracket.MissingBrackets
        self::$performanceMetrics['classes'][$className][$method]['average']
            = (
                (
                    self::$performanceMetrics['classes'][$className][$method]['count']
                    * self::$performanceMetrics['classes'][$className][$method]['average']
                )
                + $computationDelta
            )
            / (++self::$performanceMetrics['classes'][$className][$method]['count']);

        // phpcs:enable Generic.WhiteSpace.ArbitraryParenthesesSpacing.SpaceAfterOpen
        // phpcs:enable Generic.WhiteSpace.ArbitraryParenthesesSpacing.SpaceBeforeClose
        // phpcs:enable Squiz.WhiteSpace.OperatorSpacing.SpacingBefore
        // phpcs:enable Squiz.Formatting.OperatorBracket.MissingBrackets

        // Increases global time counter
        self::$performanceMetrics['meta']['all'] += $computationDelta;
        // Increases global call counter
        self::$performanceMetrics['meta']['count']++;
        // Increases global method-specific call counter
        self::$performanceMetrics['meta']['methods'][$method] += $computationDelta;
    }

    /**
     * Converts a php.ini size value (ex: "512M", "1G", "2048K") in bytes.
     *
     * @return int Maximum memory limit in bytes
     */
    protected static function getMaximumMemoryLimit(): int
    {
        if (self::$memoryLimit !== null) {
            return self::$memoryLimit;
        }

        $val = trim(ini_get('memory_limit'));

        // Unlimited
        if ($val === '-1') {
            return PHP_INT_MAX;
        }

        // Is last char an unit?
        $last = strtolower($val[(strlen($val) - 1)]);
        $num = (int) $val;

        switch ($last) {
            case 'g':
                self::$memoryLimit = ($num * 1024 * 1024 * 1024);
                break;
            case 'm':
                self::$memoryLimit = ($num * 1024 * 1024);
                break;
            case 'k':
                self::$memoryLimit = ($num * 1024);
                break;
                // Unhandled unit/typo
            default:
                self::$memoryLimit = $num;
                break;
        }

        return intval(round((self::$memoryLimit * 0.9)));
    }
}
