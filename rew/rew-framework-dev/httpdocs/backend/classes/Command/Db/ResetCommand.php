<?php

namespace REW\Backend\Command\Db;

use REW\Backend\Command\AbstractCommand;
use REW\Backend\Command\Traits\ConfirmBranchNameTrait;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Exception;

/**
 * ResetCommand
 * @package REW\Backend\Command\Db
 */
class ResetCommand extends AbstractCommand
{

    use ConfirmBranchNameTrait;

    /**
     * Let phinx know that all DemoSeeders should be run
     */
    const OPT_RUN_ALL_DEMOSEEDERS = 'run_all_demoseeders';

    /**
     * Configure "reset" command
     * @return void
     */
    protected function configure()
    {
        $this->setName('db:reset')
            ->setDescription('Rollback and reinstall database.')
            ->addOption('seed', 's', InputOption::VALUE_NONE, 'Run DemoSeeder')
            ->configureConfirmBranchName()
        ;
    }

    /**
     * Execute "reset" command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Confirm current Git branch name
        $this->confirmBranchName($input, $output);

        // Write to console
        $output->writeln([
            '===================',
            'Performing DB Reset',
            '==================='
        ]);

        // Run commands to reset database
        $seed = $input->getOption('seed');
        foreach ($this->getCommands($seed) as $command) {
            $this->executeCommand($command, $output);
        }
    }

    /**
     * @return array
     */
    protected function getCommands($seed = false)
    {
        return array_filter([
            'php tools/phinx.php rollback -t 0',
            'php tools/phinx.php migrate',
            'php install/install.php',
            $seed ? sprintf('php tools/phinx.php seed:run -s %s', self::OPT_RUN_ALL_DEMOSEEDERS) : ''
        ]);
    }
}
