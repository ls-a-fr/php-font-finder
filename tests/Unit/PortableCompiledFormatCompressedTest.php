<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class PortableCompiledFormatCompressedTest extends BaseTestCase
{
    protected static $folder = 'pcf-gz';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'Charter' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'charB08.pcf.gz'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Charter',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'charB10.pcf.gz'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Charter',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'charB12.pcf.gz'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Charter',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'charBI10.pcf.gz'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Charter',
                        ]),
                    ],
                    'Courier' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'courB08.pcf.gz'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Courier',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'courBO12.pcf.gz'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Courier',
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
