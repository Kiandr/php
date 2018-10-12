<?php

namespace REW\Backend\Command\Changelog;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CreateCommand
 * @package REW\Backend\Command\Changelog
 */
class CreateCommand extends AbstractChangelogCommand
{

    /**
     * @var string $branch_name
     */
    private $branch_name;

    /**
     * @var array $error
     */
    private $error;

    /**
     * @var string $error_number
     */
    private $error_number;

    /**
     * @var OutputInterface $output
     */
    private $output;

    /**
     * @return void
     */
    protected function configure()
    {

        $this->setName('changelog:create')
            ->setDescription('Create a changelog entry.')
            ->setHelp('This command allows you to create a YAML file in the changelog/unrelease directory, which will be used to generate a changelog on version release.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer $this->error_number
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $io = new SymfonyStyle($input, $output);

        $io->title('Creating Changelog');
        $io->text('Checking Branch is not Master or Dev');

        if ($this->checkBranchType()) {
            $commit_type = $this->confirmQuestion('Commit Category: ', $this->commit_types, $input, $output);

            $cl_name = strtolower(str_replace("-", " ", $this->branchName()));
            $cl_filepath = sprintf('%s/%s-%s_%s.yml', $this->unrelease_dir, $commit_type, $cl_name, date('YmdHis'));

            if ($this->checkChangelogExist($cl_filepath)) {
                $io->text('Title is what will be used in the changelog, please be descriptive of the work done.');
                $title = $this->askQuestion('Title: ', $input, $output);

                if ($this->checkTitle($title)) {
                    $merge_request = $this->askQuestion('Merge Request ID: ', $input, $output);

                    $io->title('Fetching Author');
                    $author = $this->getUser();

                    if (empty($author)) {
                        $io->text('Author Not Found');
                        $author = $this->askQuestion('Author: ', $input, $output);
                    }

                    $yaml_content = [
                        'title' => $title,
                        'author' => $author,
                        'merge_request' => $merge_request
                    ];

                    if ($this->saveYamlFile($cl_filepath, $yaml_content)) {
                        $io->success(sprintf('Saved: %s', $cl_filepath));
                    }
                }
            }
        }

        if (!empty($this->error)) {
            $io->error($this->error);
            return $this->error_number;
        }
    }

    /**
     * Checks the current branch is not master
     * @return bool
     */
    protected function checkBranchType()
    {
        if (empty($this->branch_name)) {
            $this->branchName();
        }

        if (in_array($this->branch_name, ['master', 'dev'])) {
            $this->error = 'Create a branch first!';
            $this->error_number = 1;
        }
        return empty($this->error);
    }

    /**
     * @param string $cl_filepath
     * @return bool
     */
    protected function checkChangelogExist($cl_filepath)
    {
        if (file_exists($cl_filepath)) {
            $this->error = sprintf('%s already exists!', $cl_filepath);
            $this->error_number = 2;
        }
        return empty($this->error);
    }

    /**
     * @param string $title
     * @return bool
     */
    protected function checkTitle($title)
    {
        if (empty($title)) {
            $this->error = 'Provide a title for the changelog entry.';
            $this->error_number = 3;
        }
        return empty($this->error);
    }

    /**
     * @return string
     */
    protected function branchName()
    {
        if (empty($this->branch_name)) {
            $this->branch_name = trim(array_values($this->executeCommand('git symbolic-ref --short HEAD', $this->output))[0]);
        }
        return $this->branch_name;
    }

    /**
     * @return string
     */
    protected function getTitle()
    {
        return trim(array_values($this->executeCommand('git log --format=\"%s\" -1', $this->output))[0]);
    }

    /**
     * @return string
     */
    protected function getUser()
    {
        return trim(array_values($this->executeCommand('echo ${GIT_AUTHOR_NAME}', $this->output))[0]);
    }
}
