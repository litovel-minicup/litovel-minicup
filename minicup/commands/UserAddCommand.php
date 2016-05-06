<?php

namespace Minicup\Commands;


use Minicup\Model\Manager\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class UserAddCommand extends Command
{
    /** @var UserManager @inject */
    public $userManager;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('app:add-user')
            ->addArgument('username', InputArgument::REQUIRED, 'Username to add')
            ->addArgument('role', InputArgument::OPTIONAL, 'Username to add', 'guest');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $dialog */
        $dialog = $this->getHelper('question');
        $username = $input->getArgument('username');
        $role = $input->getArgument('role');

        $question = (new Question('Password please: '))->setHidden(TRUE);
        $questionCheck = (new Question('Retype password please: '))->setHidden(TRUE);

        if (($password = $dialog->ask($input, $output, $question)) !== $dialog->ask($input, $output, $questionCheck)) {
            $output->write('Passwords aren\'t same! Terminating.');
            return 1;
        }
        try {
            $this->userManager->add($username, $password, $username, $role);
            $output->writeln("<info>Successfully added user {$username}.</info>");
            return 0;
        } catch (\Exception $e) {
            $output->writeln('<info>Failed.</info>');
            return 1;
        }

    }


}