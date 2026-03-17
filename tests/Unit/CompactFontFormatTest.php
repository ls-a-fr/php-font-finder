<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class CompactFontFormatTest extends BaseTestCase
{
    protected static $folder = 'cff';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'RamatSharon' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'TestSupplementEncoding.cff'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'RamatSharon',
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
