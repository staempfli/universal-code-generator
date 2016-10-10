<?php
/**
 * TemplateListCommand
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command;

use Staempfli\UniversalGenerator\Helper\FileHelper;
use Staempfli\UniversalGenerator\Helper\TemplateHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TemplateListCommand extends Command
{
    /**
     * Default command name is none is set
     *
     * @var string
     */
    protected $defaultName = 'template:list';

    /**
     * Command configuration
     */
    public function configure()
    {
        if (!$this->getName()) {
            $this->setName($this->defaultName);
        }

        $this->setDescription('Show list of possible templates to generate code.')
            ->setHelp("This command checks all available templates to generate code from.");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) //@codingStandardsIgnoreLine
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln('<comment>Templates List</comment>');

        $templateHelper = new TemplateHelper();
        $templates = $templateHelper->getTemplatesList();

        foreach ($templates as $templateName => $type)
        {
            if ($type == 'private') {
                $templateName = $templateName . ' (Private)';
            }
            $output->writeln('<info>  ' . $templateName . '</info>');
        }

        $io->newLine();
        $fileHelper = new FileHelper();
        $io->writeln([
            '<comment>Generate one of these templates using:</comment>',
            sprintf('<info>  %s template:generate <template></info>', $fileHelper->getCommandName())
        ]);
    }
}