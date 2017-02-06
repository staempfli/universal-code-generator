<?php
/**
 * TemplateGenerateCommand
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command\Template;

use Staempfli\UniversalGenerator\Helper\FileHelper;
use Staempfli\UniversalGenerator\Tasks\GenerateCodeTask;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TemplateGenerateCommand extends AbstractTemplateCommand
{
    const OPTION_DRY_RUN = 'dry-run';
    const OPTION_ROOT_DIR = 'root-dir';

    /**
     * @var FileHelper
     */
    protected $fileHelper;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->fileHelper = new FileHelper();
    }

    public function configure()
    {
        $this->setDescription('Generate code for desired template.')
            ->setHelp("This command generates code from a specific template")
            ->addArgument(
                AbstractTemplateCommand::ARG_TEMPLATE,
                InputArgument::REQUIRED,
                'The template used to generate the code.'
            )->addOption(
                self::OPTION_ROOT_DIR,
                null,
                InputOption::VALUE_REQUIRED,
                'If specified, code is generated on this root directory'
            )->addOption(
                self::OPTION_DRY_RUN,
                null,
                InputOption::VALUE_NONE,
                'If specified, no files will be generated.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $rootDir = $input->getOption(self::OPTION_ROOT_DIR);
        if (is_string($rootDir)) {
            chdir($rootDir);
        }

        if (!$this->propertiesTask->defaultPropertiesExist()) {
            $this->propertiesTask->setDefaultPropertiesConfigurationFile();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);
        $this->setTemplate($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validateTemplate();
        $this->beforeExecute();

        $this->io->writeln(sprintf('<comment>Template Generate: %s</comment>', $this->templateName));
        $this->prepareProperties();

        $this->beforeGenerate();
        $generateCodeTask = new GenerateCodeTask($this->templateName, $this->propertiesTask->getProperties(), $this->io);
        $generateCodeTask->generateCode($input->getOption(self::OPTION_DRY_RUN));
        $this->afterGenerate();

        $this->io->success('CODE GENERATED!');
    }

    protected function beforeExecute()
    {
    }

    protected function prepareProperties()
    {
        $this->propertiesTask->loadDefaultProperties();
        $this->propertiesTask->displayLoadedProperties();

        $this->beforeAskInputProperties();
        $this->propertiesTask->askAndSetInputPropertiesForTemplate($this->templateName);

        if ($this->io->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->io->writeln('<info>All Properties to replace in template</info>');
            $this->propertiesTask->displayLoadedProperties();
        }
    }

    protected function beforeAskInputProperties()
    {
    }

    protected function beforeGenerate()
    {
        $this->io->text(sprintf('Code will be generated at following path <options=bold>%s</>', $this->fileHelper->getRootDir()));
        if (!$this->io->confirm('Do you want to continue?', true)) {
            throw new \Exception('Execution stopped');
        }
    }

    protected function afterGenerate()
    {
        $afterGenerateInfo = $this->configTemplateHelper->getTemplateAfterGenerateInfo($this->templateName, $this->propertiesTask->getProperties());
        if ($afterGenerateInfo) {
            $this->io->note('This template needs you to take care of the following manual steps:');
            $this->io->text($afterGenerateInfo);
        }
    }

}