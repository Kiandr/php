<?php

namespace REW\Backend\Command\Traits;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Exception;

/**
 * ConfirmBranchNameTrait
 * This trait is used to require confirmation of the current Git branch name.
 * Classes using this trait must extend `REW\Backend\Command\AbstractCommand`
 * @package REW\Backend\Command\Traits
 */
trait ConfirmBranchNameTrait
{

    /**
     * @return void
     */
    protected function configureConfirmBranchName () {
        $this->addArgument('branch', InputArgument::OPTIONAL, 'Current Branch Name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     * @return void
     */
    protected function confirmBranchName (InputInterface $input, OutputInterface $output)
    {

        // Write to console
        $output->writeln([
            '===================',
            'Confirm Branch Name',
            '==================='
        ]);

        // Confirm execution by asking for current branch
        if (!$branch = $input->getArgument('branch')) {
            $branch = $this->getInputArgument('branch', $input, $output);
        } else {
            $output->writeLn(sprintf(
                '<info>Current Branch Name:</info> %s', $branch
            ));
        }

        // Confirm against current Git branch
        $this->assetGitBranchName($branch);

    }

    /**
     * @param string $branch
     * @throws Exception
     * @return void
     */
    protected function assetGitBranchName($branch)
    {

        // Get current branch name for confirmation of database reset
        if (!$confirm = exec('git rev-parse --abbrev-ref HEAD')) {
            throw new Exception('Failed to determine current Git branch.');
        }

        // Require confirmation
        if (empty($branch) || $branch !== $confirm) {
            throw new Exception(sprintf(
                '"%s" does not equal "%s".',
                $branch,
                $confirm
            ));
        }

    }

}
