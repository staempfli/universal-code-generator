<?php
/**
 * FileTemplateHelper
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Helper\Template;

use Staempfli\UniversalGenerator\Helper\FileHelper;

class FileTemplateHelper extends AbstractTemplateHelper
{
    /**
     * @var FileHelper
     */
    protected $fileHelper;
    /**
     * @var configTemplateHelper
     */
    protected $configTemplateHelper;
    /**
     * @var string
     */
    protected $templateDir;

    public function __construct()
    {
        $this->fileHelper = new FileHelper();
        $this->configTemplateHelper = new ConfigTemplateHelper();
    }

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
    public function getTemplatesList()
    {
        $templates = [];

        $directoryIterator = $this->fileHelper->getDirectoriesIterator($this->sharedTemplatesDir);
        foreach ($directoryIterator as $dir) {
            if ($dir->isDir()) {
                $templates[$dir->getFilename()] = 'shared';
            }
        }
        if (is_dir($this->privateTemplatesDir)) {
            $directoryIterator = $this->fileHelper->getDirectoriesIterator($this->privateTemplatesDir);
            foreach ($directoryIterator as $dir) {
                if ($dir->isDir()) {
                    $templates[$dir->getFilename()] = 'private';
                }
            }
        }

        return $templates;
    }

    /**
     * @param string $templateName
     * @return array
     * @throws \Exception
     */
    public function getTemplateFiles($templateName)
    {
        $files = [];
        foreach ($this->getDirsToLoad($templateName) as $templateDir) {
            $this->templateDir = $templateDir;
            if (!$templateDir) {
                throw new \Exception(sprintf('Template "%s" does not exists. Please check that template and its dependencies exist.'));
            }
            $filesIterator = $this->fileHelper->getFilesIterator($templateDir);
            $this->prepareFilesFromIterator($filesIterator, $files);
        }
        return $files;
    }

    /**
     * @param string $templateName
     * @return array
     */
    protected function getDirsToLoad($templateName)
    {
        $templateDependencies = $this->configTemplateHelper->getTemplateDependencies($templateName);
        $templatesToLoad = array_merge($templateDependencies, [$templateName]);
        $templateDirs = [];
        foreach ($templatesToLoad as $template) {
            $templateDirs[$template] = $this->getTemplateDir($template);
        }
        return $templateDirs;
    }

    /**
     * @param \RecursiveIteratorIterator $filesIterator
     * @param array $files
     * @return array
     */
    protected function prepareFilesFromIterator(\RecursiveIteratorIterator $filesIterator, array &$files)
    {
        foreach ($filesIterator as $file) {
            if ($this->isConfiguration($file)) {
                continue;
            }
            $this->updateFilesWithFile($files, $file);
        }
    }

    /**
     * @param \SplFileInfo $file
     * @return bool
     */
    protected function isConfiguration(\SplFileInfo $file)
    {
        if (strpos($file->getPathname(), ConfigTemplateHelper::TEMPLATE_CONFIG_FOLDER) !== false) {
            return true;
        }
        return false;
    }

    /**
     * @param array $files
     * @param \SplFileInfo $file
     */
    protected function updateFilesWithFile(array &$files, \SplFileInfo $file)
    {
        $fileContent = file_get_contents($file->getPathname());
        $filePath = $this->getAbsolutePathToCopyTo($file->getPathname());

        $currentPaths = array_column($files, 'path');
        $pathKey = array_search($filePath, $currentPaths);
        if ($pathKey !== false) {
            $files[$pathKey]['content'] = $fileContent;
        } else {
            $files[] = [
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
        return $this->fileHelper->getRootDir() . '/' . $this->getRelativePath($filePath);
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