<?php

namespace REW\Backend\Command\Db;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use \Exception;
use \PDOException;
use \PDO;

/**
 * PatchCommand
 * @package REW\Backend\Command\Db
 */
class PatchCommand extends AbstractDbCommand
{

    /**
     * @var string MIGRATION_TABLE
     */
    const MIGRATION_TABLE = '_migrations_legacy';

    /**
     * @var array PATCH_DIRS
     */
    const PATCH_DIRS = [self::PATCH_DIR, 'httpdocs/remote_db/_patches', 'remote_db/_patches'];

    /**
     * @var string
     */
    const PATCH_DIR = 'install/_patches';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('db:patch')
            ->setDescription('Perform SQL/PHP patches on database.')
            ->setHelp('This command executes sql/php patch files against the target database.')
            ->addArgument('commit', InputArgument::REQUIRED, 'Originating commit hash to patch from')
            ->addOption('hostname', null, InputOption::VALUE_REQUIRED, 'MySQL Hostname')
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'MySQL Username')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'MySQL Password')
            ->addOption('database', 'd', InputOption::VALUE_REQUIRED, 'MySQL Database')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Only list patches')
            ->addOption('skip', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'skip a patch', [])
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     * @throws PDOException
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);

        // Write output
        $io->title('Database Credentials');

        // Get site's database settings
        $dbSettings = $this->getDbSettings();

        // Verify database credentials
        $hostname = $input->getOption('hostname') ?: $dbSettings['hostname'];
        $database = $input->getOption('database') ?: $dbSettings['database'];
        $username = $input->getOption('username') ?: $dbSettings['username'];
        $password = $input->getOption('password') ?: $dbSettings['password'];
        $skipped = $input->getOption('skip');

        // Write output
        $output->writeLn([
            sprintf('<info>MySQL Hostname:</info> %s', $hostname),
            sprintf('<info>MySQL Database:</info> %s', $database),
            sprintf('<info>MySQL Username:</info> %s', $username),
            sprintf('<info>MySQL Password:</info> %s', str_repeat('*', strlen($password)))
        ]);

        // Write output
        $io->title('Verifying Connection');

        // Establish db connection to validate credentials
        $db = $this->getDbConnection($hostname, $username, $password, $database);
        $io->text('<info>Connection Successful</info>');

        // Write output
        $io->title('Locating Patch Files');

        // Get list of original patches
        $dryRun = $input->getOption('dry-run');
        $commit = $input->getArgument('commit');
        $patches = $this->getPatchFiles($commit, $output);

        // Write output
        $output->writeLn([
            sprintf('<info>Origin Commit:</info> %s', $commit),
            sprintf('<info>Patch Files Found:</info> %s', number_format(count($patches)))
        ]);

        // Require confirmation (ignore if --no-interaction or --dry-run)
        if (!$input->getOption('no-interaction') && empty($dryRun)) {
            $confirm = new ConfirmationQuestion('Are you sure you want to continue? ', false);
            if (!$this->getHelper('question')->ask($input, $output, $confirm)) {
                return;
            }
        }
        try {
            // check if legacy migration table exists
            if (empty($this->hasTable($db, self::MIGRATION_TABLE))) {
                    $this->executeQuery($db, $this->getMigrationTableSQL());
                    $io->text(sprintf('%s Table created', self::MIGRATION_TABLE));
            }
        } catch (PDOException $e) {
            // stop script
            $io->error(sprintf('Database Error: %s', $e->getMessage()));
            return;
        }

        // Write output
        $io->title(($dryRun ? 'Showing' : 'Running') . ' Patch Files');

        try {
            // Fetch run patches
            $query = sprintf('SELECT `migration_name`, `skipped` FROM `%s` ORDER BY `id` ASC;', self::MIGRATION_TABLE);
            $ranMigrations = $this->executeQuery($db, $query)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $io->error(sprintf('Database Error: %s', $e->getMessage()));
        }

        $dbPatchesRan = [];
        $dbPatchesSkip = [];
        if (!empty($ranMigrations)) {
            foreach ($ranMigrations as $migration) {
                if ($migration['skipped'] === 'true') {
                    $dbPatchesSkip[] = $migration['migration_name'];
                } else {
                    $dbPatchesRan[] = $migration['migration_name'];
                }
            }
        }

        // Run patch scripts
        foreach ($patches as $patch) {
            $patchFilename = pathinfo($patch, PATHINFO_FILENAME);

            // Skip on --dry-run
            if (!empty($dryRun)) {
                $io->text(sprintf('<comment>Patch Found:</comment> %s', $patchFilename));
                continue;
            }

            if (in_array($patchFilename, $dbPatchesRan)) {
                $io->text(sprintf('<comment>Patch Already Ran:</comment> %s', $patchFilename));
                continue;
            } elseif (in_array($patchFilename, $dbPatchesSkip)) {
                $io->text(sprintf('<comment>Patch Skipped:</comment> %s', $patchFilename));
                continue;
            } elseif (in_array($patchFilename, $skipped)) {
                try {
                    // Save in db
                    $io->text(sprintf('<comment>Skipping Patch:</comment> %s', $patchFilename));
                    $query = sprintf('INSERT INTO `%s` (`migration_name`, `skipped`) VALUES (:migration_name, :skipped);', self::MIGRATION_TABLE);
                    $this->executeQuery($db, $query, ['migration_name' => $patchFilename, 'skipped' => 'true']);
                } catch (PDOException $e) {
                    $io->error(sprintf('Failed to Record Skipped Patch in `%s`: %s', self::MIGRATION_TABLE, $patchFilename));
                    throw $e;
                }
                continue;
            }

            $timeStart = date("Y-m-d H:i:s");

            try {
                // Execute patch file
                $io->text(sprintf('<comment>Running Patch:</comment> %s', $patchFilename));
                $this->executePatch($hostname, $username, $password, $database, $patch, $output);

            // Failed to run patch
            } catch (Exception $e) {
                $io->error(sprintf('Failed Patch: %s', $patchFilename));
                throw $e;
            }

            // store patch in db
            $timeEnd = date("Y-m-d H:i:s");
            $query = sprintf('INSERT INTO `%s` (`migration_name`, `start_time`, `end_time`) VALUES (:migration_name, :start_time, :end_time);', self::MIGRATION_TABLE);
            try {
                $this->executeQuery($db, $query, ['migration_name' => $patchFilename, 'start_time' => $timeStart, 'end_time' => $timeEnd]);
            } catch (Exception $e) {
                $io->error(sprintf('Failed to Record Patch in `%s`: %s', self::MIGRATION_TABLE, $patchFilename));
                throw $e;
            }
        }
    }

    /**
     * @param string $commit
     * @param OutputInterface $output
     * @return array
     */
    protected function getPatchFiles($commit, OutputInterface $output)
    {
        $oldPatches = $this->getGitPatchFiles($commit, $output);
        $newPatches = $this->getGitPatchFiles('HEAD', $output);
        $patches = array_diff($newPatches, $oldPatches);
        natsort($patches);
        $gitPatches = array_filter($patches, function ($patch) {
            return strrpos(basename($patch), 'patch') === 0;
        });
        $svnPatches = array_filter($patches, function ($patch) {
            return strrpos(basename($patch), 'r') === 0;
        });
        $patches = array_merge($svnPatches, $gitPatches);
        return array_map(function ($patch) {
            return self::PATCH_DIR . '/' . $patch;
        }, $patches);
    }

    /**
     * @param string $commitId
     * @param OutputInterface $output
     * @throws Exception
     * @return array
     */
    protected function getGitPatchFiles($commitId, OutputInterface $output)
    {
        foreach (self::PATCH_DIRS as $path) {
            try {
                $command = 'git ls-tree --name-only -r %s:%s';
                $command = sprintf($command, $commitId, $path);
                return $this->executeCommand($command, $output);
            } catch (Exception $e) {
            }
        }
        // Unable to find patch files
        throw new Exception(sprintf(
            'Failed to find patch files for commit: %s',
            $commitId
        ));
    }

    /**
     * @param PDO $db
     * @param string $tableName
     * @throws PDOException
     * @return mixed
     */
    protected function hasTable(PDO $db, $tableName)
    {
        return $this->executeQuery($db, sprintf('SHOW TABLES LIKE "%s";', $tableName))->fetch();
    }

    /**
     * @param PDO $db
     * @param string $sql
     * @param array $params
     * @throws PDOException
     * @return \PDOStatement
     */
    protected function executeQuery(PDO $db, $sql, array $params = [])
    {
        $query = $db->prepare($sql);
        $query->execute($params);
        return $query;
    }

    /**
     * @return string
     */
    protected function getMigrationTableSQL()
    {
        return sprintf("CREATE TABLE `%s` (
              `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `migration_name` VARCHAR(200) DEFAULT NULL,
              `start_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `end_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
              `skipped` enum('true','false') NOT NULL DEFAULT 'false',
              UNIQUE (`migration_name`)
            );", self::MIGRATION_TABLE);
    }

    /**
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @param string $filename
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function executePatch($hostname, $username, $password, $database, $filename, OutputInterface $output)
    {
        $ext = end(explode('.', $filename));
        if ($ext === 'php') {
            $this->executePhpPatch($filename);
        } elseif ($ext === 'sql') {
            $this->executeSqlPatch($hostname, $username, $password, $database, $filename, $output);
        } else {
            throw new \Exception(sprintf(
                'Invalid patch file: %s',
                $filename
            ));
        }
    }

    /**
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @param string $filename
     * @param OutputInterface $output
     */
    protected function executeSqlPatch($hostname, $username, $password, $database, $filename, OutputInterface $output)
    {
        $command = 'mysql --default-character-set=utf8 --host=%s --user=%s --password=%s %s < %s';
        $command = sprintf($command, $hostname, $username, escapeshellarg($password), $database, $filename);
        $this->executeCommand($command, $output);
    }

    /**
     * @param string $filename
     * @throws Exception
     */
    protected function executePhpPatch($filename)
    {
        try {
            if (!file_exists($filename)) {
                throw new Exception(sprintf(
                    'File not found: %s',
                    $filename
                ));
            }
            require_once $filename;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
