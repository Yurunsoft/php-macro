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
success1(); #if true
#elif 1
success2();
#else
fail(); #if else
#endif
    #endif
PHP;

    public function testParse(): string
    {
        $this->assertEquals(
<<<PHP
<?php echo '<?php'; ?>

<?php if (true): ?>
success1(); #if true
<?php elseif (1): ?>
success2();
<?php else: ?>
fail(); #if else
<?php endif; ?>
    #endif
PHP,
$result = MacroParser::parse(self::CODE)
        );

        return $result;
    }

    /**
     * @depends testParse
     */
    public function testExecParsedCode(string $code): string
    {
        $this->assertEquals(
<<<PHP
<?php
success1(); #if true
    #endif
PHP,
$result = MacroParser::execParsedCode($code)
        );

        return $result;
    }

    /**
     * @depends testExecParsedCode
     */
    public function testConvert(string $code): string
    {
        $this->assertEquals($code, MacroParser::convert(self::CODE));

        return $code;
    }

    /**
     * @depends testConvert
     */
    public function testConvertFile(string $code): void
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
        $srcFile = $tmpPath . '/' . uniqid('', true);
        $destFile = $tmpPath . '/' . uniqid('', true);
        $this->assertNotFalse(file_put_contents($srcFile, self::CODE));
        $this->assertEquals($code, MacroParser::convertFile($srcFile, $destFile));
        $this->assertEquals($code, file_get_contents($destFile));
    }
}
