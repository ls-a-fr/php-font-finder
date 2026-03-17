<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class CompactFontFormatPostScriptTest extends BaseTestCase
{
    protected static $folder = 'cffps';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'Garamontio-Bold' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'Garamontio-Bold.cff'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Garamontio-Bold',
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
