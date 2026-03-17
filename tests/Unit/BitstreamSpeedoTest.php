<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class BitstreamSpeedoTest extends BaseTestCase
{
    protected static $folder = 'spd';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'Courier 10 Pitch' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'font0419.spd'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Courier 10 Pitch',
                        ]),
                    ],
                    'Courier 10 Pitch Italic' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'font0582.spd'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Courier 10 Pitch Italic',
                        ]),
                    ],
                    'Courier 10 Pitch Bold' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'font0583.spd'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Courier 10 Pitch Bold',
                        ]),
                    ],
                    'Courier 10 Pitch Bold Italic' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'font0611.spd'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Courier 10 Pitch Bold Italic',
                        ]),
                    ],
                    'Transitional 801 Bitstream Charter (TM)' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'font0648.spd'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Transitional 801 Bitstream Charter (TM)',
                        ]),
                    ],
                    'Transitional 801 Bitstream Charter Italic (TM)' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'font0649.spd'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Transitional 801 Bitstream Charter Italic (TM)',
                        ]),
                    ],
                    'Transitional 801 Bitstream Charter Black (TM)' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'font0709.spd'),
                            'weight' => 900,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Transitional 801 Bitstream Charter Black (TM)',
                        ]),
                    ],
                    'Transitional 801 Bitstream Charter Black Italic (TM)' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'font0710.spd'),
                            'weight' => 900,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Transitional 801 Bitstream Charter Black Italic (TM)',
                        ]),
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('validDataProvider')]
    public function test_fonts(array $expected): void
    {
        $this->test(self::$folder, $expected);
    }
}
