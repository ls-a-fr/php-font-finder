<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class BsdVgaFontTest extends BaseTestCase
{
    protected static $folder = 'bsd-fnt';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'IBMPlexMono-Light-12' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'IBMPlexMono-Light-12.fnt'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'IBMPlexMono-Light-12',
                        ]),
                    ],
                    'IBMPlexMono-Light-13' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'IBMPlexMono-Light-13.fnt'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'IBMPlexMono-Light-13',
                        ]),
                    ],
                    'IBMPlexMono-Light-14' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'IBMPlexMono-Light-14.fnt'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'IBMPlexMono-Light-14',
                        ]),
                    ],
                    'IBMPlexMono-Light-15' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'IBMPlexMono-Light-15.fnt'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'IBMPlexMono-Light-15',
                        ]),
                    ],
                    'IBMPlexMono-Light-16' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'IBMPlexMono-Light-16.fnt'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'IBMPlexMono-Light-16',
                        ]),
                    ],
                    'IBMPlexMono-Medium-12' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'IBMPlexMono-Medium-12.fnt'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'IBMPlexMono-Medium-12',
                        ]),
                    ],
                    'IBMPlexMono-Medium-13' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'IBMPlexMono-Medium-13.fnt'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'IBMPlexMono-Medium-13',
                        ]),
                    ],
                    'IBMPlexMono-Medium-14' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'IBMPlexMono-Medium-14.fnt'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'IBMPlexMono-Medium-14',
                        ]),
                    ],
                    'IBMPlexMono-Medium-15' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'IBMPlexMono-Medium-15.fnt'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'IBMPlexMono-Medium-15',
                        ]),
                    ],
                    'IBMPlexMono-Medium-16' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'IBMPlexMono-Medium-16.fnt'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'IBMPlexMono-Medium-16',
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
