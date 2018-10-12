#!/usr/bin/env php
<?php

namespace REW\Tools;

use REW\Backend\Command\Db\ResetCommand;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Phinx\Migration\AbstractMigration;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Phinx\Console\Command\AbstractCommand as PhinxCommand;
use Phinx\Console\Command\SeedCreate as PhinxSeedCreateCommand;
use Phinx\Console\Command\SeedRun as PhinxSeedRunCommand;
use Phinx\Console\Command\Create as PhinxCreateCommand;
use Phinx\Console\Command\Init as PhinxInitCommand;
use Phinx\Console\Command\Test as PhinxTestCommand;
use Phinx\Console\PhinxApplication;
use Phinx\Config\Config;
use Container;

$rootDir = realpath(__DIR__ . '/..');
require $rootDir . '/boot/app.php';

$dispatcher = new EventDispatcher();
$dispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) use ($rootDir) {
    $command = $event->getCommand();
    $output = $event->getOutput();

    // Disable the "init" and "test" command from running
    if ($command instanceof PhinxInitCommand || $command instanceof PhinxTestCommand) {
        $output->writeLn('<error>This command has been disabled.</error>');
        exit;
    }

    // Handle configuration for phinx commands
    if ($command instanceof PhinxCommand) {
        $output = $event->getOutput();
        $input = $event->getInput();

        // Current environment
        $environment = 'development';
        if ($input->hasOption('environment')) {
            if (null !== $input->getOption('environment')) {
                $environment = $input->getOption('environment');
            } else {
                $input->setOption('environment', $environment);
            }
        }

        // Output current env
        $output->writeLn(sprintf(
            '<comment>Loading configuration for %s</comment>',
            $environment
        ));

        // Get hooks from container
        $hooks = Container::getInstance()->get(HooksInterface::class);

        // Migration paths
        $migrationPaths = [sprintf('%s/database/migrations', $rootDir)];
        $migrationPaths = $hooks->hook(HooksInterface::HOOK_DB_MIGRATION_PATHS)->run($migrationPaths);
        $migrationPath = sprintf('{%s}', implode(',', array_unique($migrationPaths)));

        // Seed paths
        $seedPaths = [sprintf('%s/database/seeds', $rootDir)];
        $seedPaths = $hooks->hook(HooksInterface::HOOK_DB_SEED_PATHS)->run($seedPaths);
        $seedPath = sprintf('{%s}', implode(',', $seedPaths));

        // Must choose a path to put new migrations
        if ($command instanceof PhinxCreateCommand) {
            $question = new ChoiceQuestion('Choose a path for new migration:', $migrationPaths);
            $migrationPath = $command->getHelper('question')->ask($input, $output, $question);
        }

        // Must choose a path to put new seed files
        if ($command instanceof PhinxSeedCreateCommand) {
            $question = new ChoiceQuestion('Choose a path for new seed:', $seedPaths);
            $seedPath = $command->getHelper('question')->ask($input, $output, $question);
        }

        // Load default database settings
        $settings = Container::getInstance()->get(SettingsInterface::class);
        $database = $settings['databases']['default'];

        // Phinx configuration
        $config = new Config([
            'migration_base_class' => AbstractMigration::class,
            'environments' => [
                'default_database' => $environment,
                $environment => [
                    'adapter' => 'mysql',
                    'charset' => 'utf8',
                    'default_migration_table' => '_migrations',
                    'host' => $database['hostname'],
                    'user' => $database['username'],
                    'pass' => $database['password'],
                    'name' => $database['database'],
                    'port' => 3306
                ]
            ],
            'templates' => [
                'file' => AbstractMigration::MIGRATION_TEMPLATE
            ],
            'paths' => [
                'migrations' => $migrationPath,
                'seeds' => $seedPath
            ]
        ]);

        // Update phinx configuration
        $command->setConfig($config);

        // Running seed script
        if ($command instanceof PhinxSeedRunCommand) {
            if (!$seed = $input->getOption('seed')) {
                $output->writeLn('<error>You must specify a seeder to run.</error>');
                exit;
            }
            if (in_array(ResetCommand::OPT_RUN_ALL_DEMOSEEDERS, $seed)) {
                $seedSet = ['DemoSeeder'];
                $seedSet = $hooks->hook(HooksInterface::HOOK_DB_DEMO_SEEDER_NAMES)->run($seedSet);
                $input->setOption('seed', $seedSet);
            }
        }
    }
});

$phinx = new PhinxApplication();
$phinx->find('test')->setHidden(true);
$phinx->find('init')->setHidden(true);
$phinx->setDispatcher($dispatcher);
$phinx->run();
