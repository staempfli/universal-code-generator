<?php
/**
 * TemplateFilesHelper
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Helper\Files;

use Symfony\Component\Yaml\Yaml;

class TemplateFilesHelper extends AbstractFilesHelper
{
    /**
     * Config files constants
     */
    const TEMPLATE_CONFIG_FOLDER = '.no-copied-config';

    /**
     * @var string
     */
    protected $sharedTemplatesDir = BP . '/templates';
    /**
     * @var string
     */
    protected $privateTemplatesDir = BP . '/privateTemplates';
    /**
     * @var string
     */
    protected $configFilename = self::TEMPLATE_CONFIG_FOLDER . '/config.yml';
    /**
     * @var string
     */
    protected $descriptionFilename = self::TEMPLATE_CONFIG_FOLDER . '/description.txt';
    /**
     * @var string
     */
    protected $afterGenerateFilename = self::TEMPLATE_CONFIG_FOLDER . '/after-generate-info.txt'; /**
    /**
     * @var string
     */
    protected $featuredFilename = self::TEMPLATE_CONFIG_FOLDER . '/.featured';

    /**
     * @param string $templateName
     * @return bool
     */
    public function templateExists($templateName)
    {
        if (!$templateName) {
            return false;
        }
        if (!$this->getTemplateDir($templateName)) {
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function getAllTemplates()
    {
        $templates = [];
        $directoryIterator = $this->getDirectoriesIterator($this->sharedTemplatesDir);
        foreach ($directoryIterator as $dir) {
            if ($dir->isDir()) {
                $templates[] = $dir->getFilename();
            }
        }
        return $templates;
    }

    /**
     * @return array
     */
    public function getFeaturedTemplates()
    {
        $featuredTemplates = [];
        $allTemplates = $this->getAllTemplates();
        foreach ($allTemplates as $template) {
            if ($this->isFeaturedTemplate($template)) {
                $featuredTemplates[] = $template;
            }
        }
        return $featuredTemplates;
    }

    /**
     * @return array
     */
    public function getPrivateTemplates()
    {
        if (!is_dir($this->privateTemplatesDir)) {
            return [];
        }
        $templates = [];
        $directoryIterator = $this->getDirectoriesIterator($this->privateTemplatesDir);
        foreach ($directoryIterator as $dir) {
            if ($dir->isDir()) {
                $templates[] = $dir->getFilename();
            }
        }
        return $templates;
    }

    /**
     * @param string $templateName
     * @return string
     */
    public function getTemplateDir($templateName)
    {
        $dir = $this->privateTemplatesDir . '/' . $templateName;
        if (is_dir($dir)) {
            return $dir;
        }
        $dir = $this->sharedTemplatesDir . '/' . $templateName;
        if (is_dir($dir)) {
            return $dir;
        }
        return false;
    }

    /**
     * @param string $templateName
     * @return array $templatesDependencies
     */
    public function getTemplateDependencies($templateName)
    {
        $dependencies = [];
        $templateDir = $this->getTemplateDir($templateName);
        $dependenciesFile = $templateDir . '/' . $this->configFilename;

        if (file_exists($dependenciesFile)) {
            $parsedConfig = Yaml::parse(file_get_contents($dependenciesFile));
            if (isset($parsedConfig['dependencies'])) {
                $dependencies = $parsedConfig['dependencies'];
            }
        }

        return $dependencies;
    }

    /**
     * @param string $templateName
     * @return bool|string
     */
    public function getTemplateDescription($templateName)
    {
        $templateDir = $this->getTemplateDir($templateName);
        $descriptionFile = $templateDir . '/' . $this->descriptionFilename;

        if (file_exists($descriptionFile)) {
            return file_get_contents($descriptionFile);
        }

        return false;
    }

    /**
     * @param string $templateName
     * @return bool|string
     */
    public function getAfterGenerateFile($templateName)
    {
        $templateDir = $this->getTemplateDir($templateName);
        $afterGenerateFile = $templateDir . '/' . $this->afterGenerateFilename;

        if (file_exists($afterGenerateFile)) {
            return $afterGenerateFile;
        }
        return false;
    }

    /**
     * @param \SplFileInfo $file
     * @return bool
     */
    public function isConfigurationFile(\SplFileInfo $file)
    {
        if (strpos($file->getPathname(), self::TEMPLATE_CONFIG_FOLDER) !== false) {
            return true;
        }
        return false;
    }

    /**
     * @param $templateName
     * @return bool
     */
    public function isFeaturedTemplate($templateName)
    {
        $templateDir = $this->getTemplateDir($templateName);
        if (file_exists($templateDir . '/' . $this->featuredFilename)) {
            return true;
        }
        return false;
    }
}