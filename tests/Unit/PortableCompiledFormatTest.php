<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class PortableCompiledFormatTest extends BaseTestCase
{
    protected static $folder = 'pcf';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'Helvetica' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'helvB08.pcf'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Helvetica',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'helvB12.pcf'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Helvetica',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'helvBO10.pcf'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Helvetica',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'helvO10.pcf'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Helvetica',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'helvR18.pcf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Helvetica',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'helvR24.pcf'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Helvetica',
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
