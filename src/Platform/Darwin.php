<?php

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Contracts\FontPlatform;
use Symfony\Component\Process\Process;

class Darwin implements FontPlatform
{
    public static function getFontDirectories(): array
    {
        $homeDir = getenv('HOME');
        return [
            $homeDir . \DIRECTORY_SEPARATOR . 'Library' . \DIRECTORY_SEPARATOR . 'Fonts', // user
            "/Library/Fonts/", // local
            "/System/Library/Fonts/", // system
            "/Network/Library/Fonts/" // network
        ];
    }

    public static function getSystemInformation(): SystemInformation
    {
        $process = new Process(['machine']);
        $process->run();
        if ($process->isSuccessful() === false) {
            return new SystemInformation(SystemInformation::OS_DARWIN, null, 'amd64');
        }

        $output = $process->getOutput();
        if(\str_contains($output, 'arm64')) {
            return new SystemInformation(SystemInformation::OS_DARWIN, null, 'arm64');
        }
        return new SystemInformation(SystemInformation::OS_DARWIN, null, 'amd64');
    }
}
