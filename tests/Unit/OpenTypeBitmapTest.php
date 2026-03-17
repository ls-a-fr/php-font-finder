<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class OpenTypeBitmapTest extends BaseTestCase
{
    protected static $folder = 'otb';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'Bm437 Trident 9x14' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Bm437_Trident_9x14.otb'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Bm437 Trident 9x14',
                        ]),
                    ],
                    'Bm437 Trident 9x16' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Bm437_Trident_9x16.otb'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Bm437 Trident 9x16',
                        ]),
                    ],
                    'Bm437 TsengEVA 132 6x14' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Bm437_TsengEVA_132_6x14.otb'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Bm437 TsengEVA 132 6x14',
                        ]),
                    ],
                    'Bm437 TsengEVA 132 6x8' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Bm437_TsengEVA_132_6x8.otb'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Bm437 TsengEVA 132 6x8',
                        ]),
                    ],
                    'Bm437 Verite 8x14' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Bm437_Verite_8x14.otb'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Bm437 Verite 8x14',
                        ]),
                    ],
                    'Bm437 Verite 8x8-2y' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Bm437_Verite_8x8-2y.otb'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Bm437 Verite 8x8-2y',
                        ]),
                    ],
                    'Bm437 Verite 8x8' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Bm437_Verite_8x8.otb'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Bm437 Verite 8x8',
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
