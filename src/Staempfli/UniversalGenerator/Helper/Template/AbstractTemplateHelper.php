<?php
/**
 * AbstractTemplateHelper
 *
 * @copyright Copyright (c) 2017 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Helper\Template;

abstract class AbstractTemplateHelper
{
    /**
     * @var string
     */
    protected $sharedTemplatesDir = BP . '/templates';
    /**
     * @var string
     */
    protected $privateTemplatesDir = BP . '/privateTemplates';

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
}