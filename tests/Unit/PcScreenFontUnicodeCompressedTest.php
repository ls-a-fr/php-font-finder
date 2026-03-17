<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class PcScreenFontUnicodeCompressedTest extends BaseTestCase
{
    protected static $folder = 'psfu-gz';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'cp850-8x8' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'cp850-8x8.psfu.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'cp850-8x8',
                        ]),
                    ],
                    'cp850-8x14' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'cp850-8x14.psfu.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'cp850-8x14',
                        ]),
                    ],
                    'cp850-8x16' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'cp850-8x16.psfu.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'cp850-8x16',
                        ]),
                    ],
                    'cp1250' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'cp1250.psfu.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'cp1250',
                        ]),
                    ],
                    'drdos8x14' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'drdos8x14.psfu.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'drdos8x14',
                        ]),
                    ],
                    'drdos8x16' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'drdos8x16.psfu.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'drdos8x16',
                        ]),
                    ],
                    'eurlatgr' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'eurlatgr.psfu.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'eurlatgr',
                        ]),
                    ],
                    'lat4-12' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat4-12.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat4-12',
                        ]),
                    ],
                    'lat4-14' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat4-14.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat4-14',
                        ]),
                    ],
                    'lat4-16+' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat4-16+.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat4-16+',
                        ]),
                    ],
                    'lat4-16' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat4-16.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat4-16',
                        ]),
                    ],
                    'lat4a-08' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat4a-08.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat4a-08',
                        ]),
                    ],
                    'lat4a-10' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat4a-10.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat4a-10',
                        ]),
                    ],
                    'lat9wbrl-08' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat9wbrl-08.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat9wbrl-08',
                        ]),
                    ],
                    'lat9wbrl-10' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat9wbrl-10.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat9wbrl-10',
                        ]),
                    ],
                    'ruscii_8x14' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'ruscii_8x14.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'ruscii_8x14',
                        ]),
                    ],
                    'ruscii_8x16' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'ruscii_8x16.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'ruscii_8x16',
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
