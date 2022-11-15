<?php

declare(strict_types=1);

namespace Yurun\Macro\Test\Unit\Parser;

use PHPUnit\Framework\TestCase;
use Yurun\Macro\MacroParser;

class MacroParserTest extends TestCase
{
    public const CODE = <<<PHP
<?php
#if true
test();
#endif
#ifndef PI1
    # define PI1 3.14
#endif
#ifdef PI1
#const PI2 3.14
#endif
#if true
success1(); #if true
#elif 1
success2();
#else
fail(); #if else
#endif
PHP;

    public const PARSE_RESULT = <<<PHP
<?php echo '<?php'; ?>

<?php if (true): ?>
test();
<?php endif; ?>
<?php if (!defined('PI1')): ?>
<?php
(function(string \$name, \$value){
    \Yurun\Macro\checkDefine(\$name, \$value) or define(\$name, \$value);
})('PI1', 3.14);
?>
<?php endif; ?>
<?php if (defined('PI1')): ?>
<?php
(function(string \$name, \$value){
    \Yurun\Macro\checkDefine(\$name, \$value) or define(\$name, \$value);
})('PI2', 3.14);
?>
<?php endif; ?>
<?php if (true): ?>
success1(); #if true
<?php elseif (1): ?>
success2();
<?php else: ?>
fail(); #if else
<?php endif; ?>
PHP;

    public const EXEC_RESULT = <<<PHP
<?php
test();
success1(); #if true

PHP;

    public function testParse(): void
    {
        $this->assertEquals(self::PARSE_RESULT, MacroParser::parse(self::CODE));
    }

    public function testExecParsedCode(): void
    {
        $this->assertEquals(self::EXEC_RESULT, MacroParser::execParsedCode(self::PARSE_RESULT)
        );
    }

    public function testConvert(): void
    {
        $this->assertEquals(self::EXEC_RESULT, MacroParser::convert(self::CODE));
    }

    public function testConvertFile(): void
    {
        if (is_dir('/run/shm'))
        {
            $tmpPath = '/run/shm';
        }
        elseif (is_dir('/tmp'))
        {
            $tmpPath = '/tmp';
        }
        else
        {
            $tmpPath = sys_get_temp_dir();
        }
        $srcFile = $tmpPath . \DIRECTORY_SEPARATOR . uniqid('', true);
        $destFile = $tmpPath . \DIRECTORY_SEPARATOR . uniqid('', true);
        $this->assertNotFalse(file_put_contents($srcFile, self::CODE));
        $this->assertEquals(self::EXEC_RESULT, MacroParser::convertFile($srcFile, $destFile));
        $this->assertEquals(self::EXEC_RESULT, file_get_contents($destFile));

        $destFile = $tmpPath . \DIRECTORY_SEPARATOR . uniqid('', true);
        $fp = fopen($destFile, 'c+');
        $this->assertEquals(self::EXEC_RESULT, MacroParser::convertFile($srcFile, $fp));
        fclose($fp);
        $this->assertEquals(self::EXEC_RESULT, file_get_contents($destFile));
    }

    public function includeFile(): void
    {
        if (is_dir('/run/shm'))
        {
            $tmpPath = '/run/shm';
        }
        elseif (is_dir('/tmp'))
        {
            $tmpPath = '/tmp';
        }
        else
        {
            $tmpPath = sys_get_temp_dir();
        }
        $fileName = $tmpPath . \DIRECTORY_SEPARATOR . uniqid('', true);
        $code = <<<PHP
<?php
if (\$a)
{
    return 1;
}
else
{
    return 0;
}
PHP;
        file_put_contents($fileName, $code);
        $a = 1;
        $this->assertEquals(1, MacroParser::includeFile($fileName));
        $this->assertEquals(1, MacroParser::includeFile($fileName, '', true, '/tmp'));
        $a = 0;
        $this->assertEquals(0, MacroParser::includeFile($fileName));
        $this->assertEquals(0, MacroParser::includeFile($fileName, '', true, '/tmp'));
    }
}
