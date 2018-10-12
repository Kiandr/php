<?php

namespace REW\Backend\Command\Db;

use REW\Backend\Command\Traits\ConfirmBranchNameTrait;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use \Exception;
use \PDO;

/**
 * DropCommand
 * @package REW\Backend\Command\Db
 */
class DropCommand extends AbstractDbCommand
{

    use ConfirmBranchNameTrait;

    /**
     * Configure "drop" command
     * @return void
     */
    protected function configure()
    {
        $this->setName('db:drop')
            ->setDescription('Drop tables & functions from database.')
            ->addOption('hostname', null, InputOption::VALUE_REQUIRED, 'MySQL Hostname')
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'MySQL Username')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'MySQL Password')
            ->addOption('database', 'd', InputOption::VALUE_REQUIRED, 'MySQL Database')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Do not ask for confirmation')
            ->configureConfirmBranchName()
        ;
    }

    /**
     * Execute "drop" command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Confirm current Git branch name
        $this->confirmBranchName($input, $output);

        // Write output
        $output->writeLn([
            '====================',
            '<options=bold>Database Credentials</>',
            '===================='
        ]);

        // Get site's database settings
        $dbSettings = $this->getDbSettings();

        // Verify database credentials
        if ($force = $input->getOption('force')) {
            $output->writeLn([
                sprintf('<info>MySQL Hostname:</info> [%s]', $dbSettings['hostname']),
                sprintf('<info>MySQL Database:</info> [%s]', $dbSettings['database']),
                sprintf('<info>MySQL Username:</info> [%s]', $dbSettings['username']),
                sprintf('<info>MySQL Password:</info> [%s]', str_repeat('*', strlen($dbSettings['password']))),
            ]);
        }
        $hostname = $force ? $dbSettings['hostname'] : $this->getInputOption('hostname', $input, $output, $dbSettings['hostname']);
        $database = $force ? $dbSettings['database'] : $this->getInputOption('database', $input, $output, $dbSettings['database']);
        $username = $force ? $dbSettings['username'] : $this->getInputOption('username', $input, $output, $dbSettings['username']);
        $password = $force ? $dbSettings['password'] : $this->getInputOption('password', $input, $output, $dbSettings['password'], true);

        // Connect to database & select all available tables
        $db = $this->getDbConnection($hostname, $username, $password, $database);

        // Write to console
        $output->writeln([
            '==================',
            'Performing DB Drop',
            '=================='
        ]);

        // Get tables & functions to drop
        $dbTables = $this->getTableNames($db);
        $dbFunctions = $this->getDbFunctions($db);

        // OUTPUT: Show dropped tables
        $output->writeLn(array_filter([
            sprintf('<comment> * Dropping %d Tables</comment>', count($dbTables)),
            ($output->isVeryVerbose() ? sprintf(' ** %s', implode(', ', $dbTables)) : NULL)
        ]));

        // OUTPUT: Show dropped functions
        $output->writeLn(array_filter([
            sprintf('<comment> * Dropping %d Functions</comment>', count($dbFunctions)),
            ($output->isVeryVerbose() ? sprintf(' ** %s', implode(', ', $dbFunctions)) : NULL)
        ]));

        // Nothing to do - let's stop here
        if (empty($dbTables) && empty($dbFunctions)) {
            return 0;
        }

        // Require confirm to continue
        $io = new SymfonyStyle($input, $output);
        if (empty($force)) {
            if (!$io->confirm('Are you sure you want to continue?', false)) {
                return 0;
            }
        }

        // Drop all database tables
        $this->dropDbTables($db, $dbTables);

        // Drop all database functions
        $this->dropDbFunctions($db, $dbFunctions);

    }

    /**
     * @param PDO $db
     * @return array
     */
    protected function getDbFunctions(PDO $db)
    {
        if ($result = $db->query('SHOW FUNCTION STATUS;')) {
            return $result->fetchAll(PDO::FETCH_COLUMN, 1);
        }
        return [];
    }

    /**
     * @param PDO $db
     * @param string[] $functions
     */
    protected function dropDbFunctions(PDO $db, array $functions)
    {
        foreach ($functions as $function) {
            $db->query(sprintf('DROP FUNCTION `%s`;', $function));
        }
    }

}
