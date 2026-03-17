<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class JimHersheyFontTest extends BaseTestCase
{
    protected static $folder = 'jhf';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'astrology' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'astrology.jhf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'astrology',
                        ]),
                    ],
                    'cursive' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'cursive.jhf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'cursive',
                        ]),
                    ],
                    'cyrilc_1' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'cyrilc_1.jhf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'cyrilc_1',
                        ]),
                    ],
                    'cyrillic' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'cyrillic.jhf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'cyrillic',
                        ]),
                    ],
                    'futural' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'futural.jhf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'futural',
                        ]),
                    ],
                    'futuram' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'futuram.jhf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'futuram',
                        ]),
                    ],
                    'gothgbt' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'gothgbt.jhf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'gothgbt',
                        ]),
                    ],
                    'gothgrt' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'gothgrt.jhf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'gothgrt',
                        ]),
                    ],
                    'gothiceng' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'gothiceng.jhf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'gothiceng',
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
