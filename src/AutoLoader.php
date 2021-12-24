<?php

declare(strict_types=1);

namespace Yurun\Macro;

use Composer\Autoload\ClassLoader;

class AutoLoader
{
    /**
     * @var ClassLoader
     */
    protected $composerClassLoader;

    /**
     * @var bool
     */
    protected $templateMode = false;

    protected ?string $cacheDir = null;

    public function __construct(ClassLoader $composerClassLoader, bool $templateMode = false, ?string $cacheDir = null)
    {
        $this->composerClassLoader = $composerClassLoader;
        $this->templateMode = $templateMode;
        if (null !== $cacheDir)
        {
            if (\DIRECTORY_SEPARATOR !== substr($cacheDir, -1, 1))
            {
                $cacheDir .= \DIRECTORY_SEPARATOR;
            }
            $this->cacheDir = $cacheDir;
        }
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $class The name of the class
     *
     * @return true|null True if loaded, null otherwise
     */
    public function loadClass(string $class): ?bool
    {
        if (str_starts_with($class, 'Yurun\Macro\\'))
        {
            return $this->composerClassLoader->loadClass($class);
        }
        else
        {
            $fileName = $this->composerClassLoader->findFile($class);
            if (false === $fileName)
            {
                return null;
            }
            if ($this->templateMode)
            {
                $macroFileName = $fileName . '.macro';
                if (is_file($macroFileName))
                {
                    MacroParser::convertFile($macroFileName, $fileName);
                    includeFile($fileName);

                    return true;
                }
            }

            if (null === $this->cacheDir)
            {
                MacroParser::includeFile($fileName);
            }
            else
            {
                $destFile = $this->cacheDir . md5($fileName) . '.php';
                MacroParser::convertFile($fileName, $destFile);
                includeFile($destFile);
            }

            return true;
        }
    }
}
