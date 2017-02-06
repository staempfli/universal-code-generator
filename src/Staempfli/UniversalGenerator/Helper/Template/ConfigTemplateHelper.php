<?php
/**
 * ConfigTemplateHelper
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Helper\Template;

use Staempfli\UniversalGenerator\Helper\PropertiesHelper;
use Symfony\Component\Yaml\Yaml;

class ConfigTemplateHelper extends AbstractTemplateHelper
{
    /**
     * Config files constants
     */
    const TEMPLATE_CONFIG_FOLDER = '.no-copied-config';

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
    protected $afterGenerateFilename = self::TEMPLATE_CONFIG_FOLDER . '/after-generate-info.txt';

    /**
     * @var PropertiesHelper
     */
    protected $propertiesHelper;

    public function __construct()
    {
        $this->propertiesHelper = new PropertiesHelper();
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
     * @param array $properties
     * @return bool|string
     */
    public function getTemplateAfterGenerateInfo($templateName, array $properties)
    {
        $templateDir = $this->getTemplateDir($templateName);
        $afterGenerateFile = $templateDir . '/' . $this->afterGenerateFilename;

        if (file_exists($afterGenerateFile)) {
            return $this->propertiesHelper->replacePropertiesInText(file_get_contents($afterGenerateFile), $properties);
        }

        return false;
    }
}