<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class PostScriptTest extends BaseTestCase
{
    protected static $folder = 'ps';

    public static function validDataProvider(): array
    {
        return [
            [
                [
                    'Hershey-Plain-Triplex-Italic' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'hrplti.ps'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Hershey-Plain-Triplex-Italic',
                        ]),
                    ],
                    'Hershey-Script-Complex' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'hrscc.ps'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Hershey-Script-Complex',
                        ]),
                    ],
                    'Hershey-Script-Simplex' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'hrscs.ps'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Hershey-Script-Simplex',
                        ]),
                    ],
                    'Utopia' => [
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'putb.ps'),
                            'weight' => 700,
                            'italic' => false,
                            'bold' => true,
                            'name' => 'Utopia',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'putbi.ps'),
                            'weight' => 700,
                            'italic' => true,
                            'bold' => true,
                            'name' => 'Utopia',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'putr.ps'),
                            'weight' => 400,
                            'italic' => false,
                            'bold' => false,
                            'name' => 'Utopia',
                        ]),
                        new Font([
                            'filename' => self::createFullPath(self::$folder, 'putri.ps'),
                            'weight' => 400,
                            'italic' => true,
                            'bold' => false,
                            'name' => 'Utopia',
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
