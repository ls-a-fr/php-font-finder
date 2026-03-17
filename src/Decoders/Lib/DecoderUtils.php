<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders\Lib;

use Lsa\Font\Finder\Exceptions\InvalidOperationException;

/**
 * DecoderUtils contains various methods to help Decoder classes
 */
class DecoderUtils
{
    /**
     * XLFD: X Logical Font Description
     * Fields in XLFD for readability
     *
     * @var array<string,int>
     */
    protected static array $xlfdFields = [
        'foundry' => 1,
        'family' => 2,
        'weight' => 3,
        'slant' => 4,
        'setwidth' => 5,
        'addstyle' => 6,
        'pixel size' => 7,
        'point size' => 8,
        'resolution X' => 9,
        'resolution Y' => 10,
        'spacing' => 11,
        'average width' => 12,
        'charset registry' => 13,
        'charset encoding' => 14,
    ];

    /**
     * An XLFD string, or some files such as BDF or PCF will contain weight as string, not integer.
     * This map allows to infer an integer based on found string.
     *
     * @var array<string,int>
     */
    public static array $weightMap = [
        'thin' => 100,
        'extralight' => 200,
        'ultralight' => 200,
        'light' => 300,
        'book' => 350,
        'regular' => 400,
        'medium' => 400,
        'demi' => 600,
        'semibold' => 600,
        'bold' => 700,
        'black' => 900,
        'heavy' => 900,
    ];

    /**
     * Simple port of Python calcsize. Based on unpack format, compute bytes length.
     *
     * @param  string  $format  Unpack format
     * @return int Size in bytes
     */
    public static function calcsize(string $format): int
    {
        $map = [
            'a' => 1,
            'A' => 1,
            'h' => 1,
            'H' => 1,
            'c' => 1,
            'C' => 1,
            's' => 2,
            'S' => 2,
            'n' => 2,
            'v' => 2,
            'i' => PHP_INT_SIZE,
            'I' => PHP_INT_SIZE,
            'l' => 4,
            'L' => 4,
            'N' => 4,
            'V' => 4,
            'q' => 8,
            'Q' => 8,
            'J' => 8,
            'P' => 8,
            'f' => 4,
            'g' => 4,
            'G' => 4,
            'd' => 8,
            'e' => 8,
            'E' => 8,
            'x' => 1,
            'Z' => 1,
        ];

        // Unpack format
        $parts = explode('/', $format);
        $sum = 0;
        foreach ($parts as $part) {
            $char = $part[0];
            // Automatic cast is done here
            $multiplier = max(1, intval(substr($part, 1)));
            $sum += ($map[$char] * $multiplier);
        }

        return $sum;
    }

    /**
     * Computes a font name based on a file path. In recent font formats, font name is registered
     * inside the file. However, for ancient ones, you may fallback to file name.
     *
     * @param  string  $filepath  Full file path
     * @param  string  $stopper  Usually file extension. Prevent extension to be used as font name
     * @return string Font name, or Unknown if nothing found
     */
    public static function getFontNameFromFilePath(string $filepath, string $stopper = ''): string
    {
        $fontName = basename($filepath);
        $basenameParts = explode('.', $fontName);
        do {
            $candidateName = array_shift($basenameParts);

            // If candidateName equals stopper, stop here
            // In fact, candidateName cannot be null here, as this loop stops if basenameParts is empty
            if ($candidateName === $stopper || $candidateName === null) {
                break;
            }

            if ($candidateName !== '') {
                return $candidateName;
            }
        } while (empty($basenameParts) === false);

        return 'Unknown';
    }

    /**
     * Parses XLFD names.
     *
     * @param  string  $value  XLFD value
     * @return array{0:string,1:int|null,2:true|null,3:bool|null} FamilyName, Weight, Italic, Bold
     */
    public static function parseXlfd(string $value): array
    {
        $family = null;
        $weight = null;
        $italic = null;
        $bold = null;

        // Get font information from XLFD
        // An XLFD looks like this: `-adobe-helvetica-bold-o-normal--12-120-75-75-p-70-iso8859-1`
        $weightName = null;
        $slant = null;

        // Ensures if found value is XLFD
        if (isset($value[0]) === true && $value[0] === '-') {
            $xlfdParts = explode('-', $value);

            $family = ($xlfdParts[self::$xlfdFields['family']] ?? $value);
            if (isset($xlfdParts[self::$xlfdFields['weight']]) === true) {
                $weightName = strtolower($xlfdParts[self::$xlfdFields['weight']]);
            }
            if (isset($xlfdParts[self::$xlfdFields['slant']]) === true) {
                $slant = strtolower($xlfdParts[self::$xlfdFields['slant']]);
            }
        } else {
            // FONT is not XLFD, set it as family name
            $family = $value;
        }

        if ($weightName !== null && isset(self::$weightMap[$weightName]) === true) {
            $weight = self::$weightMap[$weightName];
            // phpcs:ignore Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
            $bold = ($weight >= 700);
        }
        if ($slant === 'i' || $slant === 'o') {
            $italic = true;
        }

        return [$family, $weight, $italic, $bold];
    }

    /**
     * Reads an UTF-16 string in litte-endian, starting at specified offset.
     *
     * @param  string  $raw  Raw binary contents
     * @param  int  $offset  Start reading position
     * @return string UTF-16LE contents
     */
    public static function readLittleEndianUtf16String(string $raw, int $offset): string
    {
        // Invalid offset
        $len = strlen($raw);
        if ($offset <= 0 || $offset >= $len) {
            return '';
        }

        $out = '';
        for ($i = $offset; ($i + 1) < $len; $i += 2) {
            $code = self::unpackInt('v', substr($raw, $i, 2));
            if ($code === 0) {
                break;
            }
            $out .= pack('v', $code);
        }

        // Convert from UTF-8 to UTF-16 Little Endian
        $out = mb_convert_encoding($out, 'UTF-8', 'UTF-16LE');

        return trim($out);
    }

    /**
     * Read a string byte-char after byte-char. Stops after first null-byte (\0)
     *
     * @param  string  $raw  Raw binary content
     * @param  int  $offset  Start offset
     * @return array{0: string, 1: int} First cell for value, second cell for updated offset value
     */
    public static function readNullPaddedString(string $raw, int $offset): array
    {
        $startOffset = $offset;
        $len = strlen($raw);

        while ($offset < $len && $raw[$offset] !== "\0") {
            $offset++;
        }
        $str = substr($raw, $startOffset, ($offset - $startOffset));
        // Skip null terminator if present
        if ($offset < $len && $raw[$offset] === "\0") {
            $offset++;
        }

        return [$str, $offset];
    }

    /**
     * Unpack wrapper for PHPstan and code quality. Unpacks an array.
     *
     * @param  string  $format  Unpack format
     * @param  string  $data  Data to unpack
     * @param  int  $offset  Data offset
     * @return array<string,string|int> Unpacked data
     *
     * @see https://php.net/unpack
     *
     * @throws InvalidOperationException Invalid unpack result or misuse of this function
     */
    public static function unpackArray(string $format, string $data, int $offset = 0): array
    {
        $result = \unpack($format, $data, $offset);
        if ($result === false) {
            throw new InvalidOperationException('Invalid unpack result');
        }
        if (\array_is_list($result) === true) {
            throw new InvalidOperationException(
                'Invalid unpack result: unpackArray should return an associative array'
            );
        }

        return $result;
    }

    /**
     * Unpack wrapper for PHPstan and code quality. Unpacks a string.
     *
     * @param  string  $format  Unpack format
     * @param  string  $data  Data to unpack
     * @param  int  $offset  Data offset
     * @return string Unpacked data
     *
     * @see https://php.net/unpack
     *
     * @throws InvalidOperationException Invalid unpack result or misuse of this function
     */
    public static function unpackString(string $format, string $data, int $offset = 0): string
    {
        $result = \unpack($format, $data, $offset);
        if ($result === false) {
            throw new InvalidOperationException('Invalid unpack result');
        }
        if (isset($result[1]) === false) {
            throw new InvalidOperationException('Invalid unpack result: index 1 does not exist');
        }
        if (\is_string($result[1]) === false) {
            throw new InvalidOperationException('Invalid unpack result: index 1 is not a string');
        }

        return $result[1];
    }

    /**
     * Unpack wrapper for PHPstan and code quality. Unpacks an integer.
     *
     * @param  string  $format  Unpack format
     * @param  string  $data  Data to unpack
     * @param  int  $offset  Data offset
     * @return int Unpacked data
     *
     * @see https://php.net/unpack
     *
     * @throws InvalidOperationException Invalid unpack result or misuse of this function
     */
    public static function unpackInt(string $format, string $data, int $offset = 0): int
    {
        $result = \unpack($format, $data, $offset);
        if ($result === false) {
            throw new InvalidOperationException('Invalid unpack result');
        }
        if (isset($result[1]) === false) {
            throw new InvalidOperationException('Invalid unpack result: index 1 does not exist');
        }
        if (\is_int($result[1]) === false) {
            throw new InvalidOperationException('Invalid unpack result: index 1 is not an integer');
        }

        return $result[1];
    }
}
