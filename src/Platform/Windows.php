<?php

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Contracts\FontPlatform;
use Symfony\Component\Process\Process;

class Windows implements FontPlatform {
    public static function getFontDirectories(): array
    {
        $windirs = self::getWinDirectories();

        $directories = [];
        foreach ($windirs as $windir) {
            foreach (['FONTS', 'PSFONTS'] as $fontFolderName) {
                $fontPath = $windir . \DIRECTORY_SEPARATOR . $fontFolderName;
                if (is_dir($fontPath) === true) {
                    $directories[] = $fontPath;
                }
            }
        }
        return $directories;
    }

    private static function getWinDirectories(): array
    {
        if (\str_starts_with(PHP_OS, "Windows 9")) {
            $process = new Process(['command.com', '/c', 'echo', '%windir%']);
        } else {
            $process = new Process(['cmd.exe', '/c', 'echo', '%windir%']);
        }
        
        $process->run();

        if ($process->isSuccessful() === true) {
            return [trim(str_replace("\"", '', $process->getOutput()))];
        }

        if (\str_ends_with(PHP_OS, 'NT') === true) {
            $windir = 'WINNT';
        } else {
            $windir = 'WINDOWS';
        }

        $process = new Process(['powershell.exe', 'Get-PSDrive', '-PSProvider', 'FileSystem', '|', 'Select-Object', '-ExpandProperty', 'Name']);
        $process->run();
        if ($process->isSuccessful() === true) {
            $driveLetters = $process->getOutput();
        } else {
            $driveLetters = implode("\n", ['A', 'B', 'C', 'D', 'E']);
        }

        $windirs = array_filter(
            array_map(fn($d) => $d . ':' . \DIRECTORY_SEPARATOR . $windir, explode("\n", $driveLetters)),
            fn($d) => is_dir($d)
        );

        return $windirs;
    }
}