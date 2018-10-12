<?php

namespace REW\Backend\Command\Server;

use REW\Backend\Command\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * RsyncCommand
 * @package REW\Backend\Command\Server
 */
class RsyncCommand extends AbstractCommand
{

    /**
     * @var string
     */
    const SOURCE_PATH = '~/app';

    /**
     * @var string
     */
    const DEST_PATH = '~/app';

    /**
     * @var array
     */
    const EXCLUDE_FILES = [
        'httpdocs/inc/cache/js/*',
        'httpdocs/inc/cache/css/*',
        'httpdocs/inc/cache/img/*',
        'httpdocs/inc/cache/tmp/*',
        'httpdocs/inc/cache/xml/*',
        'httpdocs/inc/cache/html/*',
        'upgrade.*'
    ];

    /**
     * @var array
     */
    const INCLUDE_FILES = [
        'httpdocs/inc/cache/xml/sitemap.xml',
        'httpdocs/uploads/'
    ];

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('server:rsync')
            ->setDescription('Use rsync to copy an existing site installation.')
            ->addArgument('source', InputArgument::OPTIONAL, 'SSH Source [user@host:path]')
            ->addArgument('dest', InputArgument::OPTIONAL, 'Destination Path', self::DEST_PATH)
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Perform a trial run with no changes made')
            ->addOption('exclude', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude files matching pattern')
            ->addOption('include', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Include files matching pattern')
            ->setHelp('This command uses rsync to copy a remote website.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Require user/host/path from server
        $source = $input->getArgument('source');
        list ($username, $hostname) = explode('@', $source, 2);
        list ($hostname, $pathname) = explode(':', $hostname);
        $pathname = $pathname ?: self::SOURCE_PATH;

        // Rebuild server string as source to
        $source = sprintf('%s%s:%s', $username ? $username . '@' : '', $hostname, $pathname);

        // Get destination path
        $dest = $input->getArgument('dest');

        // Write output
        $output->writeLn([
            '==========================',
            '<options=bold>Transferring Files</>',
            '==========================',
            sprintf('<info>Source:</info> %s', $source),
            sprintf('<info>Destination:</info> %s', $dest),
            ''
        ]);

        // Require confirmation (ignore if --no-interaction or --dry-run)
        if (!$input->getOption('no-interaction') && !$input->getOption('dry-run')) {
            $confirm = new ConfirmationQuestion('Are you sure you want to continue? ', false);
            if (!$this->getHelper('question')->ask($input, $output, $confirm)) {
                return;
            }
        }

        // Generate rsync command to execute
        $options = $this->getRsyncOptions($input);
        $rsync = $this->getRsyncCommand($username, $hostname, $pathname, $dest, $options);

        // Execute rsync command and display output
        $command = sprintf('%s | sed \'0,/^$/d\';', $rsync);
        $response = $this->executeCommand($command, $output);
        $output->writeLn($response);
    }

    /**
     * @param string $username
     * @param string $hostname
     * @param string $pathname
     * @param string $dest
     * @param array $options
     * @return string
     */
    public function getRsyncCommand($username, $hostname, $pathname, $dest, $options = [])
    {
        return sprintf('rsync -azPL --stats%s %s@%s:%s %s', implode($options), $username, $hostname, $pathname, $dest);
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    public function getRsyncOptions(InputInterface $input)
    {
        $options = [' --filter="exclude .svn"'];
        if ($input->getOption('dry-run')) {
            $options[] = ' --dry-run';
        }
        $options += $this->getExcludedFiles($input);
        $options += $this->getIncludedFiles($input);
        return $options;
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    public function getExcludedFiles(InputInterface $input)
    {
        $exclude = $input->getOption('exclude') ?: [];
        $exclude = array_merge(self::EXCLUDE_FILES, $exclude);
        return array_map(function ($path) {
            return sprintf(' --exclude="%s"', $path);
        }, $exclude);
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    public function getIncludedFiles(InputInterface $input)
    {
        $include = $input->getOption('include') ?: [];
        $include = array_merge(self::INCLUDE_FILES, $include);
        return array_map(function ($path) {
            return sprintf(' --include="%s"', $path);
        }, $include);
    }
}
