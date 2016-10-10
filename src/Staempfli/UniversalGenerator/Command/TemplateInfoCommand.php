<?php
/**
 * TemplateInfoCommand
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command;

use Staempfli\UniversalGenerator\Helper\ConfigHelper;
use Staempfli\UniversalGenerator\Helper\TemplateHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TemplateInfoCommand extends Command
{
    /**
     * Template arg name
     *
     * @var string
     */
    protected $templateArg = 'template';

    /**
     * Default command name is none is set
     *
     * @var string
     */
    protected $defaultName = 'template:info';

    /**
     * Command configuration
     */
    public function configure()
    {
        if (!$this->getName()) {
            $this->setName($this->defaultName);
        }

        $this->setDescription('Show extended info of specific template.')
            ->setHelp("This command displays a description of what the template does.")
            ->addArgument($this->templateArg, InputArgument::REQUIRED, 'The template to show description for.');
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument($this->templateArg)) {
            $io = new SymfonyStyle($input, $output);
            $template = $io->ask('Please specify a Template:', false);
            if ($template) {
                $input->setArgument($this->templateArg, $template);
            } else {
                $io->note(sprintf('You can check the list of available templates with "%s template:list"', COMMAND_NAME));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln('<comment>Template Info</comment>');

        $templateName = $input->getArgument($this->templateArg);
        $templateHelper = new TemplateHelper();
        if (!$templateHelper->templateExists($templateName)) {
            $io->error(sprintf('Template "%s" does not exists', $templateName));
            $io->note(sprintf('You can check the list of available templates with "%s template:list"', COMMAND_NAME));
            return;
        }

        $io->title($templateName);

        $configHelper = new ConfigHelper();
        $info = $configHelper->getTemplateDescription($templateName);
        if ($info) {
            $io->text($info);
        } else {
            $io->text('Sorry, there is not info defined for this Template');
        }

        $io->newLine();
        $io->writeln([
            '<comment>Generate this template using:</comment>',
            sprintf('<info>  %s template:generate %s</info>', COMMAND_NAME, $templateName)
        ]);
    }


}