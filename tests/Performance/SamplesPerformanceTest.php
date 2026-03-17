<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Performance;

use Lsa\Font\Finder\FontFileFinder;

final class SamplesPerformanceTest
{
    /**
     * @Revs(100)
     *
     * @Iterations(5)
     */
    public function benchSamples()
    {
        FontFileFinder::init()
            ->silent()
            ->except([
                '/\.collection$/',
                '/\.CompositeFont$/',
                '/\.xml$/',
                '/\.dat$/',
                '/LTMM$/',
            ])
            ->exceptExact([
                'desktop.ini',
                '.DS_Store',
                '.gitignore',
                '.gitkeep',
                'README.md',
            ])
            // Local sample fonts
            ->addDirectoryRecursive(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'samples']));
    }
}
