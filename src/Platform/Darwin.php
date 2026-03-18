<?php
/*
 * This file includes original work licensed to the ASF: the getFontDirectories() method.
 * Modifications to this code include:
 * - Added FontCollection directory
 * 
 * Original work license:
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
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
