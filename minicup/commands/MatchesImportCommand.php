<?php

namespace Minicup\Commands;


use Minicup\Model\Entity\Category;
use Minicup\Model\Entity\Year;
use Minicup\Model\Manager\MatchImporter;
use Minicup\Model\Manager\ReorderManager;
use Minicup\Model\Repository\CategoryRepository;
use Minicup\Model\Repository\YearRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class MatchesImportCommand extends Command
{
    /** @var MatchImporter @inject */
    public $importer;
    /** @var ReorderManager @inject */
    public $reorder;
    /** @var CategoryRepository @inject */
    public $categoryRepository;
    /** @var YearRepository @inject */
    public $yearRepository;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('app:import-matches')
            ->setDescription('Import matches from given file with matches exported from xlsx.')
            ->addUsage('app:import-matches 2016 mladsi mladsi.txt')
            ->addArgument('year', InputArgument::REQUIRED, 'Year of given matches.')
            ->addArgument('category', InputArgument::REQUIRED, 'Category of given matches')
            ->addArgument('file', InputArgument::REQUIRED,
                'Filed with exported matches - text file with lines in format d.m.Y\tH:i.\tLOCATION\tTEAM1\tTEAM2\n');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $dialog */
        $dialog = $this->getHelper('question');
        $yearArg = $input->getArgument('year');
        $categoryArg = $input->getArgument('category');
        $fileArg = $input->getArgument('file');

        $year = $this->yearRepository->getBySlug($yearArg);
        if (!$year) {
            $question = (new ConfirmationQuestion("Year {$yearArg} not found. Do you want to generate it?"));
            if (!$dialog->ask($input, $output, $question)) {
                $output->writeln('<info>Import terminated.</info>');
                return 1;
            }
            $year = new Year();
            $year->year = (int)$yearArg;
            $year->actual = 0;
            $year->slug = $yearArg;
            $this->yearRepository->persist($year);
        }

        $category = $this->categoryRepository->getBySlug($categoryArg, $year);
        if (!$category) {
            $question = (new ConfirmationQuestion("Category {$categoryArg} not found. Do you want to generate it?"));
            if (!$dialog->ask($input, $output, $question)) {
                $output->writeln('<info>Import terminated.</info>');
                return 1;
            }
            $category = new Category();
            $category->year = $year;
            $category->slug = $categoryArg;
            $category->name = $categoryArg;
            $category->default = 0;
            $this->categoryRepository->persist($category);
        }

        try {
            $count = $this->importer->import($category, getcwd() . '/' . $fileArg);
            $output->writeln("<info>Successfully imported {$count} matches into category {$category->slug}.</info>");
            // $this->reorder->reorder($category);
            // $output->writeln("<info>Successfully reordered category {$category->slug}.</info>");
            return 0;
        } catch (\Exception $e) {
            $output->writeln("<info>Importing failed: {$e->getMessage()}.</info>");
            return 1;
        }

    }


}