<?php

declare(strict_types=1);

namespace Yurun\Macro;

use Composer\Autoload\ClassLoader;
use Yurun\Macro\Parser\Contract\IMacroParser;

final class MacroParser
{
    /**
     * @var string[]
     */
    private static $parsers = [
        \Yurun\Macro\Parser\PhpTagParser::class,
        \Yurun\Macro\Parser\ConstParser::class,
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

    /**
     * @param string|resource $destFile
     */
    public static function convertFile(string $srcFile, $destFile = ''): string
    {
        $srcContent = file_get_contents($srcFile);
        $destContent = self::convert($srcContent);
        if (\is_resource($destFile))
        {
            fwrite($destFile, $destContent);
        }
        elseif ('' !== $destFile)
        {
            $dir = \dirname($destFile);
            if (!is_dir($dir))
            {
                mkdir($dir, 0777, true);
            }
            file_put_contents($destFile, $destContent, \LOCK_EX);
        }

        return $destContent;
    }

    /**
     * @return mixed
     */
    public static function includeFile(string $file, string $destFile = '', bool $deleteFile = true, string $lockFileDir = '')
    {
        if ('' === $destFile)
        {
            return self::execPhpCode(self::convertFile($file), null, $deleteFile);
        }
        else
        {
            if ('' === $lockFileDir)
            {
                $fp = fopen($destFile, 'w+');
                try
                {
                    if (!$fp || !flock($fp, \LOCK_EX))
                    {
                        return;
                    }
                    self::convertFile($file, $fp);

                    return includeFile($destFile);
                }
                finally
                {
                    if ($fp)
                    {
                        flock($fp, \LOCK_UN);
                        fclose($fp);
                        if ($deleteFile)
                        {
                            unlink($destFile);
                        }
                    }
                }
            }
            else
            {
                $lockFileName = $lockFileDir . '/' . md5($destFile);
                $fp = fopen($lockFileName, 'w+');
                try
                {
                    if (!$fp || !flock($fp, \LOCK_EX))
                    {
                        return;
                    }
                    self::convertFile($file, $destFile);

                    return includeFile($destFile);
                }
                finally
                {
                    if ($fp)
                    {
                        flock($fp, \LOCK_UN);
                        fclose($fp);
                        if (is_file($lockFileName))
                        {
                            unlink($lockFileName);
                        }
                        if ($deleteFile)
                        {
                            unlink($destFile);
                        }
                    }
                }
            }
        }
    }

    public static function setTmpPath(string $tmpPath): void
    {
        self::$tmpPath = $tmpPath;
    }

    public static function getTmpPath(): string
    {
        return self::$tmpPath;
    }

    /**
     * @return mixed
     */
    public static function execPhpCode(string $code, ?string $fileName = null, bool $deleteFile = true)
    {
        $tmpPath = &self::$tmpPath;
        if ('' === $tmpPath)
        {
            if (\PHP_OS_FAMILY === 'Windows')
            {
                $tmpPath = sys_get_temp_dir();
            }
            else
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
        }
        if (null === $fileName)
        {
            $fileName = tempnam($tmpPath, 'imi.macro.');
        }

        file_put_contents($fileName, $code);
        if ($deleteFile)
        {
            try
            {
                return includeFile($fileName);
            }
            finally
            {
                unlink($fileName);
            }
        }
        else
        {
            return includeFile($fileName);
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

    public static function hookComposer(bool $templateMode = false, ?string $cacheDir = null): void
    {
        $autoLoaders = spl_autoload_functions();

        // Proxy the composer class loader
        foreach ($autoLoaders as &$autoLoader)
        {
            $unregisterAutoloader = $autoLoader;
            if (\is_array($autoLoader) && isset($autoLoader[0]) && $autoLoader[0] instanceof ClassLoader)
            {
                $autoLoader[0] = new AutoLoader($autoLoader[0], $templateMode, $cacheDir);
            }
            spl_autoload_unregister($unregisterAutoloader);
        }

        unset($autoLoader);

        // Re-register the loaders
        foreach ($autoLoaders as $autoLoader)
        {
            spl_autoload_register($autoLoader);
        }
    }
}
