<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Platform;

use Lsa\Font\Finder\Contracts\FontPlatform;
use Symfony\Component\Process\Process;

/**
 * MacOS Operating System definition
 */
class Darwin implements FontPlatform
{
    public static function getFontDirectories(): array
    {
        $homeDir = getenv('HOME');

        return [
            // User
            $homeDir.\DIRECTORY_SEPARATOR.'Library'.\DIRECTORY_SEPARATOR.'Fonts',
            $homeDir.\DIRECTORY_SEPARATOR.'Library'.\DIRECTORY_SEPARATOR.'FontCollection',
            // Local
            '/Library/Fonts/',
            // System
            '/System/Library/Fonts/',
            // Network
            '/Network/Library/Fonts/',
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
        if (\str_contains($output, 'arm64') === true) {
            return new SystemInformation(SystemInformation::OS_DARWIN, null, 'arm64');
        }

        return new SystemInformation(SystemInformation::OS_DARWIN, null, 'amd64');
    }
}
