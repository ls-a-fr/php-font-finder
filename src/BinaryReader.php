<?php

namespace Lsa\Font\Finder;

use RuntimeException;
class BinaryReader
{
    private string $data;
    private int $pos = 0;
    private int $length;

    public function __construct(string $data)
    {
        $this->data = $data;
        $this->length = strlen($data);
    }

    public function seek(int $pos): void
    {
        if ($pos < 0 || $pos > $this->length) {
            throw new RuntimeException("Seek position out of bounds: $pos");
        }
        $this->pos = $pos;
    }

    public function read(int $length): string
    {
        if ($length < 0) {
            throw new RuntimeException("Negative read length: $length");
        }
        if ($this->pos + $length > $this->length) {
            throw new RuntimeException("Unexpected EOF at position {$this->pos}, tried to read $length bytes");
        }
        $result = substr($this->data, $this->pos, $length);
        $this->pos += $length;
        return $result;
    }

    public function readUInt16(): int
    {
        return unpack('n', $this->read(2))[1];
    }

    public function readUInt32(): int
    {
        return unpack('N', $this->read(4))[1];
    }

    public function readInt16(): int
    {
        $v = unpack('n', $this->read(2))[1];
        return $v > 0x7FFF ? $v - 0x10000 : $v;
    }

    public function readInt32(): int
    {
        $v = unpack('N', $this->read(4))[1];
        return $v > 0x7FFFFFFF ? $v - 0x100000000 : $v;
    }

    public function tell(): int
    {
        return $this->pos;
    }

    public function remaining(): int
    {
        return $this->length - $this->pos;
    }
}