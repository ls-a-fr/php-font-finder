<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class ScalableVectorGraphicsCompressedTest extends BaseTestCase
{
    protected static $folder = 'svgz';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'FontAwesome' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'fontawesome-webfont.svgz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'FontAwesome',
                        ]),
                    ],
                    // Glyphicons do not specify font-face-name or font-family
                    'glyphicons-halflings-regular' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'glyphicons-halflings-regular.svgz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'glyphicons-halflings-regular',
                        ]),
                    ],
                    'Icons' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'open-iconic.svgz'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Icons',
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
