<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class OpenTypeFontTest extends BaseTestCase
{
    protected static $folder = 'otf';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'Noto Fangsong KSS Rotated' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoFangsongKSSRotated-Regular.otf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Noto Fangsong KSS Rotated',
                        ]),
                    ],
                    'Noto Sans' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSans-Black.otf'),
                            'weight' => 900,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Noto Sans',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSans-BlackItalic.otf'),
                            'weight' => 900,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Noto Sans',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSans-Condensed.otf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Noto Sans',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSans-CondensedMediumItalic.otf'),
                            'weight' => 500,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Noto Sans',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSans-ExtraLight.otf'),
                            'weight' => 200,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Noto Sans',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSans-ExtraLightItalic.otf'),
                            'weight' => 200,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Noto Sans',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSans-Italic.otf'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Noto Sans',
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
