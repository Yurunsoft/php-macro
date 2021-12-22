<?php

declare(strict_types=1);

namespace Macro\Test\hook;

use Macro\Test\ComposerTest\TemplateTest;
use Macro\Test\ComposerTest\Test;
use PHPUnit\Framework\TestCase;
use Yurun\Macro\MacroParser;

class ComposerHookTest extends TestCase
{
    public function testHook(): void
    {
        MacroParser::hookComposer(true);
        $this->assertTrue(true);
    }

    public function testDefault(): void
    {
        $this->assertEquals(version_compare(\PHP_VERSION, '8.0', '>='), Test::a());
    }

    public function testTemplate(): void
    {
        $this->assertEquals(version_compare(\PHP_VERSION, '8.0', '>='), TemplateTest::a());
    }
}
