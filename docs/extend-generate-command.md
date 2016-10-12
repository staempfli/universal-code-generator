# Extend Template Generate Command

0. On your console entry point just add the following before running the application:

    * `$application->addGeneratorCommand('template:generate', 'VendorName\ProjectName\Command\TemplateGenerateCommand');`

0. Create your own custom class that inherits from `Staempfli\UniversalGenerator\Command\TemplateGenerateCommand`

0. You can implement the following methods to add your customisations:

    ```
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
    ```
    
0. If these methods do not fit all your needs, then you can also extend existing ones.