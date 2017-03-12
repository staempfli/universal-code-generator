<?php
/**
 * AbstractFilesHelper
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Helper\Files;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

abstract class AbstractFilesHelper
{
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