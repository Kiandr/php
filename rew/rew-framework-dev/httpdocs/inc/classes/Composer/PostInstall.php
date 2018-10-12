<?php

namespace REW\Composer;

use Exception;
use Composer\Script\Event;
use Composer\Semver\Constraint\EmptyConstraint;

use REW\Core\Interfaces\BootableInterface;
use REW\Core\Interfaces\InvokableInterface;

class PostInstall
{
    /**
     * @const The file used for linking modules to the application.
     */
    const BOOTSTRAP_FILE = __DIR__ . '/../../../../boot/loaders/01-package-hooks.php';

    /**
     * @const This type indicates a library, website, module or theme
     */
    const REW_TYPES = ['library', 'website', 'module', 'theme'];

    /**
     * @const This indicates the vendor of a REW website, REW module or REW theme
     */
    const REW_VENDORS = ['rew/', 'rew-website/', 'rew-module/', 'rew-theme/'];

    /**
     * Runs after a composer install or update, rebuilds the hooks file
     * @param Event $event
     * @throws Exception
     */
    public static function run(Event $event)
    {
        $repository = $event->getComposer()->getRepositoryManager()->getLocalRepository();
        $packages = [];
        foreach (static::REW_VENDORS as $vendor) {
            $packages = array_merge($packages, $repository->search($vendor));
        }
        $constraint = new EmptyConstraint();

        $autoloads = [];
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');

        foreach ($packages as $packageInfo) {
            $package = $repository->findPackage($packageInfo['name'], $constraint);

            $packageName = $package->getName();

            if (in_array($package->getType(), static::REW_TYPES)) {
                $binaries = $package->getBinaries();

                $event->getIO()->write(sprintf('Installing "%s" package', $packageName));

                $binaries = array_filter($binaries, function ($binary) {
                    return strpos($binary, 'install') !== false;
                });

                $binaryCount = count($binaries);
                if ($binaryCount > 1) {
                    throw new Exception('Website packages must provide at most one binary.');
                }
                if ($binaryCount) {
                    $cmd = escapeshellcmd($vendorDir . '/' . $packageName . '/' . $binaries[0]) . ' '
                        . escapeshellarg($vendorDir) . ' '
                        . escapeshellarg(realpath(__DIR__ . '/../../../../boot/app.php'));
                    $exitCode = 0;
                    $output = null;
                    $className = trim(exec($cmd, $output, $exitCode));

                    // Remove class name from output
                    if ($output) {
                        array_pop($output);
                    }

                    if ($output) {
                        echo implode(PHP_EOL, $output) . PHP_EOL;
                    }

                    if ($exitCode != 0) {
                        throw new Exception('Failed to install module ' . $packageName);
                    }

                    if ($className && !class_exists($className)) {
                        throw new Exception($className . ' in ' . $packageName . ' does not exist');
                    }

                    $autoloads[] = $className;
                }
            }
        }

        usort($autoloads, function ($a, $b) {
            if (is_subclass_of($a, $b)) {
                return 1;
            } else if (is_subclass_of($b, $a)) {
                return -1;
            }
            return 0;
        });

        // Build autoload file
        $fp = fopen(self::BOOTSTRAP_FILE, 'w');
        fwrite($fp, '<?php' . PHP_EOL . PHP_EOL);

        foreach ($autoloads as $autoload) {
            if (in_array(BootableInterface::class, class_implements($autoload))) {
                fwrite($fp, 'Container::getInstance()->get(' . $autoload . '::class)->boot();' . PHP_EOL);
            }
        }
        foreach ($autoloads as $autoload) {
            if (in_array(InvokableInterface::class, class_implements($autoload))) {
                fwrite($fp, 'Container::getInstance()->get(' . $autoload . '::class)->run();' . PHP_EOL);
            }
        }
        fclose($fp);
    }
}
