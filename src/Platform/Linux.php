<?php
/*
 * This file includes original work licensed to the ASF: the getFontDirectories() method.
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

/**
 * Linux Operating System definition
 */
class Linux implements FontPlatform
{
    public static function getFontDirectories(): array
    {
        $homeDir = getenv('HOME');

        return [
            // User
            $homeDir.\DIRECTORY_SEPARATOR.'.fonts',
            $homeDir.\DIRECTORY_SEPARATOR.'.local/share/fonts',
            // Local Shared
            '/usr/local/share/fonts',
            // System
            '/usr/share/fonts',
            // X
            '/usr/X11R6/lib/X11/fonts',
        ];
    }

    public static function getSystemInformation(): SystemInformation
    {
        $output = strtolower(php_uname('m'));

        if (\str_contains($output, 'aarch64') === true) {
            return new SystemInformation(SystemInformation::OS_LINUX, null, 'arm64');
        } elseif (\str_contains($output, 'x86_64') === true) {
            return new SystemInformation(SystemInformation::OS_LINUX, null, 'amd64');
        } elseif (\str_contains($output, 'armv7') === true) {
            return new SystemInformation(SystemInformation::OS_LINUX, null, 'armv7');
        }

        \trigger_error('Could not detect architecture, fallback to amd64');

        return new SystemInformation(SystemInformation::OS_LINUX, null, 'amd64');
    }
}
