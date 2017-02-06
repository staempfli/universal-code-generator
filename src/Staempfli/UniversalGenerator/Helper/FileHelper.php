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
    const DEFAULT_APPLICATION_NAME = "codegen-universal";

    /**
     * @return string
     */
    public function getProjectBaseDir()
    {
        return BP;
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        return getcwd();
    }

    /**
     * @return string
     */
    public function getPharPath()
    {
        return \Phar::running(false);
    }

    /**
     * @return string
     */
    public function getApplicationFileName()
    {
        if ($this->getPharPath()) {
            return basename($this->getPharPath());
        }
        if (defined('COMMAND_NAME')) {
            return COMMAND_NAME;
        }
        return self::DEFAULT_APPLICATION_NAME;
    }

    /**
     * @return mixed
     */
    public function getUsersHome()
    {
        return $_SERVER['HOME'];
    }

    /**
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