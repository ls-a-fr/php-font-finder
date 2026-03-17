<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\Font;
use PHPUnit\Framework\Attributes\DataProvider;

final class TrueTypeCollectionTest extends BaseTestCase
{
    protected static $folderMicrosoft = 'ttc-microsoft';

    protected static $folderApple = 'ttc-apple';

    public static function microsoftValidDataProvider(): array
    {
        $fonts = [];
        if (self::createFullPath(self::$folderMicrosoft, 'cambria.ttc') !== false) {
            $fonts = \array_merge($fonts, [
                'Cambria' => [
                    new Font([
                        'filename' => self::createFullPath(self::$folderMicrosoft, 'cambria.ttc'),
                        'weight' => 400,
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Cambria',
                    ]),
                ],
                'Cambria Math' => [
                    new Font([
                        'filename' => self::createFullPath(self::$folderMicrosoft, 'cambria.ttc'),
                        'weight' => 400,
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Cambria Math',
                    ]),
                ],
            ]);
        }

        if (self::createFullPath(self::$folderMicrosoft, 'YuGothB.ttc') !== false) {
            $fonts = \array_merge($fonts, [
                'Yu Gothic' => [
                    new Font([
                        'filename' => self::createFullPath(self::$folderMicrosoft, 'YuGothB.ttc'),
                        'weight' => 700,
                        'italic' => false,
                        'bold' => true,
                        'name' => 'Yu Gothic',
                    ]),
                ],
                'Yu Gothic UI' => [
                    new Font([
                        'filename' => self::createFullPath(self::$folderMicrosoft, 'YuGothB.ttc'),
                        'weight' => 700,
                        'italic' => false,
                        'bold' => true,
                        'name' => 'Yu Gothic UI',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderMicrosoft, 'YuGothB.ttc'),
                        'weight' => 600,
                        'italic' => false,
                        'bold' => true,
                        'name' => 'Yu Gothic UI',
                    ]),
                ],
            ]);
        }

        return [[$fonts]];
    }

    public static function appleValidDataProvider(): array
    {
        $fonts = [];
        if (self::createFullPath(self::$folderApple, 'Apple Color Emoji.ttc') !== false) {
            // Apple Color Emoji is heavy.
            ini_set('memory_limit', '2G');
            $fonts = \array_merge($fonts, [
                'Apple Color Emoji' => [
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'Apple Color Emoji.ttc'),
                        'weight' => 400,
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Apple Color Emoji',
                    ]),
                ],
                '.Apple Color Emoji UI' => [
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'Apple Color Emoji.ttc'),
                        'weight' => 400,
                        'italic' => false,
                        'bold' => false,
                        'name' => '.Apple Color Emoji UI',
                    ]),
                ],
            ]);
        }
        if (self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc') !== false) {
            $fonts = \array_merge($fonts, [
                'Apple SD Gothic Neo' => [
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 400,
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Apple SD Gothic Neo',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 500,
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Apple SD Gothic Neo',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 600,
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Apple SD Gothic Neo',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 700,
                        'italic' => false,
                        'bold' => true,
                        'name' => 'Apple SD Gothic Neo',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 300,
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Apple SD Gothic Neo',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 100,
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Apple SD Gothic Neo',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 200,
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Apple SD Gothic Neo',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 800,
                        'italic' => false,
                        'bold' => true,
                        'name' => 'Apple SD Gothic Neo',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 900,
                        'italic' => false,
                        'bold' => true,
                        'name' => 'Apple SD Gothic Neo',
                    ]),
                ],
                '.Apple SD Gothic NeoI' => [
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 400,
                        'italic' => false,
                        'bold' => false,
                        'name' => '.Apple SD Gothic NeoI',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 500,
                        'italic' => false,
                        'bold' => false,
                        'name' => '.Apple SD Gothic NeoI',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 600,
                        'italic' => false,
                        'bold' => false,
                        'name' => '.Apple SD Gothic NeoI',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 700,
                        'italic' => false,
                        'bold' => true,
                        'name' => '.Apple SD Gothic NeoI',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 300,
                        'italic' => false,
                        'bold' => false,
                        'name' => '.Apple SD Gothic NeoI',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 100,
                        'italic' => false,
                        'bold' => false,
                        'name' => '.Apple SD Gothic NeoI',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 200,
                        'italic' => false,
                        'bold' => false,
                        'name' => '.Apple SD Gothic NeoI',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 800,
                        'italic' => false,
                        'bold' => true,
                        'name' => '.Apple SD Gothic NeoI',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AppleSDGothicNeo.ttc'),
                        'weight' => 900,
                        'italic' => false,
                        'bold' => true,
                        'name' => '.Apple SD Gothic NeoI',
                    ]),
                ],
            ]);
        }
        if (self::createFullPath(self::$folderApple, 'AquaKana.ttc') !== false) {
            $fonts = \array_merge($fonts, [
                '.Aqua Kana' => [
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AquaKana.ttc'),
                        'weight' => 300,
                        'italic' => false,
                        'bold' => false,
                        'name' => '.Aqua Kana',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'AquaKana.ttc'),
                        'weight' => 600,
                        'italic' => false,
                        'bold' => true,
                        'name' => '.Aqua Kana',
                    ]),
                ],
            ]);
        }
        if (self::createFullPath(self::$folderApple, 'Avenir Next Condensed.ttc') !== false) {
            $fonts = \array_merge($fonts, [
                'Avenir Next Condensed' => [
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'Avenir Next Condensed.ttc'),
                        'weight' => 700,
                        'italic' => false,
                        'bold' => true,
                        'name' => 'Avenir Next Condensed',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'Avenir Next Condensed.ttc'),
                        'weight' => 700,
                        'italic' => true,
                        'bold' => true,
                        'name' => 'Avenir Next Condensed',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'Avenir Next Condensed.ttc'),
                        'weight' => 600,
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Avenir Next Condensed',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'Avenir Next Condensed.ttc'),
                        'weight' => 600,
                        'italic' => true,
                        'bold' => false,
                        'name' => 'Avenir Next Condensed',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'Avenir Next Condensed.ttc'),
                        'weight' => 400,
                        'italic' => true,
                        'bold' => false,
                        'name' => 'Avenir Next Condensed',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'Avenir Next Condensed.ttc'),
                        'weight' => 500,
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Avenir Next Condensed',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'Avenir Next Condensed.ttc'),
                        'weight' => 500,
                        'italic' => true,
                        'bold' => false,
                        'name' => 'Avenir Next Condensed',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'Avenir Next Condensed.ttc'),
                        'weight' => 400,
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Avenir Next Condensed',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'Avenir Next Condensed.ttc'),
                        'weight' => 900,
                        'italic' => false,
                        'bold' => true,
                        'name' => 'Avenir Next Condensed',
                    ]),
                    new Font([
                        'filename' => self::createFullPath(self::$folderApple, 'Avenir Next Condensed.ttc'),
                        'weight' => 275, // Yes that's unusual, but expected
                        'italic' => false,
                        'bold' => false,
                        'name' => 'Avenir Next Condensed',
                    ]),
                ],
            ]);
        }

        return [[$fonts]];
    }

    #[DataProvider('microsoftValidDataProvider')]
    public function test_fonts_microsoft(array $expected): void
    {
        $this->test(self::$folderMicrosoft, $expected);
    }

    #[DataProvider('appleValidDataProvider')]
    public function test_fonts_apple(array $expected): void
    {
        $this->test(self::$folderApple, $expected);
    }
}
