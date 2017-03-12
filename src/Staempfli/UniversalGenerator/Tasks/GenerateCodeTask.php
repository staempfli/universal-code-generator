<?php
/**
 * GenerateCodeTask
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Tasks;

use Staempfli\UniversalGenerator\Helper\IOHelper;
use Staempfli\UniversalGenerator\Helper\PropertiesHelper;
use Staempfli\UniversalGenerator\Handler\TemplateFilesHandler;

class GenerateCodeTask
{
    /**
     * @var string
     */
    protected $templateName;
    /**
     * @var array
     */
    protected $properties;
    /**
     * @var IOHelper
     */
    protected $io;
    /**
     * @var TemplateFilesHandler
     */
    protected $templateFilesHandler;
    /**
     * @var PropertiesHelper
     */
    protected $propertiesHelper;

    /**
     * GenerateCodeTask constructor.
     * @param string $templateName
     * @param array $properties
     * @param IOHelper $io
     */
    public function __construct($templateName, array $properties, IOHelper $io)
    {
        $this->templateName = $templateName;
        $this->properties = $properties;
        $this->io = $io;
        $this->templateFilesHandler = new TemplateFilesHandler();
        $this->propertiesHelper = new PropertiesHelper();
    }

    /**
     * @param bool $dryRun
     */
    public function generateCode($dryRun = false)
    {
        $templateFiles = $this->templateFilesHandler->getTemplateFiles($this->templateName);
        foreach ($templateFiles as $file) {
            $parsedFilePath = $this->propertiesHelper->replacePropertiesInText($file['path'], $this->properties);
            $parsedFileContent = $this->propertiesHelper->replacePropertiesInText($file['content'], $this->properties);
            if (!$dryRun) {
                $this->generateFileWithContent($parsedFilePath, $parsedFileContent);
            }
            $this->io->writeln(sprintf('<options=bold>File Created:</> %s', $parsedFilePath));
        }
    }

    /**
     * @param string $filePath
     * @param string $fileContent
     */
    protected function generateFileWithContent($filePath, $fileContent)
    {
        if (file_exists($filePath)) {
            if (!$this->shouldOverwriteFile($filePath)) {
                $this->showInfoFileNotCopied($filePath, $fileContent);
                return;
            }
        }

        $this->prepareDirToWriteTo($filePath);
        if (!file_put_contents($filePath, $fileContent)) {
            $this->io->error(sprintf('There was an error copying the file "%s"', $filePath));
            $this->showInfoFileNotCopied($filePath, $fileContent);
        }
    }

    /**
     * @param string $filePath
     * @return bool|string
     */
    protected function shouldOverwriteFile($filePath)
    {
        return $this->io->confirm(sprintf('%s already exists, would you like to overwrite it?', $filePath), false);
    }


    /**
     * @param string $filePath
     * @param string $templateContent
     */
    protected function showInfoFileNotCopied($filePath, $templateContent)
    {
        $this->io->warning(sprintf('%s NOT generated', $filePath));
        $this->io->text($templateContent);
        $this->io->note(sprintf('You can copy the previous code and add it manually on %s', $filePath));
    }

    /**
     * @param string $filePath
     */
    protected function prepareDirToWriteTo($filePath)
    {
        $dirToWrite = dirname($filePath);
        if (!is_dir($dirToWrite)) {
            mkdir($dirToWrite, 0764, true);
        }
    }

}