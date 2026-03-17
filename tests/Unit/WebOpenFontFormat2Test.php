<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class WebOpenFontFormat2Test extends BaseTestCase
{
    protected static $folder = 'woff2';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'Andika' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Andika', 'Andika-Bold.woff2'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Andika',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Andika', 'Andika-BoldItalic.woff2'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Andika',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Andika', 'Andika-Italic.woff2'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Andika',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Andika', 'Andika-Medium.woff2'),
                            'weight' => 500,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Andika',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Andika', 'Andika-MediumItalic.woff2'),
                            'weight' => 500,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Andika',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Andika', 'Andika-Regular.woff2'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Andika',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Andika', 'Andika-SemiBold.woff2'),
                            'weight' => 600,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Andika',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Andika', 'Andika-SemiBoldItalic.woff2'),
                            'weight' => 600,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Andika',
                        ]),
                    ],
                    'Charis' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Charis', 'Charis-Bold.woff2'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Charis',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Charis', 'Charis-BoldItalic.woff2'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Charis',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Charis', 'Charis-Italic.woff2'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Charis',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Charis', 'Charis-Medium.woff2'),
                            'weight' => 500,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Charis',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Charis', 'Charis-MediumItalic.woff2'),
                            'weight' => 500,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Charis',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Charis', 'Charis-Regular.woff2'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Charis',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Charis', 'Charis-SemiBold.woff2'),
                            'weight' => 600,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Charis',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder.\DIRECTORY_SEPARATOR.'Charis', 'Charis-SemiBoldItalic.woff2'),
                            'weight' => 600,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Charis',
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
