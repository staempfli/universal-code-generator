<?php
/**
 * TemplateListCommand
 *
 * @copyright Copyright (c) 2016 Staempfli AG
 * @author    juan.alonso@staempfli.com
 */

namespace Staempfli\UniversalGenerator\Command\Template;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TemplateListCommand extends AbstractTemplateCommand
{
    /**
     * Command configuration
     */
    public function configure()
    {
        $this->setDescription('Show list of possible templates to generate code.')
            ->setHelp("This command checks all available templates to generate code from.");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) //@codingStandardsIgnoreLine
    {
        $this->io->writeln('Templates List');

        $featuredTemplates = $this->templateFilesHelper->getFeaturedTemplates();
        $allTemplates = $this->templateFilesHelper->getAllTemplates();
        $privateTemplates = $this->templateFilesHelper->getPrivateTemplates();

        $this->displayTemplateList('Featured', $featuredTemplates);
        $this->displayTemplateList('More Templates', array_diff($allTemplates, $featuredTemplates));
        $this->displayTemplateList('Private', $privateTemplates);

        $this->io->newLine();
        $this->io->writeln([
            '<comment>Generate one of these templates using:</comment>',
            sprintf('<info>  %s template:generate <template></info>', $this->getApplication()->getName())
        ]);
    }

    /**
     * @param string $listTitle
     * @param array $templates
     */
    private function displayTemplateList($listTitle, $templates)
    {
        if ($templates) {
            $this->io->newLine();
            $this->io->writeln('<comment>' . $listTitle . '</comment>');
            foreach ($templates as $templateName) {
                $this->io->writeln('<info>  ' . $templateName . '</info>');
            }
        }
    }
}