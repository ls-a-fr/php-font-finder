<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class Type1Test extends BaseTestCase
{
    protected static $folder = 't1';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'C059' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'C059-BdIta.t1'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'C059',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'C059-Bold.t1'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'C059',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'C059-Italic.t1'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'C059',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'C059-Roman.t1'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'C059',
                        ]),
                    ],
                    'Nimbus Mono PS' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NimbusMonoPS-Bold.t1'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Nimbus Mono PS',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NimbusMonoPS-BoldItalic.t1'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Nimbus Mono PS',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NimbusMonoPS-Italic.t1'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Nimbus Mono PS',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'NimbusMonoPS-Regular.t1'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Nimbus Mono PS',
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
