<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class TrueTypeFontTest extends BaseTestCase
{
    protected static $folder = 'ttf';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'Galatia SIL' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'GalSILB.ttf'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Galatia SIL',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'GalSILR.ttf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Galatia SIL',
                        ]),
                    ],
                    'Gentium' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Gentium-Bold.ttf'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Gentium',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Gentium-BoldItalic.ttf'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Gentium',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Gentium-ExtraBold.ttf'),
                            'weight' => 800,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Gentium',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Gentium-ExtraBoldItalic.ttf'),
                            'weight' => 800,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Gentium',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Gentium-Italic.ttf'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Gentium',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Gentium-Medium.ttf'),
                            'weight' => 500,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Gentium',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Gentium-MediumItalic.ttf'),
                            'weight' => 500,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Gentium',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Gentium-Regular.ttf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Gentium',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Gentium-SemiBold.ttf'),
                            'weight' => 600,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Gentium',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Gentium-SemiBoldItalic.ttf'),
                            'weight' => 600,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Gentium',
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
