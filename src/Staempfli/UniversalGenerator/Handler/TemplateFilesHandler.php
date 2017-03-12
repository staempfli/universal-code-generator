<?php
/**
 * TemplateFilesHandler
 *
 * Copyright © 2017 Staempfli AG. All rights reserved.
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Handler;

use RecursiveIteratorIterator;
use Staempfli\UniversalGenerator\Helper\Files\ApplicationFilesHelper;
use Staempfli\UniversalGenerator\Helper\Files\TemplateFilesHelper;

class TemplateFilesHandler
{
    /**
     * @var ApplicationFilesHelper
     */
    protected $applicationFilesHelper;
    /**
     * @var TemplateFilesHelper
     */
    protected $templateFilesHelper;
    /**
     * @var string
     */
    protected $templateDir;
    /**
     * @var RecursiveIteratorIterator
     */
    protected $filesIterator;
    /**
     * @var array
     */
    protected $files = [];

    public function __construct()
    {
        $this->applicationFilesHelper = new ApplicationFilesHelper();
        $this->templateFilesHelper = new TemplateFilesHelper();
    }

    /**
     * @param string $templateName
     * @return array
     * @throws \Exception
     */
    public function getTemplateFiles($templateName)
    {
        $this->addDependencyFiles($templateName);
        $this->addTemplateFiles($templateName);
        return $this->files;
    }

    /**
     * @param string $templateName
     */
    protected function addDependencyFiles($templateName)
    {
        $dependencyTemplates = $this->templateFilesHelper->getTemplateDependencies($templateName);
        foreach ($dependencyTemplates as $dependencyName) {
            $this->addTemplateFiles($dependencyName);
        }
    }

    /**
     * @param string $templateName
     */
    protected function addTemplateFiles($templateName)
    {
        $this->initDirAndFiles($templateName);
        $this->addFiles();
    }

    /**
     * @param string $templateName
     * @throws \Exception
     */
    protected function initDirAndFiles($templateName)
    {
        $this->templateDir = $this->templateFilesHelper->getTemplateDir($templateName);
        if (!$this->templateDir) {
            throw new \Exception(sprintf('Template "%s" does not exists. Please check that template and its dependencies exist.',  $this->templateDir));
        }
        $this->filesIterator = $this->applicationFilesHelper->getFilesIterator($this->templateDir);
    }

    protected function addFiles()
    {
        foreach ($this->filesIterator as $file) {
            if ($this->templateFilesHelper->isConfigurationFile($file)) {
                continue;
            }
            $this->setFile($file);
        }
    }

    /**
     * @param \SplFileInfo $file
     */
    protected function setFile(\SplFileInfo $file)
    {
        $fileContent = file_get_contents($file->getPathname());
        $filePath = $this->getAbsolutePathToCopyTo($file->getPathname());

        $currentPaths = array_column($this->files, 'path');
        $pathKey = array_search($filePath, $currentPaths);
        if ($pathKey !== false) {
            $this->files[$pathKey]['content'] = $fileContent;
        } else {
            $this->files[] = [
                'path' => $filePath,
                'content' => $fileContent
            ];
        }
    }

    /**
     * @param string $filePath
     * @return string
     */
    protected function getAbsolutePathToCopyTo($filePath)
    {
        return $this->applicationFilesHelper->getRootDir() . '/' . $this->getRelativePath($filePath);
    }

    /**
     * @param string $filePath
     * @return string
     */
    protected function getRelativePath($filePath)
    {
        $relativePath = substr($filePath, strlen($this->templateDir));
        $relativePathFixed = ltrim($relativePath, '/');
        return $relativePathFixed;
    }

}