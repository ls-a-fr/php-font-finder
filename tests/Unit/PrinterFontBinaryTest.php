<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class PrinterFontBinaryTest extends BaseTestCase
{
    protected static $folder = 'pfb';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'Courier 10 Pitch' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'c0419bt_.pfb'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Courier 10 Pitch',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'c0582bt_.pfb'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Courier 10 Pitch',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'c0583bt_.pfb'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Courier 10 Pitch',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'c0611bt_.pfb'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Courier 10 Pitch',
                        ]),
                    ],
                    'Bitstream Charter' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'c0632bt_.pfb'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Bitstream Charter',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'c0633bt_.pfb'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Bitstream Charter',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'c0648bt_.pfb'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Bitstream Charter',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'c0649bt_.pfb'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Bitstream Charter',
                        ]),
                    ],
                    // This font is not included with pfm file for testing purposes
                    'Nimbus Roman No9 L' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'n021003l.pfb'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Nimbus Roman No9 L',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'n021004l.pfb'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Nimbus Roman No9 L',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'n021023l.pfb'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Nimbus Roman No9 L',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'n021024l.pfb'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Nimbus Roman No9 L',
                        ]),
                    ],
                    'NimbusMonL' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'n022003l.pfb'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'NimbusMonL',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'n022004l.pfb'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'NimbusMonL',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'n022023l.pfb'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'NimbusMonL',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'n022024l.pfb'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'NimbusMonL',
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
