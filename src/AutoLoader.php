<?php

declare(strict_types=1);

namespace Yurun\Macro;

use Composer\Autoload\ClassLoader;

class AutoLoader
{
    protected ClassLoader $composerClassLoader;

    protected bool $templateMode = false;

    public function __construct(ClassLoader $composerClassLoader, bool $templateMode = false)
    {
        $this->composerClassLoader = $composerClassLoader;
        $this->templateMode = $templateMode;
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

            MacroParser::includeFile($fileName);

            return true;
        }
    }
}
