<?php

declare(strict_types=1);

namespace Macro\Test\ComposerTest;

class Test
{
    public static function a(): bool
    {
        #if version_compare(PHP_VERSION, '8.0', '>=')
        return true;
        #else
        return false;
        #endif
    }
}
