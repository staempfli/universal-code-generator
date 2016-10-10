<?php
/**
 * ConfigUnsetCommand
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

class ConfigUnsetCommand extends Command
{
    /**
     * Default command name is none is set
     *
     * @var string
     */
    protected $defaultName = 'config:unset';

    /**
     * Command configuration
     */
    public function configure()
    {
        if (!$this->getName()) {
            $this->setName($this->defaultName);
        }

        $this->setDescription('Unset Global Configuration.')
            ->setHelp('This commands unsets the global configuration for code generation.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln('<comment>Unset Configuration</comment>');

        $propertiesTask = new PropertiesTask($io);
        if (!$propertiesTask->defaultPropertiesExist()) {
            $io->error('Configuration file does exist');
        }
        unlink($propertiesTask->getDefaultPropertiesFile());
        $io->success('Configuration was unset');
    }
}