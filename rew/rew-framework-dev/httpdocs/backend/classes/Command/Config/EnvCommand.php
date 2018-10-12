<?php

namespace REW\Backend\Command\Config;

use REW\Backend\Command\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Yaml\Yaml;

/**
 * EnvCommand
 * @package REW\Backend\Command\Config
 */
class EnvCommand extends AbstractCommand
{

    /**
     * @var string
     */
    const CONFIG_DIR = './config/env';

    /**
     * @var string
     */
    const CONFIG_FILE_GENERAL = 'general.yml';

    /**
     * @var string
     */
    const CONFIG_FILE_DATABASES = 'databases.yml';

    /**
     * @var string
     */
    const LANG_US = 'en-US';

    /**
     * @var string
     */
    const LANG_CA = 'en-CA';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('config:env')
            ->setDescription('Setup environment configuration files.')
            ->addArgument('skin', InputArgument::REQUIRED, 'Site Skin')
            ->addArgument('scheme', InputArgument::OPTIONAL, 'Site Scheme', 'default')
            ->addArgument('locale', InputArgument::OPTIONAL, 'Site Locale', self::LANG_US)
            ->addOption('idx-feed', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'IDX Feed')
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'MySQL Username')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'MySQL Password')
            ->addOption('database', 'd', InputOption::VALUE_REQUIRED, 'MySQL Database')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force overwriting existing configuration')
            ->setHelp('This command creates general.yml and databases.yml from provided options.')
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
            '================',
            '<options=bold>General Settings</>',
            '================',
        ]);

        // Verify general settings
        $skin = $this->getInputArgument('skin', $input, $output);
        $scheme = $this->getInputArgument('scheme', $input, $output);
        $locale = $this->getInputArgument('locale', $input, $output);

        // Verify IDX feed settings
        if ($feeds = $this->getInputOption('idx-feed', $input, $output)) {
            $feeds = is_string($feeds) ? explode(',', $feeds) : $feeds;
            $feeds = array_combine($feeds, $feeds);
            $feed = reset($feeds);
        }

        // Writing files if already exists
        $force = $input->getOption('force') ?: false;

        // Write output
        $output->writeLn([
            '=================',
            '<options=bold>Database Settings</>',
            '=================',
        ]);

        // Verify database credentials
        $database = $this->getInputOption('database', $input, $output, null);
        $username = $this->getInputOption('username', $input, $output, null);
        $password = $this->getInputOption('password', $input, $output, null, true);

        // general.yml
        $general = [
            'lang' => $locale,
            'skin' => $skin,
            'skin_scheme' => $scheme,
            'idx_feed' => $feed
        ];

        // Multi-IDX config
        if (count($feeds) > 1) {
            $feeds = array_combine($feeds, $feeds);
            $general['idx_feeds'] = array_map(function ($feed) {
                $title = str_replace(['_', '-'], ' ', $feed);
                return ['title' => strtoupper($title)];
            }, $feeds);
        }

        // databases.yml
        $databases = [
            'databases' => [
                'default' => [
                    'database' => $database,
                    'username' => $username,
                    'password' => $password
                ]
            ]
        ];

        // Write output
        $output->writeLn([
            '===================',
            '<options=bold>Saving Config Files</>',
            '===================',
        ]);

        // Save general configuration settings
        $generalYml = sprintf('%s/%s', self::CONFIG_DIR, self::CONFIG_FILE_GENERAL);
        if (!$force && file_exists($generalYml)) {
            $output->writeLn(sprintf('<comment>Found:</comment> %s', $generalYml));
        } else {
            if ($this->saveYamlFile($generalYml, $general)) {
                $output->writeLn(sprintf('<info>Saved:</info> %s', $generalYml));
            }
        }

        // Save database configuration settings
        $databasesYml = sprintf('%s/%s', self::CONFIG_DIR, self::CONFIG_FILE_DATABASES);
        if (!$force && file_exists($databasesYml)) {
            $output->writeLn(sprintf('<comment>Found:</comment> %s', $databasesYml));
        } else {
            if ($this->saveYamlFile($databasesYml, $databases)) {
                $output->writeLn(sprintf('<info>Saved:</info> %s', $databasesYml));
            } else {
                $output->writeLn(sprintf('<error></error> %s', $databasesYml));
            }
        }
    }

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
        return Yaml::dump($data, 2, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
    }
}
