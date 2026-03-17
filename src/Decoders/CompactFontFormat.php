<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders;

use Lsa\Font\Finder\Contracts\FontDecoder;
use Lsa\Font\Finder\Decoders\Lib\DecoderUtils;
use Lsa\Font\Finder\Exceptions\InvalidOperationException;
use Lsa\Font\Finder\Font;

/**
 * Compact Font Format
 *
 * @see https://adobe-type-tools.github.io/font-tech-notes/pdfs/5176.CFF.pdf
 */
class CompactFontFormat implements FontDecoder
{
    public static function canExecute(string $raw): bool
    {
        // CFF header:
        // Offset  Size  Description
        // 0       1     major version
        // 1       1     minor version
        // 2       1     header size
        // 3       1     offset size
        if (strlen($raw) < 4) {
            return false;
        }

        $major = ord($raw[0]);
        $minor = ord($raw[1]);

        // Only accept CFF 1.0 and CFF 2.0
        if (($major === 1 || $major === 2) && $minor === 0) {
            return true;
        }

        return false;
    }

    public static function extractFontMeta(string $raw, string $filename): array
    {
        // CFF Header
        // Offset  Size  Description
        // 0       1     major version
        // 1       1     minor version
        // 2       1     headerSize (total header length)
        // 3       1     offSize (offset size in INDEX structures)
        // 4..N    -     reserved / padding (until headerSize)
        $headerSize = ord($raw[2]);

        $offset = 4;
        // Header may be longer than 4 bytes
        if ($headerSize > 4) {
            $offset = $headerSize;
        }

        // Name INDEX structure:
        // Offset  Size        Description
        // 0       2           count (number of names)
        // 2       1           offSize (1,2,3,4 bytes per offset)
        // 3       count+1 *   offsets (1-based)
        // ?       variable    data (concatenated names)
        [$nameIndex, $offset] = self::readIndex($raw, $offset);

        $family = 'Unknown';
        $weight = 400;
        $italic = null;
        $bold = null;

        if (count($nameIndex) > 0) {
            // First name INDEX is family name, if defined
            $family = trim($nameIndex[0]);
        }

        // TopDict INDEX structure:
        // 0       2           count (usually 1)
        // 2       1           offSize
        // 3       count+1 *   offsets
        // ?       variable    DICT data (PostScript operators)
        [$topDictIndex, $offset] = self::readIndex($raw, $offset);

        // String INDEX structure:
        // 0       2           count (number of custom strings)
        // 2       1           offSize
        // 3       count+1 *   offsets (1-based)
        // ?       variable    string data (ASCII/UTF-8)
        [$stringIndex, $offset] = self::readIndex($raw, $offset);

        if (count($topDictIndex) > 0) {
            [$family, $weight, $bold, $italic] = self::getTopDictInformation($topDictIndex[0], $stringIndex, $family);
        }

        // Fallbacks
        if ($italic === null && str_contains(strtolower($family), 'italic') === true) {
            $italic = true;
        }
        if ($bold !== true) {
            // phpcs:ignore Squiz.PHP.DisallowComparisonAssignment.AssignedComparison
            $bold = ($weight >= 700);
        }

        // Remove subfamily prefix if any
        // CFF files usually have family names prefixed with 6 alphabetical random characters,
        // followed by a "-".
        if (preg_match('/^[A-Z]{6}\+/', $family) === 1) {
            $family = substr($family, 7);
        }

        // Fallback on name
        if ($family === 'Unknown') {
            $family = DecoderUtils::getFontNameFromFilePath($filename, 'cff');
        }

        return [
            new Font([
                'name' => $family,
                'filename' => $filename,
                'weight' => $weight,
                'italic' => ($italic ?? false),
                'bold' => $bold,
            ]),
        ];
    }

    /**
     * Parse top dictionary and retrive bold, italic and weight information. May change family.
     *
     * @param  string  $topDict  Top dictionary string
     * @param  string[]  $stringIndex  String resources offsets
     * @param  string  $family  Current family. May be 'Unknown'
     * @return array{0:string,1:int,2:?bool,3:?bool} Family, Weight, Bold flag, Italig flag
     */
    protected static function getTopDictInformation(string $topDict, array $stringIndex, string $family)
    {
        $bold = null;
        $italic = null;
        $weight = 400;

        $dictData = self::parseDict($topDict);

        // ItalicAngle
        if (isset($dictData['ItalicAngle']) === true && $dictData['ItalicAngle'] !== floatval(0)) {
            $italic = true;
        }

        // Weight
        if (isset($dictData['WeightSID']) === true && isset($stringIndex[$dictData['WeightSID']]) === true) {
            $weightName = strtolower($stringIndex[$dictData['WeightSID']]);
            if (str_contains($weightName, 'bold') === true) {
                $bold = true;
                $weight = 700;
            }
        }

        // FamilyName
        if (
            isset($dictData['FullNameSID']) === true
            && isset($stringIndex[$dictData['FullNameSID']]) === true
        ) {
            $family = trim($stringIndex[$dictData['FullNameSID']]);
        } elseif (
            isset($dictData['FamilyNameSID']) === true
            && isset($stringIndex[$dictData['FamilyNameSID']]) === true
        ) {
            $family = trim($stringIndex[$dictData['FamilyNameSID']]);
        }

        return [$family, $weight, $bold, $italic];
    }

    /**
     * Read an index from CFF
     *
     * @param  string  $raw  Raw binary content
     * @param  int  $offset  Offset in binary content to read at
     * @return array{0: list<string>, 1: int} First cell for data, second cell for new offset value
     *
     * @throws InvalidOperationException Corrupted font
     */
    private static function readIndex(string $raw, int $offset): array
    {
        // Get index count
        $count = DecoderUtils::unpackInt('n', $raw, $offset);
        if ($count <= 0) {
            throw new InvalidOperationException('Invalid count in readIndex, font is corrupted');
        }

        // Get offsets size
        $offSize = ord($raw[($offset + 2)]);
        $offsets = [];

        // Gather offsets
        $offset = ($offset + 3);
        for ($i = 0; $i < ($count + 1); $i++) {
            $offsets[] = self::readOffset($raw, ($offset + ($i * $offSize)), $offSize);
        }

        // Data starts after offsets
        $dataStart = ($offset + (($count + 1) * $offSize));

        // Get index data
        $items = [];
        for ($i = 0; $i < $count; $i++) {
            $start = ($dataStart + ($offsets[$i] - 1));
            $end = ($dataStart + ($offsets[($i + 1)] - 1));
            $length = ($end - $start);

            $items[] = substr($raw, $start, $length);
        }

        return [$items, ($dataStart + ($offsets[$count] - 1))];
    }

    /**
     * Read an offset from position until size
     *
     * @param  string  $raw  Raw binary content
     * @param  int  $position  Start position
     * @param  int  $size  Content length
     * @return int Value
     */
    private static function readOffset(string $raw, int $position, int $size): int
    {
        $value = 0;
        for ($i = 0; $i < $size; $i++) {
            $value = (($value << 8) | ord($raw[$position]));
            $position++;
        }

        return $value;
    }

    /**
     * Parse a CFF dictionary from its binary data
     *
     * @param  string  $data  Raw data
     * @return array{
     *   FullNameSID?: int,
     *   FamilyNameSID?: int,
     *   WeightSID?: int,
     *   ItalicAngle?: float
     * } The dictionary
     */
    private static function parseDict(string $data): array
    {
        // DICT Reference
        // Offset  Size    Description
        // var     var     operand(s) (integers, floats, SID, etc.)
        // var     1–2     operator (1 byte, or 2 bytes if 0x0C)
        //
        // Operators (excerpt):
        // 0   version SID
        // 1   Notice SID
        // 2   FullName SID
        // 3   FamilyName SID
        // 4   Weight SID
        // 5   isFixedPitch
        // 6   ItalicAngle
        // 7   UnderlinePosition
        // 8   UnderlineThickness
        // 9   PaintType
        // 10  CharstringType
        // 11  FontMatrix
        // 12  (escape, meaning extended operator)
        // 13  UniqueID
        // 14  XUID
        // 15  charset offset
        // 16  Encoding offset
        // 17  CharStrings offset
        // 18  Private DICT size & offset
        // 19-21 reserved
        $offset = 0;
        $dataLength = strlen($data);
        $operand = null;
        $result = [];

        while ($offset < $dataLength) {
            // Get current byte
            $currentByte = ord($data[$offset]);

            // Is the current byte an operator?
            if ($currentByte <= 21) {
                // Lookahead nextByte to make operator
                $nextByte = null;
                if (isset($data[($offset + 1)]) === true) {
                    $nextByte = ord($data[($offset + 1)]);
                }
                $op = self::makeOperator($currentByte, $nextByte);

                // Set current operator to defined key
                $key = self::findKeyForOperator($op);

                // If operand is not null, set value in result array (with correct type)
                if ($operand !== null && $key === 'ItalicAngle') {
                    $result[$key] = floatval($operand);
                } elseif ($operand !== null && $key !== null) {
                    $result[$key] = intval($operand);
                }

                // Reset operand
                $operand = null;
                $offset++;
                // Extended operator is two bytes long
                if ($op > 1200) {
                    $offset++;
                }

                continue;
            }

            // The current byte is an operand, meaning a value related to an operator.
            [$op, $length] = self::readOperand($currentByte, $data, $offset);
            if ($op !== null) {
                $operand = $op;
            }
            $offset += $length;
        }

        /**
         * PHPStan still has issues when "optimizing" array types
         *
         * @var array{FullNameSID?:int,FamilyNameSID?:int,WeightSID?:int,ItalicAngle?:float} $result
         */
        return $result;
    }

    /**
     * Makes an operator based on CFF structure.
     *
     * @param  int  $currentByte  Mandatory. See operator map for values.
     * @param  ?int  $nextByte  Only used with extended operators (two bytes long)
     * @return int Operator integer representation
     *
     * @throws InvalidOperationException Extended operator with only one byte
     */
    private static function makeOperator(int $currentByte, ?int $nextByte): int
    {
        $op = $currentByte;

        if ($op === 12 && $nextByte === null) {
            throw new InvalidOperationException('Corrupted file: extended operators should be two bytes long');
        }

        if ($op === 12) {
            // Extended operator. We do not need to get value from this operator,
            // but we need to increase offset accordingly: this operator is 2 bytes long.
            $op = (1200 + $nextByte);
        }

        return $op;
    }

    /**
     * Helper method to get string representation of an operator.
     * Constants would be a fine choice too, but for completeness a lot of constants would need
     * to be defined.
     *
     * @param  int  $op  Operator integer representation
     * @return ?string String representation, or null if operator unused by this library
     */
    private static function findKeyForOperator(int $op): ?string
    {
        // Wanted operators. Set current operand to corresponding key.
        switch ($op) {
            case 2:
                // FullName SID
                return 'FullNameSID';
            case 3:
                // FamilyName SID
                return 'FamilyNameSID';
            case 4:
                // Weight
                return 'WeightSID';
            case 6:
                // ItalicAngle
                return 'ItalicAngle';
            default:
                // Operator not used by this library
                return null;
        }
    }

    /**
     * Read an integer operand in a dictionary.
     *
     * @param  int  $currentByte  The current byte
     * @param  string  $data  Raw binary content
     * @return array{0: int|float|null, 1: int<1,5>} First cell is the value, second cell is the value length (in bytes)
     */
    private static function readOperand(int $currentByte, string $data, int $offset): array
    {
        // 28: 2-bytes integer (16-bit signed)
        if ($currentByte === 28) {
            $b1 = ord($data[($offset + 1)]);
            $b2 = ord($data[($offset + 2)]);
            $value = (($b1 << 8) | $b2);
            // Convert to signed integer
            if (($value & 0x8000) !== 0) {
                $value -= 0x10000;
            }

            return [$value, 3];
        }

        // 29: 4-bytes value
        if ($currentByte === 29) {
            // Unsigned 4 bytes value
            $u = DecoderUtils::unpackInt('N', $data);

            // Convert to signed integer
            if (($u & 0x80000000) !== 0) {
                $u -= 0x100000000;
            }

            return [
                ($u / 65536.0),
                5,
            ];
        }

        // 32-246 : value between -107 and 107
        if ($currentByte >= 32 && $currentByte <= 246) {
            return [
                ($currentByte - 139),
                1,
            ];
        }

        // 247-250: value between +108 and +1131 (unsigned)
        if ($currentByte >= 247 && $currentByte <= 250) {
            $b1 = ord($data[($offset + 1)]);

            return [
                ((($currentByte - 247) * 256) + $b1 + 108),
                2,
            ];
        }
        // 251-254: value between -1131 and -108 (signed)
        if ($currentByte >= 251 && $currentByte <= 254) {
            $b1 = ord($data[($offset + 1)]);

            return [
                -((($currentByte - 251) * 256) + $b1 + 108),
                2,
            ];
        }

        return [null, 1];
    }
}
