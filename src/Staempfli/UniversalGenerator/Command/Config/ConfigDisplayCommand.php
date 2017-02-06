<?php
/**
 * ConfigDisplayCommand
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command\Config;

use Staempfli\UniversalGenerator\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigDisplayCommand extends AbstractCommand
{
    public function configure()
    {
        $this->setDescription('Show Global Configuration.')
            ->setHelp('This commands displays the global configuration for code generation.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->writeln('<comment>Display Configuration</comment>');

        $this->propertiesTask->loadDefaultProperties();
        $this->propertiesTask->displayLoadedProperties();
        $this->io->writeln([
            '<comment>You can change this properties with:</comment>',
            sprintf('<info>  %s config:set</info>', $this->getApplication()->getName())
        ]);
    }
}