<?php

declare(strict_types=1);

namespace Yurun\Macro;

/**
 * @param mixed $value
 */
function checkDefine(string $name, $value): bool
{
    if (\defined($name))
    {
        if (\constant($name) !== $value)
        {
            throw new \RuntimeException(sprintf('Constant "%s" redefined', $name));
        }

        return true;
    }

    return false;
}

function str_starts_with(?string $haystack, ?string $needle): bool
{
    return 0 === strncmp($haystack, $needle, \strlen($needle));
}
