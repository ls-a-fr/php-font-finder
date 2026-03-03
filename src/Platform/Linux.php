<?php

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Contracts\FontPlatform;
use Symfony\Component\Process\Process;

class Linux implements FontPlatform {
    public static function getFontDirectories(): array
    {
        $homeDir = getenv('HOME');
        return [
            $homeDir . \DIRECTORY_SEPARATOR . '.fonts', // user
            $homeDir . \DIRECTORY_SEPARATOR . '.local/share/fonts',
            "/usr/local/share/fonts", // local shared
            "/usr/share/fonts", // system
            "/usr/X11R6/lib/X11/fonts" // X
        ];
    }

    public static function getSystemInformation(): SystemInformation
    {
        $output = strtolower(php_uname('m'));

        if(\str_contains($output, 'aarch64')) {
            return new SystemInformation(SystemInformation::OS_LINUX, null, 'arm64');
        } else if(\str_contains($output, 'x86_64')) {
            return new SystemInformation(SystemInformation::OS_LINUX, null, 'amd64');
        } else if(\str_contains($output, 'armv7')) {
            return new SystemInformation(SystemInformation::OS_LINUX, null, 'armv7');
        }

        \trigger_error('Could not detect architecture, fallback to amd64');
        return new SystemInformation(SystemInformation::OS_LINUX, null, 'amd64');
    }
}