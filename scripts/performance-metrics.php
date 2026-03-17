<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Lsa\Font\Finder\FontDecoder;
use Lsa\Font\Finder\FontFileFinder;

// This file is a simple file (without heavy dependencies, such as )

// Prevent crash/warnings for slow computers or heavy fonts
set_time_limit(0);
ini_set('memory_limit', '2G');

// Number of iterations
$iterations = 10;

// Standard deviation function
if (! function_exists('stats_standard_deviation')) {
    /**
     * This user-land implementation follows the implementation quite strictly;
     * it does not attempt to improve the code or algorithm in any way. It will
     * raise a warning if you have fewer than 2 values in your array, just like
     * the extension does (although as an E_USER_WARNING, not E_WARNING).
     *
     * @param  bool  $sample  [optional] Defaults to false
     * @return float|bool The standard deviation or false on error.
     */
    function stats_standard_deviation(array $a, $sample = false)
    {
        $n = count($a);
        if ($n === 0) {
            trigger_error('The array has zero elements', E_USER_WARNING);

            return false;
        }
        if ($sample && $n === 1) {
            trigger_error('The array has only 1 element', E_USER_WARNING);

            return false;
        }
        $mean = array_sum($a) / $n;
        $carry = 0.0;
        foreach ($a as $val) {
            $d = ((float) $val) - $mean;
            $carry += $d * $d;
        }
        if ($sample) {
            $n--;
        }

        return sqrt($carry / $n);
    }
}

// Performance metrics start
echo "Performance metrics: start\n";
echo "------------------\n";

$globalTime = [];
for ($i = 0; $i < $iterations; $i++) {
    $time = microtime(true);
    [$count, $errors] = FontFileFinder::init()
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
        ->enableMetrics()
        // System fonts
        ->addSystemFonts()
        // Local sample fonts
        ->addDirectoryRecursive(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'tests', 'samples']))
        ->get('count', 'errors');

    $globalTime[] = microtime(true) - $time;
    echo '.';
}

echo " Done\n";
echo "------------------\n";
echo "Global results:\n";
$avgTime = 0;
foreach ($globalTime as $time) {
    $avgTime += $time;
}
$avgTime /= count($globalTime);
$stdDev = stats_standard_deviation($globalTime);

echo 'Got '.$count.' fonts in '.round($avgTime, 3)." seconds (average) \n";
echo 'Standard deviation: '.round($stdDev, 3)."\n";

echo "------------------\n";
echo "Decoder results:\n";
foreach (FontDecoder::getPerformanceMetrics()['classes'] ?? [] as $className => $data) {
    echo $className."\n";
    foreach ($data as $methodName => $d) {
        echo '- '.$methodName.': '.round($d['count'] * $d['average'], 5).' ('.$d['count'].")\n";
    }
}
echo "------------------\n";
echo "Decoder average results:\n";
foreach (FontDecoder::getPerformanceMetrics()['classes'] ?? [] as $className => $data) {
    if (isset($data['extractFontMeta']['average']) === false) {
        continue;
    }
    $d = $data['extractFontMeta']['average'];
    if ($d >= 1) {
        $d = round($d, 3);
        $unit = 's';
    } else {
        $d *= 1000;
        $unit = 'ms';
        if ($d < 1) {
            $d *= 1000;
            $unit = 'µs';
        }
        if ($d < 1) {
            $d *= 1000;
            $unit = 'ns';
        }
        if ($d < 1) {
            $d *= 1000;
            $unit = 'ps';
        }
        $d = round($d);
    }
    echo $className.': '.$d.$unit."\n";
}
echo "------------------\n";
echo 'Not found fonts + error: '.count($errors)."\n";
foreach ($errors as $error) {
    echo '- '.$error;
}
