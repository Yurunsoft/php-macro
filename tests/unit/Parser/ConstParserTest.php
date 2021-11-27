<?php

declare(strict_types=1);

namespace Yurun\Macro\Test\Unit\Parser;

use PHPUnit\Framework\TestCase;
use Yurun\Macro\Parser\ConstParser;

class ConstParserTest extends TestCase
{
    public function testCase(): void
    {
        $parser = new ConstParser();
        $this->assertEquals(
<<<PHP
<?php
(function(string \$name, \$value){
    \Yurun\Macro\checkDefine(\$name, \$value) or define(\$name, \$value);
})('PI1', 3.14);
?>
<?php
(function(string \$name, \$value){
    \Yurun\Macro\checkDefine(\$name, \$value) or define(\$name, \$value);
})('PI2', 3.14);
?>
PHP,
$parser->parse(<<<PHP
#define PI1 3.14
#const PI2 3.14
PHP)
        );
    }
}
