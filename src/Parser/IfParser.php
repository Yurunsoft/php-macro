<?php

declare(strict_types=1);

namespace Yurun\Macro\Parser;

use Yurun\Macro\Parser\Contract\IMacroParser;

class IfParser implements IMacroParser
{
    public function parse(string $content): string
    {
        $resultContent = $content;
        $resultContent = preg_replace('/^#if\s+(.+)$/m', '<?php if ($1): ?>', $resultContent);
        $resultContent = preg_replace('/^#else$/m', '<?php else: ?>', $resultContent);
        $resultContent = preg_replace('/^#elif\s+(.+)/m', '<?php elseif ($1): ?>', $resultContent);
        $resultContent = preg_replace('/^#endif$/m', '<?php endif; ?>', $resultContent);

        return $resultContent;
    }
}
