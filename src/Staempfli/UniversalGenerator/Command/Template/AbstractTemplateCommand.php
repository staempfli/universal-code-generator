<?php
/**
 * AbstractTemplateCommand
 *
 * @copyright Copyright (c) 2017 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command\Template;

use Staempfli\UniversalGenerator\Command\AbstractCommand;
use Staempfli\UniversalGenerator\Helper\Files\TemplateFilesHelper;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractTemplateCommand extends AbstractCommand
{
    const ARG_TEMPLATE = 'template';

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var TemplateFilesHelper
     */
    protected $templateFilesHelper;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->templateFilesHelper = new TemplateFilesHelper();
    }

    /**
     * @param InputInterface $input
     */
    protected function setTemplate(InputInterface $input)
    {
        if (!$input->getArgument(self::ARG_TEMPLATE)) {
            $template = $this->io->ask('Please specify a Template:', false);
            $input->setArgument(self::ARG_TEMPLATE, $template);
        }
        $this->templateName = $input->getArgument(self::ARG_TEMPLATE);
    }

    /**
     * @throws \Exception
     */
    protected function validateTemplate()
    {
        if (!$this->templateFilesHelper->templateExists($this->templateName)) {
            $errorMessage = sprintf(
                'Template "%s" does not exists. You can check the list of available templates with "%s template:list"',
                $this->templateName,
                $this->getApplication()->getName()
            );
            throw new \Exception($errorMessage);
        }
    }
}