<?php
/**
 * ConfigDisplayCommand
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command;

use Staempfli\UniversalGenerator\Tasks\PropertiesTask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConfigDisplayCommand extends Command
{
    /**
     * Default command name is none is set
     *
     * @var string
     */
    protected $defaultName = 'config:display';

    /**
     * Command configuration
     */
    public function configure()
    {
        if (!$this->getName()) {
            $this->setName($this->defaultName);
        }

        $this->setDescription('Show Global Configuration.')
            ->setHelp('This commands displays the global configuration for code generation.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln('<comment>Display Configuration</comment>');

        $propertiesTask = new PropertiesTask($io);
        $propertiesTask->loadDefaultProperties();
        $propertiesTask->displayLoadedProperties();
        $fileHelper = new FileHelper();
        $io->writeln([
            '<comment>You can change this properties with:</comment>',
            sprintf('<info>  %s config:set</info>', $fileHelper->getCommandName())
        ]);
    }
}