# Font Finder

[![Build Woff2 Decompress utility](https://github.com/ls-a-fr/php-font-finder/actions/workflows/woff2.yml/badge.svg)](https://github.com/ls-a-fr/php-font-finder/actions/workflows/woff2.yml)
[![Test and bench](https://github.com/ls-a-fr/php-font-finder/actions/workflows/test-and-bench.yml/badge.svg)](https://github.com/ls-a-fr/php-font-finder/actions/workflows/test-and-bench.yml)

This documentation is also available in these languages:
- [Français](docs/LISEZMOI.md)

This library is an universal font finder: with _almost every_ operating system, containing _almost every_ font format, returns an array of standardized information. It aims to be (very) quick and retrieves minimal but sufficient information on fonts.

If you like this package and want to show you support, star this project: it means a lot to us!

## Quick example

```php
$fonts = FontFileFinder::init()
    // Load system fonts (OS-dependent)
    ->addSystemFonts()
    // Load some directory
    ->addDirectoryRecursive(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'tests', 'samples']))
    ->get();
```

In previous example, `$fonts` is an array of `Font` objects, aggregated by font name, with these properties:
- `filename`: where the font lies
- `name`: family name
- `bold`: bold flag (boolean)
- `italic`: italic flag (boolean)
- `weight`: CSS style weight, from 100 to 900

Find more in [documentation below](#documentation).

## Installation

This library is available on Composer. Install it with:
```sh
composer require ls-a/font-finder
```

## Operating system support

This library is currently tested on:
- Windows x64
- Windows i386
- Windows ARM
- macOS amd64
- macOS arm64
- Linux amd64
- Linux arm64
- Linux armv7
- FreeBSD amd64
- FreeBSD arm64
- OpenBSD amd64
- OpenBSD arm64
- Solaris amd64

For more information, feel free to check [test-and-bench action](.github/workflows/test-and-bench.yml).

## Font format support

This library currently retrieves font information in these formats:
- AdobeFontMetrics (*.afm, in conjuction with PFA/PFB/T1/PS)
- BitstreamSpeedo (*.spd)
- BsdVgaFont (*.fnt, but BSD ones, not Microsoft)
- CompactFontFormat (*.cff, both PostScript and binary)
- DataForkTrueTypeFont (*.dfont)
- EmbeddedOpenType (*.eot)
- GlyphBitmapDistributionFormat (*.bdf)
- JimHersheyFont (*.jhf)
- OpenTypeBitmap (*.otb)
- OpenTypeCollection (*.otc)
- OpenTypeFont (*.otf)
- PcScreenFont (*.psf)
- PcScreenFontUnicodeCompressed (*.psfu.gz, usually found with *.psf.fz extension)
- PcScreenFontCompressed (*.psf.gz)
- PcScreenFontUnicode (*.psfu)
- PortableCompiledFormat (*.pcf)
- PortableCompiledFormatCompressed (*.pcf.gz)
- PostScript (*.ps)
- PrinterFontAscii (*.pfa)
- PrinterFontBinary (*.pfb)
- PrinterFontMetrics (*.pfm, in conjuction with PFA/PFB/T1/PS)
- ScalableVectorGraphics (*.svg)
- ScalableVectorGraphicsCompressed (*.svgz)
- Type1 (*.t1)
- TrueTypeCollection (*.ttc)
- TrueTypeFont (*.ttf)
- WebOpenFontFormat (*.woff)
- WebOpenFontFormat2 (*.woff2)
- WindowsBitmapFontCollection (*.fon)

## Why?

A rabbit hole, really. We needed to check some configuration option for our next package about XSL-FO, especially the `<auto-detect/>` option in Apache FOP configuration file. This option allows to retrieve all fonts registered in current OS.  

First, we wanted to validate some values against this option. Then, we found old fonts on some operating systems does not publicize their name in the filename. And after some search on Composer, it seems no package does that. [Phenx PHP Font Lib](https://packagist.org/packages/phenx/php-font-lib) handles TrueType, OpenType and WOFF but that's all. And it is very slow with hundreds of fonts.  

After some time and digging through history of fonts, this package became more and more a tribute to evolution of fonts. And it's a pleasure to discover so many ways to embed characters and typography.

If you can read French or can use a translator, find more about how we did it [here](docs/DEROULEMENT.md).  
If you wish to browse through samples collection used in this package, see their provenance and our thanks, you may go [here](samples/README.md).

## Performance

Performance is checked with GitHub runners, in [test-and-bench action](.github/workflows/test-and-bench.yml).  
Data displayed below are extracted from a single run, with following architectures:
- Windows: Windows amd64
- MacOS: MacOS amd64
- Linux: Linux amd64
- FreeBSD: Virtual machine amd64 inside Linux container
- OpenBSD: Virtual machine amd64 inside Linux container
- Solaris: Virtual machine amd64 inside Linux container

**Note:** Obviously, you should get better results in *BSD and Solaris with bare metal environment.  

Time displayed is meant to be *per file*: it's an average of every font available in samples + current operating system.

| Filetype    | Windows | Mac   | Linux | FreeBSD | OpenBSD | Solaris |
| ----------- | ------- | ----- | ----- | ------- | ------- | ------- |
| bdf         | 3ms     | 48µs  | 85µs  | 81µs    | 104µs   | 81µs    |
| cff         | 37µs    | 17µs  | 103µs | 32µs    | 39µs    | 19µs    |
| cff (ps)    | 2ms     | 621µs | 9ms   | 1ms     | 2ms     | 1ms     |
| dfont       | 129µs   | 49µs  | 405µs | 114µs   | 141µs   | 91µs    |
| eot         | 123µs   | 46µs  | 401µs | 96µs    | 118µs   | 79µs    |
| fnt (BSD)   | 5µs     | 2µs   | 7µs   | 3µs     | 4µs     | 3µs     |
| fon (win)   | 19µs    | 5µs   | 27µs  | 7µs     | 10µs    | 7µs     |
| jhf         | 4µs     | 2µs   | 5µs   | 2µs     | 3µs     | 2µs     |
| pcf         | 75µs    | 31µs  | 246µs | 57µs    | 70µs    | 50µs    |
| pcf.gz      | 80µs    | 43µs  | 230µs | 68µs    | 80µs    | 57µs    |
| pfa         | 60µs    | 36µs  | 41µs  | 24µs    | 75µs    | 58µs    |
| pfb         | 122µs   | 83µs  | 96µs  | 40µs    | 88µs    | 81µs    |
| psf         | 4µs     | 2µs   | 5µs   | 2µs     | 3µs     | 2µs     |
| psf.gz      | 7µs     | 5µs   | 9µs   | 6µs     | 7µs     | 6µs     |
| psfu        | 3µs     | 1µs   | 5µs   | 2µs     | 3µs     | 3µs     |
| psfu.gz     | 9µs     | 6µs   | 11µs  | 7µs     | 8µs     | 7µs     |
| spd         | 4µs     | 2µs   | 7µs   | 2µs     | 3µs     | 3µs     |
| svg         | 12µs    | 11µs  | 13µs  | 8µs     | 10µs    | 9µs     |
| svgz        | 129µs   | 132µs | 102µs | 117µs   | 121µs   | 105µs   |
| ttc         | 372µs   | 287µs | 2ms   | 483µs   | 549µs   | 397µs   |
| ttf         | 189µs   | 145µs | 1ms   | 272µs   | 327µs   | 214µs   |
| woff        | 5ms     | 3ms   | 7ms   | 5ms     | 5ms     | 5ms     |
| woff2       | 210ms   | 21ms  | 22ms  | 30ms    | 70ms    | 27ms    |

You may check metrics in [the specific run used for these metrics](https://github.com/ls-a-fr/php-font-finder/actions/runs/23244358247/job/67568710047).  

If you like PHPBench and would prefer PHPBench metrics because you also care about memory peak, here you go:

### Windows amd64

| benchmark              | subject      | set | revs | its | mem_peak | mode    | rstdev |
| ---------------------- | ------------ | --- | ---- | --- | -------- | ------- | ------ |
| SamplesPerformanceTest | benchSamples |     | 100  | 5   | 1.946mb  | 7.184ms | ±0.61% |
| SystemPerformanceTest  | benchSystem  |     | 100  | 5   | 1.946mb  | 5.251μs | ±3.50% |

### Linux amd64

| benchmark              | subject      | set | revs | its | mem_peak | mode    | rstdev |
| ---------------------- | ------------ | --- | ---- | --- | -------- | ------- | ------ |
| SystemPerformanceTest  | benchSystem  |     | 100  | 5   | 2.189mb  | 5.843μs | ±1.82% |
| SamplesPerformanceTest | benchSamples |     | 100  | 5   | 2.189mb  | 1.645ms | ±0.29% |

### MacOS amd64
| benchmark              | subject      | set | revs | its | mem_peak | mode    | rstdev |
| ---------------------- | ------------ | --- | ---- | --- | -------- | ------- | ------ |
| SamplesPerformanceTest | benchSamples |     | 100  | 5   | 1.950mb  | 1.064ms | ±4.56% |
| SystemPerformanceTest  | benchSystem  |     | 100  | 5   | 1.950mb  | 3.099μs | ±9.82% |

### FreeBSD amd64 (VM)

| benchmark              | subject      | set | revs | its | mem_peak | mode      | rstdev |
| ---------------------- | ------------ | --- | ---- | --- | -------- | --------- | ------ |
| SamplesPerformanceTest | benchSamples |     | 100  | 5   | 1.955mb  | 346.581μs | ±1.02% |
| SystemPerformanceTest  | benchSystem  |     | 100  | 5   | 1.955mb  | 3.367μs   | ±9.12% |

### OpenBSD amd64 (VM)

| benchmark              | subject      | set | revs | its | mem_peak | mode    | rstdev |
| ---------------------- | ------------ | --- | ---- | --- | -------- | ------- | ------ |
| SamplesPerformanceTest | benchSamples |     | 100  | 5   | 1.943mb  | 3.870ms | ±1.69% |
| SystemPerformanceTest  | benchSystem  |     | 100  | 5   | 1.943mb  | 4.224μs | ±6.37% |

### Solaris amd64 (VM)

| benchmark              | subject      | set | revs | its | mem_peak | mode    | rstdev |
| ---------------------- | ------------ | --- | ---- | --- | -------- | ------- | ------ |
| SamplesPerformanceTest | benchSamples |     | 100  | 5   | 1.948mb  | 1.463ms | ±1.63% |
| SystemPerformanceTest  | benchSystem  |     | 100  | 5   | 1.948mb  | 3.821μs | ±3.65% |

## Documentation

Every call to this library should be done on `FontFileFinder` class.

### Usage

#### Create an instance: inst()

```php
// Returns a FontFileFinder instance
$instance = FontFileFinder::inst();
```

No operation will be done until you call the `get()` method: it only creates an objet.

#### Include system fonts: addSystemFonts()

```php
// Returns a FontFileFinder instance
$instance = FontFileFinder::inst()
    ->addSystemFonts();
```

Based on current operating system, will add system font directories.

#### Include a specific directory: addDirectory()

```php
// Returns a FontFileFinder instance
$instance = FontFileFinder::inst()
    // Tip: use absolute path
    ->addDirectory(implode(DIRECTORY_SEPARATOR, [
        __DIR__,
        'samples'
    ]));
```

This method will add any file found in this directory. Note that it is not recursive.

#### Include a specific directory, recursively: addDirectoryRecursive()

```php
// Returns a FontFileFinder instance
$instance = FontFileFinder::inst()
    // Tip: use absolute path
    ->addDirectoryRecursive(implode(DIRECTORY_SEPARATOR, [
        __DIR__,
        'samples'
    ]));
```

This method will add any file found in this directory, recursively.

#### Exclude patterns: except()

```php
$instance = FontFileFinder::inst()
    // Exclude .git* files
    ->except('/\.git.*$/')
    // You may use a list
    ->except([
        '/myfolder\/*/',
        '/\.php$/'
    ]) ;
```

Except method accepts regular expressions and is tested against an absolute path.  
If you prefer to exclude based on strings, check exceptExact.

#### Exclude file names: exceptExact()

```php
$instance = FontFileFinder::inst()
    // Exclude .gitignore files
    ->exceptExact('.gitignore')
    // You may use a list
    ->exceptExact([
        '.DS_Store',
        '.gitkeep'
    ]);
```

This method checks your strings against an absolute path **with `str_ends_with`**, meaning you can exclude extensions without relying on regular expressions.

#### Get fonts

```php
// Get fonts
$fonts = FontFileFinder::init()
    // Load system fonts (OS-dependent)
    ->addSystemFonts()
    // Load some directory
    ->addDirectoryRecursive(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'tests', 'samples']))
    ->get();
```

You may want to retrieve other information, such as font count or errors. `get()` method allows to define what
you want to get:

```php
[$errors, $count, $fonts] = FontFileFinder::init()
    ->addSystemFonts()
    ->get('errors', 'count', 'fonts');
```

Currently, only `errors`, `count` and `fonts` are defined. You may define them in any order:
```php
[$count, $errors] = FontFileFinder::init()
    ->addSystemFonts()
    ->get('count', 'errors');
```

### Configuration

#### Silence errors

You may want to silence errors: by default, errors are printed to standard error output (stderr).

```php
$instance = FontFileFinder::inst()
    ->silent();
```

#### Enable performance metrics

Because sometimes, you wish to see performance metrics.

```php
$instance = FontFileFinder::inst()
    ->enableMetrics();
```

You can disable them later with `disableMetrics`.

### Advanced

#### Use a configuration array

```php
$fonts = FontFileFinder::load([
     'directories' => [
         [
             'recursive' => true,
             'path' => '/usr/local/...'
         ],
         '/home/user/...'
     ]
]);
```

Method `load` accepts `autoDetect` (boolean) or `directories`. It is not possible to get errors
and count this way, but maybe in a next release!

#### Check system fonts

If you want a quick peek on system fonts, simply write:

```php
$fonts = FontFileFinder::getSystemFonts();
```

You won't have access to any of FontFileFinder configuration, but sometimes it's just what you want.

#### Override FontDecoder

FontFileFinder relies on a class called `FontDecoder`, which:
- Run through all decoders to find a match for the current file
- Actually does the performance check

If this class is not sufficient for your needs, you may define another subclass (that you have to write)
and declare it this way:
```php
$instance = FontFileFinder::inst()
    ->setDecoderClass(MyAwesomeClass::class);
```

## Changelog

Please refer to the [CHANGELOG](CHANGELOG.md) file to see the latest changes.

## Support

We put our heart into delivering high-quality products that are accessible to everyone. If you like our work, don’t hesitate to reach out to us for your next project!

## Contributing

Contributions are governed by the [CONTRIBUTING](https://github.com/ls-a-fr/.github/CONTRIBUTING.md) file.

## Security

If you’ve found a bug or vulnerability, please contact us by email at [contact@ls-a.fr](mailto:contact@ls-a.fr) instead of opening an issue, in order to protect the security of other users.

## Credits

- Renaud Berthier

## License

The MIT License (MIT). Please see License File for more information.