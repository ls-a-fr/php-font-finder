<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Meta;

use DirectoryIterator;
use Lsa\Font\Finder\FontFileFinder;
use PHPUnit\Framework\TestCase;

class MetaTest extends TestCase
{
    public function test_values()
    {
        // Act
        [$count, $errors, $fonts] = FontFileFinder::init()
            ->silent()
            ->exceptExact([
                '.gitkeep',
                '.gitignore',
                'README.md',
            ])
            // Local sample fonts
            ->addDirectoryRecursive(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'samples']))
            ->get('count', 'errors', 'fonts');

        // Assert.
        $this->assertEmpty($errors);
        // You must change values here if you add some fonts
        $this->assertEquals(300, $count);
        $this->assertEquals(128, count($fonts));
    }

    public function test_decoders()
    {
        $decodersPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'src', 'Decoders']);
        $iterator = new DirectoryIterator($decodersPath);

        $expected = [];
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isDir()) {
                continue;
            }
            $expected[] = \str_replace('.php', 'Test.php', $fileInfo->getFilename());
        }

        $testDecodersPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..']);
        $iterator = new DirectoryIterator($testDecodersPath);

        $actual = [];
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isDir()) {
                continue;
            }
            if (\str_ends_with($fileInfo->getFilename(), 'Test.php') === false) {
                continue;
            }
            $actual[] = $fileInfo->getFilename();
        }

        $this->assertEqualsCanonicalizing($expected, $actual);

    }
}
