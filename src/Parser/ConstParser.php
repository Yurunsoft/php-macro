<?php

declare(strict_types=1);

namespace Yurun\Macro\Parser;

use Yurun\Macro\Parser\Contract\IMacroParser;

class ConstParser implements IMacroParser
{
    public function parse(string $content): string
    {
        $resultContent = preg_replace_callback('/^\s*#\s*(define|const)\s+(.+)\s+?(.+)$/mUS', function (array $matches) {
            $name = var_export($matches[2], true);

            return <<<PHP
<?php
(function(string \$name, \$value){
    \Yurun\Macro\checkDefine(\$name, \$value) or define(\$name, \$value);
})({$name}, {$matches[3]});
?>
PHP;
        }, $content);
        $resultContent = preg_replace_callback('/^#if(n?)def\s+(.+)$/mUS', function (array $matches) {
            $name = var_export($matches[2], true);

            return '<?php if (' . ($matches[1] ? '!' : '') . 'defined(' . $name . ')): ?>';
        }, $resultContent);

        return $resultContent;
    }
}
