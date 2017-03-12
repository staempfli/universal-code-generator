<?php
/**
 * TemplateListCommand
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command\Template;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TemplateListCommand extends AbstractTemplateCommand
{
    /**
     * Command configuration
     */
    public function configure()
    {
        $this->setDescription('Show list of possible templates to generate code.')
            ->setHelp("This command checks all available templates to generate code from.");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) //@codingStandardsIgnoreLine
    {
        $this->io->writeln('<comment>Templates List</comment>');

        foreach ($this->templateFilesHelper->getTemplatesList() as $templateName => $type)
        {
            if ($type == 'private') {
                $templateName = $templateName . ' (Private)';
            }
            $this->io->writeln('<info>  ' . $templateName . '</info>');
        }

        $this->io->newLine();
        $this->io->writeln([
            '<comment>Generate one of these templates using:</comment>',
            sprintf('<info>  %s template:generate <template></info>', $this->getApplication()->getName())
        ]);
    }
}