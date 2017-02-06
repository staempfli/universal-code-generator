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
use Staempfli\UniversalGenerator\Helper\Template\ConfigTemplateHelper;

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

    /**
     * @var ConfigTemplateHelper
     */
    protected $configTemplateHelper;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->configTemplateHelper = new ConfigTemplateHelper();
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->io = new IOHelper($input, $output);
        $this->propertiesTask = new PropertiesTask($this->io);
        parent::run($input, $output);
    }
}