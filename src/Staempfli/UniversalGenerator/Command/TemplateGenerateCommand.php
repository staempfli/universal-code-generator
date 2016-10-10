<?php
/**
 * TemplateGenerateCommand
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command;

use Staempfli\UniversalGenerator\Helper\ConfigHelper;
use Staempfli\UniversalGenerator\Helper\FileHelper;
use Staempfli\UniversalGenerator\Tasks\GenerateCodeTask;
use Staempfli\UniversalGenerator\Tasks\PropertiesTask;
use Staempfli\UniversalGenerator\Helper\TemplateHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TemplateGenerateCommand extends Command
{
    /**
     * Template arg name
     *
     * @var string
     */
    protected $templateArg = 'template';

    /**
     * Option dry run
     *
     * @var string
     */
    protected $optionDryRun = 'dry-run';

    /**
     * option root dir
     *
     * @var string
     */
    protected $rootDir = 'root-dir';

    /**
     * Default command name is none is set
     *
     * @var string
     */
    protected $defaultName = 'template:generate';

    /**
     * Command configuration
     */
    public function configure()
    {
        if (!$this->getName()) {
            $this->setName($this->defaultName);
        }

        $this->setDescription('Generate code for desired template.')
            ->setHelp("This command generates code from a specific template")
            ->addArgument($this->templateArg, InputArgument::REQUIRED, 'The template used to generate the code.')
            ->addOption(
                $this->rootDir,
                'md',
                InputOption::VALUE_REQUIRED,
                'If specified, then generate code on this root directory'
            )
            ->addOption(
                $this->optionDryRun,
                'd',
                InputOption::VALUE_NONE,
                'If specified, then no files will be actually generated.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument($this->templateArg)) {
            $io = new SymfonyStyle($input, $output);
            $template = $io->ask('Please specify a Template:', false);
            if ($template) {
                $input->setArgument($this->templateArg, $template);
            } else {
                $fileHelper = new FileHelper();
                $io->note(sprintf('You can check the list of available templates with "%s template:list"', $fileHelper->getCommandName()));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // Change directory if root dir was specifically set
        $rootDir = $input->getOption($this->rootDir);
        if (is_string($rootDir)) {
            chdir($rootDir);
        }

        // Set default properties configuration if not yet set
        $io = new SymfonyStyle($input, $output);
        $propertiesTask = new PropertiesTask($io);
        if (!$propertiesTask->defaultPropertiesExist()) {
            $propertiesTask->setDefaultPropertiesConfigurationFile();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->beforeExecute($input, $output)) {
            return;
        }
        $io = new SymfonyStyle($input, $output);

        $templateName = $input->getArgument($this->templateArg);
        $templateHelper = new TemplateHelper();
        if (!$templateHelper->templateExists($templateName)) {
            $io->error(sprintf('Template "%s" does not exists', $templateName));
            $fileHelper = new FileHelper();
            $io->note(sprintf('You can check the list of available templates with "%s template:list"', $fileHelper->getCommandName()));
            return;
        }

        $resultDependencies = $this->runTemplateDependencies($templateName, $input, $output);
        if ($resultDependencies) {
            $io->error('There was a problem loading the template dependencies');
            return;
        }

        $io->writeln(sprintf('<comment>Template Generate: %s</comment>', $templateName));

        // Set properties
        $io->section('Loading Default Properties');
        $propertiesTask = new PropertiesTask($io);
        $propertiesTask->loadDefaultProperties();

        $this->beforeAskInputProperties($templateName, $propertiesTask, $io);
        $propertiesTask->displayLoadedProperties();

        // Ask input properties
        $propertiesTask->askAndSetInputPropertiesForTemplate($templateName);

        // Process properties lower and upper
        $propertiesTask->generateMultiCaseProperties();

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $io->writeln('<info>All Properties to replace in template</info>');
            $propertiesTask->displayLoadedProperties();
        }

        $this->beforeGenerate($templateName, $propertiesTask, $io);

        // Generate code files
        $generateCodeTask = new GenerateCodeTask($templateName, $propertiesTask->getProperties(), $io);
        $generateCodeTask->generateCode($input->getOption($this->optionDryRun));

        $this->afterGenerate($templateName, $propertiesTask, $io);

        $io->success('CODE GENERATED!');
    }

    /**
     * Run template dependencies
     *
     * @param $templateName
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    protected function runTemplateDependencies($templateName, InputInterface $input, OutputInterface $output)
    {
        $configHelper = new ConfigHelper();
        $dependencies = $configHelper->getTemplateDependencies($templateName);
        foreach ($dependencies as $dependencyTemplate) {
            $io = new SymfonyStyle($input, $output);
            if ($io->confirm(sprintf('This template depends on "%s" template. Would you also like to generate this template?', $dependencyTemplate), true)) {
                return $this->runCommandForAnotherTemplate($dependencyTemplate, $input, $output);
            }
        }
    }

    /**
     * Run template generator command for specific template name
     *
     * @param $templateName
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string $resultCode
     */
    protected function runCommandForAnotherTemplate($templateName, InputInterface $input, OutputInterface $output)
    {
        $commandName = 'template:generate';
        $command = $this->getApplication()->find($commandName);
        $arguments = [
            'command' => $commandName,
            $this->templateArg => $templateName
        ];
        foreach ($input->getOptions() as $option => $value) {
            if ($value) {
                $arguments['--' . $option] = $value;
            }
        }

        $newInput = new ArrayInput($arguments);
        return $command->run($newInput, $output);
    }

    /**
     * Some checks or specific actions before command execution
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool|string $resultCode
     * @return void
     */
    protected function beforeExecute(InputInterface $input, OutputInterface $output)
    {}

    /**
     * Set specific properties before step to ask user for manual input
     *
     * @param $templateName
     * @param PropertiesTask $propertiesTask
     * @return void
     */
    protected function beforeAskInputProperties($templateName, PropertiesTask $propertiesTask, SymfonyStyle $io)
    {}

    /**
     * Actions right before code is generated
     *
     * @param $templateName
     * @param PropertiesTask $propertiesTask
     * @param SymfonyStyle $io
     */
    protected function beforeGenerate($templateName, PropertiesTask $propertiesTask, SymfonyStyle $io)
    {
        $fileHelper = new FileHelper();
        $io->text(sprintf('Code will be generated at following path %s', $fileHelper->getModuleDir()));
        if (!$io->confirm('You want to continue?', true)) {
            $io->error('Execution stopped');
            return;
        }
    }

    /**
     * Actions right after code is generated
     *
     * @param $templateName
     * @param PropertiesTask $propertiesTask
     * @param SymfonyStyle $io
     */
    protected function afterGenerate($templateName, PropertiesTask $propertiesTask, SymfonyStyle $io)
    {
        $configHelper = new ConfigHelper();
        $afterGenerateInfo = $configHelper->getTemplateAfterGenerateInfo($templateName, $propertiesTask->getProperties());
        if ($afterGenerateInfo) {
            $io->warning('This template needs you to take care of the following manual steps:');
            $io->text($afterGenerateInfo);
        }

    }

}