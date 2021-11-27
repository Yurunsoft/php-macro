<?php

declare(strict_types=1);

namespace Yurun\Macro;

use Yurun\Macro\Parser\Contract\IMacroParser;

final class MacroParser
{
    /**
     * @var string[]
     */
    private static $parsers = [
        \Yurun\Macro\Parser\PhpTagParser::class,
        \Yurun\Macro\Parser\IfParser::class,
    ];

    /**
     * 临时目录地址
     */
    private static $tmpPath = '';

    private function __construct()
    {
    }

    public static function parse(string $content): string
    {
        $resultContent = $content;
        foreach (self::$parsers as $parserClass)
        {
            /** @var IMacroParser $parser */
            $parser = new $parserClass();
            $resultContent = $parser->parse($resultContent);
        }

        return $resultContent;
    }

    public static function execParsedCode(string $code): string
    {
        ob_start();
        self::execPhpCode($code);
        $result = ob_get_clean();
        if (false === $result)
        {
            throw new \RuntimeException('Exec parsed code failed');
        }

        return $result;
    }

    public static function convert(string $content): string
    {
        $parsedCode = self::parse($content);

        return self::execParsedCode($parsedCode);
    }

    public static function convertFile(string $srcFile, string $destFile = ''): string
    {
        $srcContent = file_get_contents($srcFile);
        $destContent = self::convert($srcContent);
        if ('' !== $destFile)
        {
            $dir = \dirname($destFile);
            if (!is_dir($dir))
            {
                mkdir($dir);
            }
            file_put_contents($destFile, $destContent);
        }

        return $destContent;
    }

    /**
     * @return mixed
     */
    public static function execPhpCode(string $code, ?string $fileName = null, bool $deleteFile = true)
    {
        $tmpPath = &self::$tmpPath;
        if ('' === $tmpPath)
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
        }
        if (null === $fileName)
        {
            $fileName = $tmpPath . '/' . getmypid() . '-' . uniqid('', true) . '.php';
        }

        file_put_contents($fileName, $code);
        if ($deleteFile)
        {
            try
            {
                return require $fileName;
            }
            finally
            {
                unlink($fileName);
            }
        }
        else
        {
            return require $fileName;
        }
    }

    /**
     * @return string[]
     */
    public static function getParsers(): array
    {
        return self::$parsers;
    }

    /**
     * @param string[] $parsers
     */
    public static function setParsers(array $parsers): void
    {
        self::$parsers = $parsers;
    }

    /**
     * @param string|string[] $parsers
     */
    public static function addParsers($parsers): void
    {
        if (\is_array($parsers))
        {
            self::$parsers = array_merge(self::$parsers, $parsers);
        }
        else
        {
            self::$parsers = $parsers;
        }
    }
}