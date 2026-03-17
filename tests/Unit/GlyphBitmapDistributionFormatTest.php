<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class GlyphBitmapDistributionFormatTest extends BaseTestCase
{
    protected static $folder = 'bdf';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'Gothic' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'hanglg16.bdf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Gothic',
                        ]),
                    ],
                    'Mincho' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'hanglm16.bdf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Mincho',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'hanglm24.bdf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Mincho',
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
