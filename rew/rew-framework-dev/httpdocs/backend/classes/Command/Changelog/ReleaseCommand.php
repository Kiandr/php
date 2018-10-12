<?php

namespace REW\Backend\Command\Changelog;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ReleaseCommand
 * @package REW\Backend\Command\Changelog
 */
class ReleaseCommand extends AbstractChangelogCommand
{

    /**
     * @var string CHANGELOG
     */
    const CHANGELOG = 'CHANGELOG.md';

    /**
     * @var string REPO_URL
     */
    const REPO_URL = 'https://git.rewhosting.com/rew/rew-framework/%s/';

    /**
     * @var string ARCHIVE
     */
    const ARCHIVE = './changelogs/ARCHIVE.md';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('changelog:release')
            ->setDescription('Merge unreleased changelogs.')
            ->addArgument('release', InputArgument::REQUIRED, 'New release tag.')
            ->addOption('commit', 'c', InputOption::VALUE_NONE, 'Commits changelog after update')
            ->addOption('overwrite', 'o', InputOption::VALUE_NONE, 'Overwrites the changelog')
            ->setHelp('This command allows you to create a new release changelog.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);

        $release = $input->getArgument('release');
        $commit = !empty($input->getOption('commit'));
        $overwrite = !empty($input->getOption('overwrite'));

        $io->title('Checking Git Staged');
        if ($this->checkStaged($output)) {
            // exit command
            $io->error('You have files staged');
            return 0;
        }


        $raw_changelog['title'] = sprintf('%s (%s)', $release, date("Y-m-d"));

        $io->title('Fetching Unreleased Changelogs');
        $files = $this->fetchChangelogs();

        if (!empty($files)) {
            foreach ($files as $file) {
                $io->text(sprintf('Found: %s', $file));

                $type = array_shift(explode('-', $file));
                $content = $this->convertYaml(file_get_contents(sprintf('%s/%s', $this->unrelease_dir, $file)));
                $raw_changelog['commits'][$type][] = $content;
            }

            $io->text('Converting to Markdown');
            $changelog = $this->markdown($raw_changelog);

            $io->title(sprintf('Updating %s', self::CHANGELOG));
            if ($this->updateChangelog($changelog, $overwrite)) {
                $io->text(sprintf('Archiving changes to %s', self::ARCHIVE));
                $this->updateArchivelog($changelog);

                $io->title('Deleting Unreleased Changelogs');
                foreach ($files as $file) {
                    $io->text(sprintf('Delete: %s', $file));
                    unlink(sprintf('%s/%s', $this->unrelease_dir, $file));
                }

                if ($commit) {
                    $this->commitDeleted($this->unrelease_dir, $output);

                    $io->text('Committing Changelog');
                    $this->commit(self::CHANGELOG, sprintf('Update %s for %s', self::CHANGELOG, $release), $output);
                }

                $io->success('Changelog Update Complete');
            } else {
                $io->error('Changelog failed to be updated');
            }
        } else {
            $io->error(sprintf('No Entries found in %s', $this->unrelease_dir));
        }
    }

    /**
     * @return array
     */
    protected function fetchChangelogs()
    {
        foreach (glob(sprintf('%s/*.yml', $this->unrelease_dir)) as $file) {
            $files[] = array_pop(explode('/', $file));
        }
        return $files;
    }

    /**
     * @param array $data
     * @return string
     */
    protected function markdown($data)
    {
        $styled = sprintf('# %s', $data['title']) . PHP_EOL . PHP_EOL;
        foreach ($this->commit_types as $commit_type) {
            if (isset($data['commits'][$commit_type])) {
                $styled .= sprintf('### %s', ucfirst($commit_type)) . PHP_EOL;
                foreach ($data['commits'][$commit_type] as $commit) {
                    $styled .= $this->markdownEntry($commit) . PHP_EOL;
                }
                $styled .= PHP_EOL;
            }
        }
        return $styled;
    }

    /**
     * @param array $entry
     * @return string
     */
    protected function markdownEntry($entry)
    {
        $data = sprintf('* %s', $entry['title']);
        $data .= !empty($entry['merge_request']) ? sprintf(' [!%s](%s/)', $entry['merge_request'], sprintf(self::REPO_URL, 'merge_requests') . $entry['merge_request']) : '';
        $data .= !empty($entry['author']) ? sprintf(' (%s)', $entry['author']) : '';
        return $data;
    }

    /**
     * @param string $log
     * @param bool $overwrite
     * @return bool
     */
    protected function updateChangelog($log, $overwrite = false)
    {
        // prepend log to changelog, prepends log to /changelog.md
        if (!$overwrite) {
            $file_contents = file_get_contents(self::CHANGELOG);
            $log = $log . $file_contents;
        }

        if (!file_put_contents(self::CHANGELOG, $log, LOCK_EX)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $log
     * @return bool
     */
    protected function updateArchivelog($log)
    {
        // archive log, prepends log to /unreleased/archive.md
        if (file_exists(self::ARCHIVE)) {
            $file_contents = file_get_contents(self::ARCHIVE);
            $archive_log = $log . $file_contents;
            file_put_contents(self::ARCHIVE, $archive_log, LOCK_EX);
        } else {
            file_put_contents(self::ARCHIVE, $log, LOCK_EX);
        }

        return true;
    }

    /**
     * @param array $dir
     * @param OutputInterface $output
     */
    protected function commitDeleted($dir, $output)
    {
        $this->executeCommand(sprintf('git add -u %s', $dir), $output);
        $this->executeCommand('git commit -m "DELETE: old unreleased changelogs"', $output);
    }

    /**
     * @param string $file_path
     * @param string $message
     * @param OutputInterface $output
     */
    protected function commit($file_path, $message, $output)
    {
        $this->executeCommand(sprintf('git add %s', $file_path), $output);
        $this->executeCommand(sprintf('git commit -m "RELEASE: %s"', $message), $output);
    }
}
