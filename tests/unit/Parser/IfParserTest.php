<?php

declare(strict_types=1);

namespace Yurun\Macro\Test\Unit\Parser;

use PHPUnit\Framework\TestCase;
use Yurun\Macro\Parser\IfParser;

class IfParserTest extends TestCase
{
    public function testCase(): void
    {
        $parser = new IfParser();
        $this->assertEquals(
<<<PHP
<?php if (true): ?>
success1(); #if true
<?php elseif (1): ?>
success2();
<?php else: ?>
fail(); #if else
<?php endif; ?>
    #endif
PHP,
$parser->parse(<<<PHP
#if true
success1(); #if true
#elif 1
success2();
#else
fail(); #if else
#endif
    #endif
PHP)
        );
    }
}
