<?php

declare(strict_types=1);

namespace Yurun\Macro\Parser;

use Yurun\Macro\Parser\Contract\IMacroParser;

class IfParser implements IMacroParser
{
    public function parse(string $content): string
    {
        $resultContent = $content;
        $resultContent = preg_replace('/^#if\s+(.+)$/mUS', '<?php if ($1): ?>', $resultContent);
        $resultContent = preg_replace('/^#else$/mUS', '<?php else: ?>', $resultContent);
        $resultContent = preg_replace('/^#elif\s+(.+)/mUS', '<?php elseif ($1): ?>', $resultContent);
        $resultContent = preg_replace('/^#endif$/mUS', '<?php endif; ?>', $resultContent);

        return $resultContent;
    }
}
