<?php

declare(strict_types=1);

namespace Yurun\Macro\Parser\Contract;

interface IMacroParser
{
    public function parse(string $content): string;
}
