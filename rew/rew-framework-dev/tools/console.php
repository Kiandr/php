#!/usr/bin/env php
<?php

namespace REW\Tools;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;
use \Container;

// Require application bootstrap
$rootDir = realpath(__DIR__ . '/..');
require $rootDir . '/vendor/autoload.php';

// Register container
$app = new Container();
$app->boot(require $rootDir . '/config/dirs.php', require $rootDir . '/config/bindings.php');
Container::setInstance($app);

// REW paths
$nsPaths = [
    'REW\\' => sprintf('%s/httpdocs/inc/classes', $rootDir),
    'REW\\Backend' => sprintf('%s/httpdocs/backend/classes', $rootDir)
];

// Find commands
$finder = new Finder;
$finder->files()->name('*Command.php')->in($nsPaths);

// REW console application
$console = new Application;

// Add console commands
foreach ($finder as $file) {
    // Get path namespace
    $path = $file->getPath();
    $relPath = $file->getRelativePath();
    $nsPath = substr($path, 0, strpos($path, $relPath) - 1);
    $nsPath = array_search($nsPath, $nsPaths);

    // Get full qualified class name
    $namespace = str_replace('/', '\\', $relPath);
    $className = sprintf('\\%s\\%s\\%s', $nsPath, $namespace, $file->getBasename('.php'));

    // Add command to console tools
    if (class_exists($className)) {
        $r = new \ReflectionClass($className);
        if ($r->isSubclassOf(Command::class) && !$r->isAbstract() && !$r->getConstructor()->getNumberOfRequiredParameters()) {
            $console->add($r->newInstance());
        }
    }
}

// Run application
$console->run();
