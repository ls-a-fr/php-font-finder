<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Decoders\Lib;

use Lsa\Font\Finder\Exceptions\ZlibException;

/**
 * ZlibDecoder wraps `zlib_decode` function to add some cache
 */
class ZlibDecoder
{
    /**
     * Raw decoded data: checksum as key, raw decoded as value
     *
     * @var array<string,string>
     */
    protected static array $raws;

    /**
     * Decodes a compressed binary content. If already decoded, returns cached value instead.
     *
     * @param  string  $raw  Raw binary content
     * @return string Decompressed binary content if valid. Throws an exception otherwise.
     *
     * @throws ZlibException If content is not encoded, or decoding failed
     */
    public static function decode(string $raw): string
    {
        $md5 = md5($raw);
        if (isset(self::$raws[$md5]) === false) {
            if (strlen($raw) < 2) {
                throw new ZlibException('Raw data is too small');
            }

            if ($raw[0] !== "\x1F" || $raw[1] !== "\x8B") {
                throw new ZlibException('Raw data does not contain valid content');
            }

            $result = \zlib_decode($raw);
            if ($result === false) {
                throw new ZlibException('Could not deflate raw data');
            }
            self::$raws[$md5] = $result;
        }

        return self::$raws[$md5];
    }
}
