<?php

namespace REW\Backend\Command\Db;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use \Exception;

/**
 * ImportCommand
 * @package REW\Backend\Command\Db
 */
class ImportCommand extends AbstractDbCommand
{

    /**
     * @var string
     */
    const IMPORT_ABORT = 'abort';

    /**
     * @var string
     */
    const IMPORT_CONTINUE = 'continue';

    /**
     * @var string
     */
    const IMPORT_TRUNCATE = 'truncate';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('db:import')
            ->setDescription('Import SQL file to database.')
            ->setHelp('This command executes mysql against the target database.')
            ->addOption('hostname', null, InputOption::VALUE_REQUIRED, 'MySQL Hostname')
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'MySQL Username')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'MySQL Password')
            ->addOption('database', 'd', InputOption::VALUE_REQUIRED, 'MySQL Database')
            ->addArgument('filename', InputArgument::REQUIRED, 'Name of MySQL file to import')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Make sure file exists
        $filename = $input->getArgument('filename');
        if (!file_exists($filename)) {
            throw new Exception(sprintf(
                'File does not exist: %s',
                $filename
            ));
        }

        // Write output
        $output->writeLn([
            '====================',
            '<options=bold>Database Credentials</>',
            '====================',
        ]);

        // Get site's database settings
        $dbSettings = $this->getDbSettings();

        // Verify database credentials
        $hostname = $this->getInputOption('hostname', $input, $output, $dbSettings['hostname']);
        $database = $this->getInputOption('database', $input, $output, $dbSettings['database']);
        $username = $this->getInputOption('username', $input, $output, $dbSettings['username']);
        $password = $this->getInputOption('password', $input, $output, $dbSettings['password'], true);

        // Write output
        $output->writeLn([
            '====================',
            '<options=bold>Verifying Connection</>',
            '====================',
        ]);

        // Establish db connection to validate credentials
        $db = $this->getDbConnection($hostname, $username, $password, $database);
        $output->writeLn('<info>Connection Successful</info>');

        // Check if database has existing table
        if ($tables = $this->getTableNames($db)) {
            $output->writeLn(['<error>', 'DATABASE IS NOT EMPTY!', '</error>']);


            // Abort/continue/truncate
            $opts = [self::IMPORT_ABORT, self::IMPORT_CONTINUE, self::IMPORT_TRUNCATE];
            $confirm = new ChoiceQuestion('How would you like to continue? (defaults to "abort")', $opts, 0);
            $import = $this->getHelper('question')->ask($input, $output, $confirm);

            // Abort database import
            if ($import == self::IMPORT_ABORT) {
                return;

            // Drop database tables
            } elseif ($import === self::IMPORT_TRUNCATE) {
                $this->dropDbTables($db, $tables);
                $output->writeLn(sprintf('<info>Successfully Dropped %s Tables</info>', count($tables)));
            }
        }

        // Generate mysql command to import from file to database
        $command = $this->getMySqlImportCommand($hostname, $username, $password, $database, $filename);

        // Execute mysql import command
        $this->executeCommand($command, $output);
    }

    /**
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @param string $filename
     * @return string
     */
    public function getMySqlImportCommand($hostname, $username, $password, $database, $filename)
    {
        $command = 'mysql --default-character-set=utf8 --host=%s --user=%s --password=%s %s';
        $command = sprintf($command, $hostname, $username, escapeshellarg($password), $database);
        $ext = end(explode('.', $filename));
        if ($ext === 'gz') {
            return sprintf('gunzip < %s | %s', $filename, $command);
        }
        return sprintf('%s < %s', $command, $filename);
    }
}
