<?php
/**
 * ApplicationFilesHelper
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Helper\Files;

use RuntimeException;

class ApplicationFilesHelper extends AbstractFilesHelper
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
     * @throws RuntimeException
     */
    public function getUsersHome()
    {
        if (false !== ($home = getenv('HOME'))) {
            return $home;
        }
        if (defined('PHP_WINDOWS_VERSION_BUILD') && false !== ($home = getenv('USERPROFILE'))) {
            return $home;
        }
        if (function_exists('posix_getuid') && function_exists('posix_getpwuid')) {
            $info = posix_getpwuid(posix_getuid());
            return $info['dir'];
        }
        throw new RuntimeException('Could not determine user directory');
    }

}
