<?php

namespace REW\Backend\Command\Changelog;

use REW\Backend\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * AbstractChangelogCommand
 * @package REW\Backend\Command\Changelog
 */
abstract class AbstractChangelogCommand extends AbstractCommand
{

    /**
     * @var string
     */
    protected $unrelease_dir = './changelogs/unreleased';

    /**
     * @var array
     */
    protected $commit_types = ['add', 'change', 'deprecate', 'remove', 'fix', 'security'];

    /**
     * @param string $filename
     * @param string $data
     * @return string|false
     */
    public function saveYamlFile($filename, $data)
    {
        return file_put_contents($filename, $this->getYaml($data));
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function getYaml($data)
    {
        $yaml_content = Yaml::dump($data, 2, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        return str_replace('/ +$/', '', $yaml_content);
    }

    /**
     * @param string $data
     * @return array
     */
    public function convertYaml($data)
    {
        return Yaml::parse($data);
    }

    /**
     * @param string $question
     * @param null $default
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    public function askQuestion($question, InputInterface $input, OutputInterface $output, $default = null)
    {

        $helper = $this->getHelper('question');

        $ask = new Question($question, $default);

        $ask->setNormalizer(function ($value) {
            // $value can be null here
            return $value ? trim(htmlEntities($value, ENT_QUOTES)) : '';
        });

        return $helper->ask($input, $output, $ask);
    }

    /**
     * @param string $question
     * @param array $choices
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    public function confirmQuestion($question, $choices, InputInterface $input, OutputInterface $output)
    {

        $helper = $this->getHelper('question');

        $ask = new ChoiceQuestion($question, $choices, 0);

        return $helper->ask($input, $output, $ask);
    }

    /**
     * @return bool
     */
    public function checkStaged(OutputInterface $output)
    {
        return !empty($this->executeCommand('git diff-index --cached --name-only HEAD', $output));
    }
}