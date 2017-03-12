<?php
/**
 * TemplateInfoCommand
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command\Template;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TemplateInfoCommand extends AbstractTemplateCommand
{
    public function configure()
    {
        $this->setDescription('Show extended info of specific template.')
            ->setHelp("This command displays a description of what the template does.")
            ->addArgument(
                AbstractTemplateCommand::ARG_TEMPLATE,
                InputArgument::REQUIRED,
                'The template to show description for.'
            );
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);
        $this->setTemplate($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validateTemplate();
        $this->io->writeln('<comment>Template Info</comment>');

        $this->io->title($this->templateName);

        $this->displayDescription();
        $this->displayDependencies();

        $this->io->newLine();
        $this->io->writeln([
            '<comment>Generate this template using:</comment>',
            sprintf('<info>  %s template:generate %s</info>', $this->getApplication()->getName(), $this->templateName)
        ]);
    }

    protected function displayDescription()
    {
        $description = $this->templateFilesHelper->getTemplateDescription($this->templateName);
        if ($description) {
            $this->io->text($description);
        } else {
            $this->io->text('Sorry, there is not info defined for this Template');
        }

    }

    protected function displayDependencies()
    {
        $dependencies = $this->templateFilesHelper->getTemplateDependencies($this->templateName);
        if ($dependencies) {
            $this->io->note('DEPENDENCIES - This module will also load the following templates:');
            $this->io->text($dependencies);
        }
    }


}