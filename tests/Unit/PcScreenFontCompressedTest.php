<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class PcScreenFontCompressedTest extends BaseTestCase
{
    protected static $folder = 'psf-gz';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'lat4-12-no-unicode' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat4-12-no-unicode.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat4-12-no-unicode',
                        ]),
                    ],
                    'lat4-14-no-unicode' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat4-14-no-unicode.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat4-14-no-unicode',
                        ]),
                    ],
                    'lat4-16+-no-unicode' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat4-16+-no-unicode.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat4-16+-no-unicode',
                        ]),
                    ],
                    'lat4-16-no-unicode' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat4-16-no-unicode.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat4-16-no-unicode',
                        ]),
                    ],
                    'lat4a-08-no-unicode' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat4a-08-no-unicode.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat4a-08-no-unicode',
                        ]),
                    ],
                    'lat4a-10-no-unicode' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat4a-10-no-unicode.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat4a-10-no-unicode',
                        ]),
                    ],
                    'lat9wbrl-08-no-unicode' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat9wbrl-08-no-unicode.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat9wbrl-08-no-unicode',
                        ]),
                    ],
                    'lat9wbrl-10-no-unicode' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'lat9wbrl-10-no-unicode.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'lat9wbrl-10-no-unicode',
                        ]),
                    ],
                    'ruscii_8x14-no-unicode' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'ruscii_8x14-no-unicode.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'ruscii_8x14-no-unicode',
                        ]),
                    ],
                    'ruscii_8x16-no-unicode' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'ruscii_8x16-no-unicode.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'ruscii_8x16-no-unicode',
                        ]),
                    ],
                    'Solarize' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Solarize.12x29-no-unicode.psf.gz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Solarize',
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
