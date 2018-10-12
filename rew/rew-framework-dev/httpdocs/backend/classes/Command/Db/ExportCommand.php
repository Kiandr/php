<?php

namespace REW\Backend\Command\Db;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ExportCommand
 * @package REW\Backend\Command\Db
 */
class ExportCommand extends AbstractDbCommand
{

    /**
     * @var array
     */
    const IGNORE_TABLES = [
        'campaigns_sent',
        'cms_cache',
        'delayed_emails',
        'history_data',
        'history_events',
        'history_users',
        'users_listings',
        'users_messages',
        'users_notes',
        'users_pages',
        'users_pageviews',
        'users_searches',
        'users_sessions',
        'users_viewed_listings',
        'users_viewed_searches',
    ];

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('db:export')
            ->setDescription('Export SQL file from database.')
            ->addArgument('filename', InputArgument::OPTIONAL, 'Name of MySQL file to export')
            ->addOption('full', null, InputOption::VALUE_NONE, 'Full database export (no tables ignored)')
            ->addOption('hostname', null, InputOption::VALUE_REQUIRED, 'MySQL Hostname')
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'MySQL Username')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'MySQL Password')
            ->addOption('database', 'd', InputOption::VALUE_REQUIRED, 'MySQL Database')
            ->setHelp(implode(PHP_EOL, [
                'This command uses mysqldump to export the target database.', '',
                'The --full option is used to include data from ignored tables:', '',
                sprintf("\t - %s", implode("\n\t - ", self::IGNORE_TABLES)), ''
            ]))
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Write output
        $output->writeLn([
            '====================',
            '<options=bold>Database Credentials</>',
            '===================='
        ]);

        // Get site's database settings
        $dbSettings = $this->getDbSettings();

        // Verify database credentials
        $hostname = $this->getInputOption('hostname', $input, $output, $dbSettings['hostname']);
        $database = $this->getInputOption('database', $input, $output, $dbSettings['database']);
        $username = $this->getInputOption('username', $input, $output, $dbSettings['username']);
        $password = $this->getInputOption('password', $input, $output, $dbSettings['password'], true);
        $filename = $input->getArgument('filename') ?: $this->getMySqlExportFilename($database);

        // Establish db connection to validate credentials
        $db = $this->getDbConnection($hostname, $username, $password, $database);

        // Ignored
        $exclude = [];
        if (!$input->getOption('full')) {
            $exclude = array_intersect(
                self::IGNORE_TABLES,
                $this->getTableNames($db)
            );
        }

        // Generate --ignore-table option string
        $options = implode('', array_map(function ($table) use ($database) {
            return sprintf(' --ignore-table=%s.%s', $database, $table);
        }, $exclude));

        // Generate mysql command to export database to files (minus ignored tables)
        $command = $this->getMySqlExportCommand($hostname, $username, $password, $database, [], $options);
        $command = sprintf("%s | sed -r 's/DEFINER=`\w+`@`[^`]+`//' | gzip > %s", $command, $filename);

        // Write output
        $output->writeLn([
            '==========================',
            '<options=bold>Performing Database Export</>',
            '=========================='
        ]);

        // Export table structure & data to file
        $this->executeCommand($command, $output);

        // Excluded tables
        if (!empty($exclude)) {
            $command = $this->getMySqlExportCommand($hostname, $username, $password, $database, $exclude, ' --no-data');
            $command = sprintf("%s | sed -r 's/DEFINER=`\w+`@`[^`]+`//' |gzip >> %s", $command, $filename);

            // Export ignored tables structure
            $this->executeCommand($command, $output);
        }
    }

    /**
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @param array $tables
     * @param string $options
     * @return string
     */
    public function getMySqlExportCommand($hostname, $username, $password, $database, $tables = [], $options = '')
    {
        $command = 'mysqldump --single-transaction --routines --default-character-set=utf8 --host=%s --user=%s --password=%s%s %s %s';
        return sprintf($command, $hostname, $username, escapeshellarg($password), $options, $database, implode(' ', $tables));
    }

    /**
     * @param string $database
     * @return string
     */
    public function getMySqlExportFilename($database)
    {
        return sprintf('%s-%s.sql.gz', $database, date('Y-m-d-H-i-s'));
    }
}
