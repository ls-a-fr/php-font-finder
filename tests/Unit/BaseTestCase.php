<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Unit;

use Lsa\Font\Finder\FontFileFinder;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected static function createFullPath(string $folder, string $fileName)
    {
        return realpath(str_replace('/', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            'samples',
            $folder,
            $fileName,
        ])));
    }

    protected function test(string $folder, array $expected)
    {
        // Act
        [$actual, $errors] = FontFileFinder::init()
            ->addDirectoryRecursive(implode(\DIRECTORY_SEPARATOR, [
                __DIR__,
                '..',
                'samples',
                $folder,
            ]))
            ->exceptExact('.gitkeep')
            ->get('fonts', 'errors');

        // Special BSD operation:
        // Prevents failure in BSD filesystems: file order in not consistent with other OSes
        foreach (array_keys($actual) as $key) {
            usort($actual[$key], fn ($a, $b) => $a->filename <=> $b->filename);
        }
        foreach (array_keys($expected) as $key) {
            usort($expected[$key], fn ($a, $b) => $a->filename <=> $b->filename);
        }

        // Assert
        $this->assertEqualsCanonicalizing(array_keys($expected), array_keys($actual));

        foreach ($expected as $fontName => $fontDerivatives) {
            foreach ($fontDerivatives as $j => $derivative) {
                $this->assertEquals($derivative, $actual[$fontName][$j]);
            }
        }

        $this->assertEmpty($errors);
    }
}
