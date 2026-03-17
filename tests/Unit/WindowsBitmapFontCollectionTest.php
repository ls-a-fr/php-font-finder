<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class WindowsBitmapFontCollectionTest extends BaseTestCase
{
    protected static $folder = 'fon';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'fixed' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'fixed.fon'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'fixed',
                        ]),
                    ],
                    'Terminal' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'fixedc.fon'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Terminal',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'terminal.fon'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Terminal',
                        ]),
                    ],
                    'Spleen' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'spleen-12x24.fon'),
                            'weight' => 500,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Spleen',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'spleen-16x32.fon'),
                            'weight' => 500,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Spleen',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'spleen-5x8.fon'),
                            'weight' => 500,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Spleen',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'spleen-8x16.fon'),
                            'weight' => 500,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Spleen',
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
