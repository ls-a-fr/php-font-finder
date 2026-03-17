<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class DataForkTrueTypeFontTest extends BaseTestCase
{
    protected static $folder = 'dfont';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'PT Sans' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'PTSans-Regular.otf.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'PT Sans',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'PTSans-Regular.otf.dfont'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'PT Sans',
                        ]),
                    ],
                    'Tamsyn10x20' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamsyn10x20.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamsyn10x20',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamsyn10x20.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamsyn10x20',
                        ]),
                    ],
                    'Tamsyn6x12' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamsyn6x12.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamsyn6x12',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamsyn6x12.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamsyn6x12',
                        ]),
                    ],
                    'Tamsyn7x13' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamsyn7x13.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamsyn7x13',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamsyn7x13.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamsyn7x13',
                        ]),
                    ],
                    'Tamsyn7x14' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamsyn7x14.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamsyn7x14',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamsyn7x14.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamsyn7x14',
                        ]),
                    ],
                    'Tamsyn8x15' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamsyn8x15.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamsyn8x15',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamsyn8x15.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamsyn8x15',
                        ]),
                    ],
                    'Tamsyn8x16' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamsyn8x16.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamsyn8x16',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamsyn8x16.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamsyn8x16',
                        ]),
                    ],
                    'Tamzen10x20' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen10x20.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamzen10x20',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen10x20.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamzen10x20',
                        ]),
                    ],
                    'Tamzen5x9' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen5x9.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamzen5x9',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen5x9.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamzen5x9',
                        ]),
                    ],
                    'Tamzen6x12' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen6x12.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamzen6x12',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen6x12.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamzen6x12',
                        ]),
                    ],
                    'Tamzen7x13' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen7x13.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamzen7x13',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen7x13.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamzen7x13',
                        ]),
                    ],
                    'Tamzen7x14' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen7x14.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamzen7x14',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen7x14.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamzen7x14',
                        ]),
                    ],
                    'Tamzen8x15' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen8x15.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamzen8x15',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen8x15.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamzen8x15',
                        ]),
                    ],
                    'Tamzen8x16' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen8x16.dfont'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Tamzen8x16',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Tamzen8x16.dfont'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Tamzen8x16',
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
