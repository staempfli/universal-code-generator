<?php
/**
 * ConfigSetCommand
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command\Config;

use Staempfli\UniversalGenerator\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigSetCommand extends AbstractCommand
{
    public function configure()
    {
        $this->setDescription('Set Global Configuration.')
            ->setHelp('This commands sets the global configuration for code generation.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->writeln('<comment>Set Configuration</comment>');

        $this->propertiesTask->setDefaultPropertiesConfigurationFile();

        $this->io->success(sprintf('Configuration set into %s', $this->propertiesTask->getDefaultPropertiesFile()));
    }
}