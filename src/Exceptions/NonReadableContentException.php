<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Exceptions;

/**
 * Thrown when a file cannot be read (permissions, non-existant file, ...)
 */
class NonReadableContentException extends FontFinderException {}
