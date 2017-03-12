<?php
/**
 * AbstractCommand
 *
 * @copyright Copyright (c) 2017 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command;

use Staempfli\UniversalGenerator\Helper\IOHelper;
use Staempfli\UniversalGenerator\Tasks\PropertiesTask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    /**
     * @var IOHelper
     */
    protected $io;
    /**
     * @var PropertiesTask
     */
    protected $propertiesTask;

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->io = new IOHelper($input, $output);
        $this->propertiesTask = new PropertiesTask($this->io);
        parent::run($input, $output);
    }
}