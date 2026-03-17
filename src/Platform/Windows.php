<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Contracts\FontPlatform;
use Symfony\Component\Process\Process;

/**
 * Windows Operating System definition
 */
class Windows implements FontPlatform
{
    public static function getFontDirectories(): array
    {
        $windirs = self::getWinDirectories();

        $directories = [];
        foreach ($windirs as $windir) {
            foreach (['FONTS', 'PSFONTS'] as $fontFolderName) {
                $fontPath = $windir.\DIRECTORY_SEPARATOR.$fontFolderName;
                if (is_dir($fontPath) === true) {
                    $directories[] = $fontPath;
                }
            }
        }

        return $directories;
    }

    // Personal opinion: this method is readable, and cyclomatic complexity algorithm is wrong in this case
    // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
    public static function getSystemInformation(): SystemInformation
    {
        $arch = strtolower(php_uname('m'));
        $architecture = '';

        switch ($arch) {
            // Windows x64
            case 'amd64':
            case 'x86_64':
                $architecture = 'amd64';
                break;

                // Windows ARM64
            case 'arm64':
            case 'aarch64':
                $architecture = 'arm64';
                break;

                // Windows 32-bit
            case 'i386':
            case 'i486':
            case 'i586':
            case 'i686':
            case 'x86':
                $architecture = 'i386';
                break;

                // Unknown
            default:
                trigger_error('Unknown Windows architecture '.$arch.', falling back to i386');
                $architecture = 'i386';
                break;
        }

        return new SystemInformation(SystemInformation::OS_WINDOWS, null, $architecture);
    }

    /**
     * Returns Windows font directories, as a list of strings.
     *
     * Note: return type is to please PHPStan which changes views every few runs
     *
     * @return list<non-falsy-string>|array{string}
     */
    private static function getWinDirectories(): array
    {
        if (\str_starts_with(PHP_OS, 'Windows 9') === true) {
            $process = new Process(['command.com', '/c', 'echo', '%windir%']);
        } else {
            $process = new Process(['cmd.exe', '/c', 'echo', '%windir%']);
        }

        $process->run();

        if ($process->isSuccessful() === true) {
            return [trim(str_replace('"', '', $process->getOutput()))];
        }

        if (\str_ends_with(PHP_OS, 'NT') === true) {
            $windir = 'WINNT';
        } else {
            $windir = 'WINDOWS';
        }

        $process = new Process([
            'powershell.exe',
            'Get-PSDrive',
            '-PSProvider',
            'FileSystem',
            '|',
            'Select-Object',
            '-ExpandProperty',
            'Name',
        ]);

        $process->run();
        if ($process->isSuccessful() === true) {
            $driveLetters = $process->getOutput();
        } else {
            $driveLetters = implode("\n", ['A', 'B', 'C', 'D', 'E']);
        }

        $windirs = array_filter(
            array_map(fn ($d) => $d.':'.\DIRECTORY_SEPARATOR.$windir, explode("\n", $driveLetters)),
            fn ($d) => is_dir($d)
        );

        return \array_values($windirs);
    }
}
