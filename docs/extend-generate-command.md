# Extend Template Generate Command

0. On your console entry point just add the following before running the application:

    * `$application->addGeneratorCommand('template:generate', 'VendorName\ProjectName\Command\TemplateGenerateCommand');`

0. Create your own custom class that inherits from `Staempfli\UniversalGenerator\Command\TemplateGenerateCommand`

0. You can implement the following methods to add your customisations:

    ```
    protected function beforeExecute()
    {}

    protected function beforeAskInputProperties()
    {}
    ```
    
0. If these methods do not fit all your needs, then you can also extend existing ones.