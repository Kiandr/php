<?php

namespace REW\Backend\Command\Git;

use REW\Backend\Command\AbstractCommand;
use REW\Core\Interfaces\SettingsInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Container;
use Exception;

/**
 * CheckoutCommand
 * @package REW\Backend\Command\Git
 */
class CheckoutCommand extends AbstractCommand {

    /**
     * @var string
     */
    const ORIGIN_NAME = 'framework';

    /**
     * @var array
     */
    const THEME_PACKAGES = [
        'REW\\Theme\\Enterprise\\Theme' => 'rew-theme/enterprise'
    ];

    /**
     * Configure "git:checkout" command
     * @return void
     */
    protected function configure () {
        $this->setName('git:checkout')
            ->setDescription('Checkout latest framework branch.')
            ->addArgument('branch', InputArgument::OPTIONAL, 'Branch Name')
        ;
    }

    /**
     * Execute "git:checkout" command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     * @return void
     */
    protected function execute (InputInterface $input, OutputInterface $output) {

        // Write to console
        $output->writeln([
            '===================',
            'Check for Changes',
            '===================',
        ]);

        // Check if there are modified files
        $command = 'git ls-files -m';
        if ($modifiedFiles = $this->executeCommand($command, $output)) {
            $io = new SymfonyStyle($input, $output);
            $io->title('Found modified files:');
            $io->listing($modifiedFiles);

            // Require confirm to continue
            if (!$io->confirm(sprintf(
                'Found %d modified files. Continue?',
                count($modifiedFiles)
            ), false)) return 0;

        }

        // Write to console
        $output->writeln([
            '===================',
            'Performing Rollback',
            '===================',
        ]);

        // Perform database rollback before all other things
        $rollbackCommand = 'php tools/phinx.php rollback -t 0';
        $this->executeCommand($rollbackCommand, $output);

        // Write to console
        $output->writeln([
            '===================',
            'Performing Checkout',
            '===================',
        ]);

        // Get name of branch to be checked out
        if (!$branchName = $input->getArgument('branch')) {
            $branchName = $this->getInputArgument('branch', $input, $output);
        } else {
            $output->writeLn(sprintf(
                '<info>Branch Name:</info> %s',
                $branchName
            ));
        }

        // Checkout branch & update to latest
        $this->checkoutLatestBranch($branchName, $output);

        // Reset application cache
        $this->executeCommand('console cache:reset', $output);

        // Install latest theme package
        if ($themePackage = $this->getThemePackage()) {
            $command = sprintf('composer require %s', $themePackage);
            $this->executeCommand($command, $output);
        } else {
            $command = 'composer install';
            $this->executeCommand($command, $output);
        }

        // Update NPM dependencies
        $command = 'npm install';
        $this->executeCommand($command, $output);

        // Update theme package
        if (!empty($themePackage)) {
            $command = 'cd ~/app/vendor/%s && npm install';
            $command = sprintf($command, $themePackage);
            $this->executeCommand($command, $output);
        }

        // Rebuild backend assets (production build)
        $command = 'cd ~/app/httpdocs/backend && npm install && npm run ship-js && npm run build-css';
        $this->executeCommand($command, $output);

        // Rollback & reset application database
        $command = sprintf('console db:reset %s --seed', $branchName);
        $this->executeCommand($command, $output);

    }

    /**
     * @param $branchName
     * @param OutputInterface $output
     */
    protected function checkoutLatestBranch ($branchName, OutputInterface $output) {
        $command = sprintf('git branch --list %s', $branchName);
        $exists = $this->executeCommand($command, $output);
        if (!empty($exists)) {
            $commands = [
                sprintf('git checkout %s', $branchName),
                sprintf('git pull %s %s', self::ORIGIN_NAME, $branchName)
            ];
        } else {
            $commands = [
                sprintf('git fetch %s %s', self::ORIGIN_NAME, $branchName),
                sprintf('git checkout -b %2$s %1$s/%2$s', self::ORIGIN_NAME, $branchName)
            ];
        }
        foreach ($commands as $command) {
            $this->executeCommand($command, $output);
        }
    }

    /**
     * @return string|NULL
     */
    protected function getThemePackage () {
        $container = Container::getInstance();
        $settings = $container->get(SettingsInterface::class);
        $themeClass = $settings['SKIN'];
        if ($themePackage = self::THEME_PACKAGES[$themeClass]) {
            return $themePackage;
        }
        return NULL;
    }

}
