<?php

namespace REW\Backend\Command\Git;

use REW\Backend\Command\AbstractCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

/**
 * DeployCommand
 * @package REW\Backend\Command\Git
 */
class DeployCommand extends AbstractCommand {

    /**
     * Always excluded paths
     * @var string[]
     */
    const EXCLUDE_PATHS = [
        'httpdocs/uploads'
    ];

    /**
     * Known paths to add
     * @var string[]
     */
    const INCLUDE_PATHS = [
        'config/env',
        'boot/loaders',
        'httpdocs/backend/build',
        'httpdocs/backend/node_modules',
        'node_modules',
        'vendor',
        'httpdocs/build',
        'httpdocs/images'
    ];

    /**
     * Safe paths to delete
     * @var string[]
     */
    const DELETE_PATHS = [
        '~/app/node_modules/.cache',
        '~/app/httpdocs/backend/node_modules/.cache'
    ];

    /**
     * Command to push site live
     * @var string
     */
    const GIT_PUSH_COMMAND = 'git push live';

    /**
     * Name of node_modules folder
     * @var string
     */
    const NODE_MODULES = 'node_modules';

    /**
     * Name of master branch
     * @var string
     */
    const MASTER_BRANCH = 'master';

    /**
     * Configure "git:deploy" command
     * @return void
     */
    protected function configure () {
        $this->setName('git:deploy')
            ->setDescription('Push all required files from the "dev" site to the "live" site.')
            ->setHelp(implode(PHP_EOL, [
                sprintf('This command commits ignored files and runs: "%s"', self::GIT_PUSH_COMMAND),
                sprintf('* This command must only be ran on the "%s" branch.', self::MASTER_BRANCH)
            ]))
        ;
    }

    /**
     * Execute "git:deploy" command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception If not using master branch
     * @return void
     */
    protected function execute (InputInterface $input, OutputInterface $output) {

        // Write to console
        $output->writeln([
            '==============================',
            'Confirm Checkout Master Branch',
            '==============================',
        ]);

        // Confirm current branch is master
        $branchName = $this->getCurrentBranchName();
        if ($branchName !== self::MASTER_BRANCH) {
            throw new Exception(sprintf(
                'Must be using "%s" branch.',
                self::MASTER_BRANCH
            ));
        }
        $output->writeLn(sprintf(
            '<info>Current Branch Name:</info> %s',
            $branchName
        ));

        // Write to console
        $output->writeln([
            '=============================',
            'Check Previously Staged Files',
            '=============================',
        ]);

	// Output style formatter
        $io = new SymfonyStyle($input, $output);

        // Check if there are modified files
        $command = 'git diff --name-only --cached';
        if ($stagedFiles = $this->executeCommand($command, $output)) {
            $io->title('Found staged files:');
            $io->listing($stagedFiles);

            // Require confirm to continue
            if (!$io->confirm(sprintf(
                'Found %s Staged Files. Continue?',
                number_format(count($stagedFiles))
            ), false)) return 0;
        }

        // Write to console
        $output->writeln([
            '=============================',
            'Delete `vendor/**/.git` Paths',
            '=============================',
        ]);

        // Delete vendor git
        $this->executeCommand('find ~/app/vendor/ -type d -name ".git" -exec rm -rf {} +', $output);

        // Write to console
        $output->writeln([
            '=============================',
            'Delete Cached Files & Folders',
            '=============================',
        ]);

        // Remove temporary cached files
        $this->executeCommand('console cache:reset', $output);

        // Remove safe to delete paths
        foreach (self::DELETE_PATHS as $deletePath) {
            $this->executeCommand(sprintf(
                'rm -rf %s',
                escapeshellarg($deletePath)
            ), $output);
        }

        // Write to console
        $output->writeln([
            '=============================',
            'Add Known Git Ignored Folders',
            '=============================',
        ]);

        // Add known include path (quicker than one by one)
        foreach (self::INCLUDE_PATHS as $includePath) {
            if (!file_exists($includePath)) {
                $output->writeLn(sprintf(
                    '<info>Folder Not Found: %s</info>',
                    $includePath
                ));
                continue;
            }
            $this->executeCommand(sprintf(
                'git add -f %s',
                escapeshellarg($includePath)
            ), $output);
        }

        // Write to console
        $output->writeln([
            '=============================',
            'Find Ignored Files to Include',
            '=============================',
        ]);

        // Access list of gitignored files/directories and prompt to add them
        $command = 'git ls-files --others -i --exclude-standard';
        if ($ignoredPaths = $this->executeCommand($command, $output)) {
            $nodeModules = [];
            $ignoredFiles = [];

            // Process ignored paths to be added
            foreach ($ignoredPaths as $ignoredPath) {
                $ignoredPath = trim($ignoredPath);

                // Add module path for `node_modules` for performance gains
                $nodeModulePath = self::NODE_MODULES . DIRECTORY_SEPARATOR;
                if (($nodeModule = strpos($ignoredPath, $nodeModulePath)) !== false) {
                    $modulePath = substr($ignoredPath,0, $nodeModule + strlen($nodeModulePath));
                    $nodeModules[$modulePath] = isset($nodeModules[$modulePath]) ? $nodeModules[$modulePath] + 1 : 1;
                    if (!in_array($modulePath, $ignoredFiles)) {
                        $ignoredFiles[] = $modulePath;
                    }
                    continue;
                }

                // Add ignored files if not in list of excluded paths
                if (!in_array($ignoredPath, self::EXCLUDE_PATHS)) {
                    $ignoredFiles[] = $ignoredPath;
                }

            }

            // Found ignored files to add
            if (!empty($ignoredFiles)) {

                // List out files
                $io->title(sprintf(
                    'Found %s Ignored Files:',
                    number_format(count($ignoredFiles))
                ));
                $io->listing($ignoredFiles);

                // Require confirm to continue
                if (!$io->confirm(sprintf(
                    'Adding %s Ignored Files. Continue?',
                    number_format(count($ignoredFiles))
                ))) {
                    return 0;
                }

                // git add -f on the ignored files
                foreach ($ignoredFiles as $ignoredFile) {
                    $this->executeCommand(sprintf(
                        'git add -f %s',
                        escapeshellarg($ignoredFile)
                    ), $output);
                }

            }

        }

        // Write to console
        $output->writeln([
            '=============================',
            'Review Stage for Final Commit',
            '=============================',
        ]);

        // Require confirm to continue
        if (!$io->confirm('Continue to commit these changes and push them live?', false)) {
            return 0;
        }

        // Generate commit message
        $commitMessage = sprintf(
            '[%s]: commit ignored files for deployment',
            $this->getName()
        );

        // Commit staged files
        $this->executeCommand(sprintf(
            'git commit -m "%s"',
            $commitMessage
        ),$output);

        // Write to console
        $output->writeln([
            '==========================',
            'Deploy Branch to Live Site',
            '==========================',
        ]);

        // Deploy to live site via Git push command
        $this->executeCommand(self::GIT_PUSH_COMMAND, $output);

    }

    /**
     * Get current branch name from Git
     * @return string
     */
    protected function getCurrentBranchName () {
        if (!$branchName = exec('git rev-parse --abbrev-ref HEAD')) {
            throw new Exception('Failed to determine current Git branch.');
        }
        return $branchName;
    }

}
