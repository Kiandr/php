<?php

namespace REW\Backend\Command\Uat;

use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Style\SymfonyStyle;
use \PDOException;
use \Exception;
use \PDO;

/**
 * ExportCommand
 * @package REW\Backend\Command\Uat
 */
class ExportCommand extends AbstractUatCommand
{

    /**
     * @var string[]
     */
    const INCLUDE_TABLES = [
        'api_applications',
        'api_requests',
        'featured_offices',
        'auth',
        'agents',
        'associates',
        'lenders',
        'groups',
        'users',
        'users_forms',
        'users_groups',
        'users_listings',
        'users_searches',
        'users_viewed_listings',
        'users_viewed_searches',
        'users_messages',
        'users_notes',
        'users_pages',
        'users_sessions',
        'users_pageviews',
        'users_rejected',
        'users_reminders',
        'users_transactions',
        'history_events',
        'history_users',
        'history_data_normal',
        'history_data',
        'docs_categories',
        'docs',
        'docs_templates',
        'campaigns',
        'campaigns_emails',
        'campaigns_groups',
        'campaigns_users',
        'campaigns_sent',
        'calendar_events',
        'calendar_dates',
        'calendar_reminders',
        'calendar_types',
        'blog_tags',
        'blog_categories',
        'blog_entries',
        'blog_comments',
        'blog_links',
        'blog_pings',
        'blog_settings',
        '_listings',
        '_listing_fields',
        '_listing_locations'
    ];

    /**
     * @var resource
     */
    protected $handle;

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('uat:export')
            ->setDescription('Export UAT dataset from MySQL database.')
            ->addArgument('hostname', InputArgument::REQUIRED, 'MySQL Hostname')
            ->addArgument('username', InputArgument::REQUIRED, 'MySQL Username')
            ->addArgument('password', InputArgument::REQUIRED, 'MySQL Password')
            ->addArgument('database', InputArgument::REQUIRED, 'MySQL Database')
            ->addOption('filename', null, InputOption::VALUE_REQUIRED, 'Name of SQLq file to export')
            ->setHelp(implode(PHP_EOL, [
                'Export records from target database for the table :', '',
                sprintf("\t - %s", implode("\n\t - ", self::INCLUDE_TABLES)), ''
            ]))
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception If missing SQL export
     * @throws Exception If failed to copy images
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        // Verify database credentials
        $hostname = $input->getArgument('hostname');
        $database = $input->getArgument('database');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        // Get filename to save SQL export
        $filename = sprintf('%s-%s.sql', $database, date('Y-m-d-H-i-s'));
        $filename = $input->getOption('filename') ? : $filename;

        // Open file to save SQL to
        $this->openFile($filename);

        // Verify source vs destination
        $io->title('Verifying Database');

        // Source database to export from
        $srcDb = $this->connectDatabase($hostname, $username, $password, $database);

        // Connect to site's database
        $db = $this->getDbConnection();

        // Tables to copy
        $tableNames = self::INCLUDE_TABLES;

        // Verify source tables exist
        $output->write('Checking source tables: ');
        $this->assertTableNames($srcDb, $tableNames);
        $output->writeLn('<info>[OK]</info>');

        // Verify tables exist on database
        $output->write('Checking site tables: ');
        $this->assertTableNames($db, $tableNames);
        $output->writeLn('<info>[OK]</info>');
        $io->newLine();

        // Ignore missing columns from source
        $tables = [];
        foreach ($tableNames as $tableName) {
            $output->write(sprintf(' - %s: ', $tableName));
            $srcColumns = $this->getTableColumns($srcDb, $tableName);
            $dbColumns = $this->getTableColumns($db, $tableName);
            $columns = array_intersect($srcColumns, $dbColumns);
            $tables[$tableName] = $columns;
            $output->writeLn('<info>[OK]</info>');
        }

        // Wrap all queries in SQL transaction
        $queryString = 'START TRANSACTION;' . PHP_EOL;
        $this->writeToFile($queryString);

        // Export/import table data
        $io->newLine();
        $io->title('Exporting Database');
        foreach ($tables as $tableName => $columns) {
            $output->write(sprintf(' - %s: ', $tableName));
            $queryCols = implode('`, `', $columns);

            // Fetch data from source database
            $queryString = sprintf("SELECT `%s` FROM `%s`;", $queryCols, $tableName);
            $query = $srcDb->query($queryString);

            // Check if table has records
            $rowCount = $query->rowCount();
            if (empty($rowCount)) {
                $output->writeLn('<comment>[EMPTY]</comment>');
                continue;
            }

            // Write query string to export file
            $queryString = "REPLACE INTO `%s` (`%s`) VALUES " . PHP_EOL;
            $queryString = sprintf($queryString, $tableName, $queryCols);
            $this->writeToFile($queryString);

            // Start importing records
            $rowNumber = 0;
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $rowNumber++;

                // Generate multi-insert query
                $queryString = "\t" . sprintf('(%s)', implode(
                    ', ',
                    array_map(function ($value) use ($db) {
                        if (is_null($value)) {
                            return 'NULL';
                        }
                            return $db->quote($value);
                    }, $row)
                ));

                // Write query string to export file
                $queryString .= ($rowCount === $rowNumber ? '' : ', ') . PHP_EOL;
                $this->writeToFile($queryString);
            }

            // Write query string to export file
            $this->writeToFile(';' . PHP_EOL);

            // All done
            $output->writeLn(sprintf(
                '<info>[%s]</info>',
                number_format($rowNumber)
            ));
        }

        // Commit transaction when done
        $queryString = 'COMMIT;' . PHP_EOL;
        $this->writeToFile($queryString);

        // Close export file
        $this->closeFile();

        // Success
        $io->newLine();
        $io->success($filename);
    }

    /**
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @throws Exception
     * @return PDO
     */
    protected function connectDatabase($hostname, $username, $password, $database)
    {
        try {
            $dsn = sprintf('mysql:host=%s;dbname=%s', $hostname, $database);
            return new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => sprintf(
                    "SET NAMES 'utf8', `time_zone` = '%s';",
                    @date_default_timezone_get()
                )
            ]);
        } catch (PDOException $e) {
            throw new Exception(sprintf(
                'Fail to connect to database: %s',
                $e->getMessage()
            ));
        }
    }

    /**
     * @param PDO $db
     * @param array $tableNames
     * @throws Exception
     */
    protected function assertTableNames(PDO $db, array $tableNames)
    {
        $tables = array_intersect($tableNames, $this->getTableNames($db));
        if ($tables !== $tableNames) {
            throw new Exception(sprintf(
                'Missing one or more expected tables: %s',
                implode(', ', array_diff($tableNames, $tables))
            ));
        }
    }

    /**
     * @param PDO $db
     * @return array
     */
    protected function getTableNames(PDO $db)
    {
        if ($result = $db->query('SHOW TABLES;')) {
            return $result->fetchAll(PDO::FETCH_COLUMN);
        }
        return [];
    }

    /**
     * @param PDO $db
     * @param string $tableName
     * @return array
     */
    protected function getTableColumns(PDO $db, $tableName)
    {
        $query = $db->query(sprintf('DESCRIBE `%s`;', $tableName));
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @param string $filename
     * @throws Exception
     * @return void
     */
    protected function openFile($filename)
    {
        if (!$this->handle = fopen($filename, 'w+')) {
            throw new Exception(sprintf(
                'Failed to open file: %s',
                $filename
            ));
        }
    }

    /**
     * @param string $line
     * @throws Exception
     * @return void
     */
    protected function writeToFile($line)
    {
        if (fwrite($this->handle, $line) === false) {
            throw new Exception('Failed to write line.');
        }
    }

    /**
     * @return void
     */
    protected function closeFile()
    {
        fclose($this->handle);
    }
}
