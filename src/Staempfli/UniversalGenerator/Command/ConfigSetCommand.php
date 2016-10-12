<?php
/**
 * ConfigSetCommand
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

class ConfigSetCommand extends Command
{
    /**
     * Default command name is none is set
     *
     * @var string
     */
    protected $defaultName = 'config:set';

    /**
     * Command configuration
     */
    public function configure()
    {
        if (!$this->getName()) {
            $this->setName($this->defaultName);
        }

        $this->setDescription('Set Global Configuration.')
            ->setHelp('This commands sets the global configuration for code generation.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln('<comment>Set Configuration</comment>');

        $propertiesTask = new PropertiesTask($io);
        $propertiesTask->setDefaultPropertiesConfigurationFile();

        $io->success(sprintf('Configuration set into %s', $propertiesTask->getDefaultPropertiesFile()));
    }
}