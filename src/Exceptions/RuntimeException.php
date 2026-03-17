<?php

declare(strict_types=1);

namespace Lsa\Font\Finder\Exceptions;

/**
 * Thrown when something should not happen (invalid package, PHP API breaking changes, ...)
 */
class RuntimeException extends FontFinderException {}
