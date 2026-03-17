<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class EmbeddedOpenTypeTest extends BaseTestCase
{
    protected static $folder = 'eot';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'FontAwesome' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'fontawesome-webfont.eot'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'FontAwesome',
                        ]),
                    ],
                    'GLYPHICONS Halflings Regular' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'glyphicons-halflings-regular.eot'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'GLYPHICONS Halflings Regular',
                        ]),
                    ],
                    'Maki' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Maki.eot'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Maki',
                        ]),
                    ],
                    'Icons' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'open-iconic.eot'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Icons',
                        ]),
                    ],
                    'Satoshi' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Satoshi-Black.eot'),
                            'weight' => 900,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Satoshi',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Satoshi-BlackItalic.eot'),
                            'weight' => 900,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Satoshi',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Satoshi-Light.eot'),
                            'weight' => 300,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Satoshi',
                        ]),
                    ],
                    // Yes, Satoshi-Bold and Satoshi-BoldItalic in samples contains "false" as FamilyName
                    'false' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Satoshi-Bold.eot'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'false',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Satoshi-BoldItalic.eot'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'false',
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
