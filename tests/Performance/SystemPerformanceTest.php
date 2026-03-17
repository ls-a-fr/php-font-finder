<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Tests\Performance;

use Lsa\Font\Finder\FontFileFinder;

final class SystemPerformanceTest
{
    /**
     * @Revs(100)
     *
     * @Iterations(5)
     */
    public function benchSystem()
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
            // System fonts
            ->addSystemFonts();
    }
}
