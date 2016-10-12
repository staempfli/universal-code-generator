<?php
/**
 * FileHelper
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileHelper
{
    /**
     * @var string
     */
    protected $defaultCommandName = "codegen-universal";

    /**
     * Get Project Base Dir
     *
     * @return mixed
     */
    public function getProjectBaseDir()
    {
        return BP;
    }

    /**
     * Get module directory where code generator will be executed
     *
     * @return string
     */
    public function getModuleDir()
    {
        return getcwd();
    }

    /**
     * Get Phar path
     * - Returns the filename if valid, empty string otherwise.
     *
     * @return string
     */
    public function getPharPath()
    {
        return \Phar::running(false);
    }

    /**
     * Get Command name according to file basename
     *
     * @return string
     */
    public function getCommandName()
    {
        if ($this->getPharPath()) {
            return basename($this->getPharPath());
        }
        if (defined('COMMAND_NAME')) {
            return COMMAND_NAME;
        }
        return $this->defaultCommandName;
    }

    /**
     * Get users home
     *
     * @return mixed
     */
    public function getUsersHome()
    {
        return $_SERVER['HOME'];
    }

    /**
     * Get Directories Iterator from specific directory
     *
     * @param $dir
     * @return RecursiveDirectoryIterator
     */
    public function getDirectoriesIterator($dir)
    {
        $directoryIterator = new RecursiveDirectoryIterator($dir);
        $directoryIterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
        return $directoryIterator;
    }

    /**
     * Get Files Iterator from specific directory
     *
     * @param $dir
     * @return RecursiveIteratorIterator
     */
    public function getFilesIterator($dir)
    {
        $directoryIterator = $this->getDirectoriesIterator($dir);
        $fileIterator = new RecursiveIteratorIterator($directoryIterator);

        return $fileIterator;
    }
}