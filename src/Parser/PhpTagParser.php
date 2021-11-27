<?php

declare(strict_types=1);

namespace Yurun\Macro\Parser;

use Yurun\Macro\Parser\Contract\IMacroParser;

class PhpTagParser implements IMacroParser
{
    public const PHP_TAG_BEGIN = '###php-tag-begin###';

    public const PHP_TAG_END = '###php-tag-end###';

    public function parse(string $content): string
    {
        $resultContent = $content;
        $resultContent = preg_replace('/<\?php(\s*?)([\r\n]*)/S', sprintf('%s echo \'%s\'; %s$1$2$2', self::PHP_TAG_BEGIN, self::PHP_TAG_BEGIN, self::PHP_TAG_END), $resultContent);
        $resultContent = preg_replace('/\?>/S', sprintf('%s echo \'%s\'; %s', self::PHP_TAG_BEGIN, self::PHP_TAG_END, self::PHP_TAG_END), $resultContent);
        $resultContent = str_replace([
            self::PHP_TAG_BEGIN,
            self::PHP_TAG_END,
        ], [
            '<?php',
            '?>',
        ], $resultContent);

        return $resultContent;
    }
}
