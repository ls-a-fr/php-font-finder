<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Exceptions;

/**
 * Thrown when a file cannot be decoded (corrupted, or not zipped file)
 */
class ZlibException extends FontFinderException {}
