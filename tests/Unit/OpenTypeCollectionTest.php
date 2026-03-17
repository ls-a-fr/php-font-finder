<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class OpenTypeCollectionTest extends BaseTestCase
{
    protected static $folder = 'otc';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'Noto Sans CJK JP' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Bold.otc'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Noto Sans CJK JP',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Light.otc'),
                            'weight' => 300,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Noto Sans CJK JP',
                        ]),
                    ],
                    'Noto Sans CJK KR' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Bold.otc'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Noto Sans CJK KR',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Light.otc'),
                            'weight' => 300,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Noto Sans CJK KR',
                        ]),
                    ],
                    'Noto Sans CJK SC' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Bold.otc'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Noto Sans CJK SC',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Light.otc'),
                            'weight' => 300,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Noto Sans CJK SC',
                        ]),
                    ],
                    'Noto Sans CJK TC' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Bold.otc'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Noto Sans CJK TC',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Light.otc'),
                            'weight' => 300,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Noto Sans CJK TC',
                        ]),
                    ],
                    'Noto Sans CJK HK' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Bold.otc'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Noto Sans CJK HK',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Light.otc'),
                            'weight' => 300,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Noto Sans CJK HK',
                        ]),
                    ],
                    'Noto Sans Mono CJK JP' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Bold.otc'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Noto Sans Mono CJK JP',
                        ]),
                    ],
                    'Noto Sans Mono CJK KR' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Bold.otc'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Noto Sans Mono CJK KR',
                        ]),
                    ],
                    'Noto Sans Mono CJK SC' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Bold.otc'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Noto Sans Mono CJK SC',
                        ]),
                    ],
                    'Noto Sans Mono CJK TC' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Bold.otc'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Noto Sans Mono CJK TC',
                        ]),
                    ],
                    'Noto Sans Mono CJK HK' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NotoSansCJK-Bold.otc'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Noto Sans Mono CJK HK',
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
