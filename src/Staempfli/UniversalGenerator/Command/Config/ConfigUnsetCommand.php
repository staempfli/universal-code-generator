<?php
/**
 * ConfigUnsetCommand
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command\Config;

use Staempfli\UniversalGenerator\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigUnsetCommand extends AbstractCommand
{
    public function configure()
    {
        $this->setDescription('Unset Global Configuration.')
            ->setHelp('This command unsets the global configuration for code generation.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->writeln('<comment>Unset Configuration</comment>');

        if (!$this->propertiesTask->defaultPropertiesExist()) {
            throw new \Exception('Configuration file does exist');
        }
        unlink($this->propertiesTask->getDefaultPropertiesFile());
        $this->io->success('Configuration was unset');
    }
}