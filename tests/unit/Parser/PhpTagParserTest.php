<?php

declare(strict_types=1);

namespace Yurun\Macro\Test\Unit\Parser;

use PHPUnit\Framework\TestCase;
use Yurun\Macro\Parser\PhpTagParser;

class PhpTagParserTest extends TestCase
{
    public function testCase(): void
    {
        $parser = new PhpTagParser();
        $this->assertEquals(
<<<PHP
<?php echo '<?php'; ?>

echo 'hello world';
PHP,
$parser->parse(<<<PHP
<?php
echo 'hello world';
PHP)
        );
    }
}
