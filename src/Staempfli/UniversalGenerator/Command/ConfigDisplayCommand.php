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
     * Command configuration
     */
    public function configure()
    {
        $this->setName('config:display')
            ->setDescription('Show Global Configuration.')
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
        $io->writeln([
            '<comment>You can change this properties with:</comment>',
            sprintf('<info>  %s config:set</info>', COMMAND_NAME)
        ]);
    }
}