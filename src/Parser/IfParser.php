<?php

declare(strict_types=1);

namespace Yurun\Macro\Parser;

use Yurun\Macro\Parser\Contract\IMacroParser;

class IfParser implements IMacroParser
{
    public function parse(string $content): string
    {
        $resultContent = $content;
        $resultContent = preg_replace('/^(\s*)#\s*if\s+(.+)$/mUS', '$1<?php if ($2): ?>', $resultContent);
        $resultContent = preg_replace('/^(\s*)#\s*else$/mUS', '$1<?php else: ?>', $resultContent);
        $resultContent = preg_replace('/^(\s*)#\s*elif\s+(.+)/mUS', '$1<?php elseif ($2): ?>', $resultContent);
        $resultContent = preg_replace('/^(\s*)#\s*endif$/mUS', '$1<?php endif; ?>', $resultContent);

        return $resultContent;
    }
}
