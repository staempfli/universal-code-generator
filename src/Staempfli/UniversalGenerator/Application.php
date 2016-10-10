<?php
/**
 * Application
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator;

use Staempfli\UniversalGenerator\Helper\FileHelper;
use Symfony\Component\Console\Application as SymfonyConsoleApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends SymfonyConsoleApplication
{
    /**
     * @var array
     */
    protected $generatorCommands = [
        'config:set' => 'Staempfli\UniversalGenerator\Command\ConfigSetCommand',
        'config:display' => 'Staempfli\UniversalGenerator\Command\ConfigDisplayCommand',
        'config:unset' => 'Staempfli\UniversalGenerator\Command\ConfigUnsetCommand',
        'template:list' => 'Staempfli\UniversalGenerator\Command\TemplateListCommand',
        'template:info' => 'Staempfli\UniversalGenerator\Command\TemplateInfoCommand',
        'template:generate' => 'Staempfli\UniversalGenerator\Command\TemplateGenerateCommand',
        'self-update' => 'Staempfli\UniversalGenerator\Command\SelfUpdateCommand',
    ];

    public function __construct($name = '', $version = '')
    {
        if (!$name) {
            $fileHelper = new FileHelper();
            $name = $fileHelper->getCommandName();
        }
        if (!$version) {
            $version = "@git_version@";
        }

        parent::__construct($name, $version);
    }

    /**
     * Add generator command
     * - This method also overrides a command, if an existing one has the same name.
     *
     * @param $name
     * @param $class
     */
    public function addGeneratorCommand($name, $class)
    {
        $this->generatorCommands[$name] = $class;
    }

    /**
     * Load generator commands
     */
    protected function loadGeneratorCommands()
    {
        foreach ($this->generatorCommands as $name => $class) {
            $parsedClass = '\\' . trim($class, '\\');
            $this->add(new $parsedClass($name));
        }
    }

    /**
     * Edit default run to load generator commands at the beginning.
     *
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->loadGeneratorCommands();
        return parent::run($input, $output);
    }
}