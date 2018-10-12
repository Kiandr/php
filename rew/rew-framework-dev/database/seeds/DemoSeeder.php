<?php

use REW\Phinx\Seed\AbstractSeed;
use REW\Seed\Dynamic\UserFormsDataSeeder;

/**
 * DemoSeeder
 */
class DemoSeeder extends AbstractSeed
{

    /**
     * @throws \Exception If missing SQL export
     * @throws \Exception If failed to copy images
     * @return void
     */
    public function run()
    {

        // Locate SQL file containing CRM demo database
        $sql = sprintf('%s/data_crm.sql', $this->getPathToDemoFiles());
        if (!file_exists($sql)) {
            throw new \Exception('Could not find SQL file');
        }

        // Get config options
        $options = $this->adapter->getOptions();

        // Execute SQL file
        $exec = sprintf(
            'mysql --default-character-set=%s --host=%s --user=%s --password=%s %s < %s',
            $options['charset'],
            $options['host'],
            $options['user'],
            escapeshellarg($options['pass']),
            $options['name'],
            $sql
        );

        // Execute contents of SQL file
        exec($exec, $output, $error);
        if ($error) {
            throw new \Exception('Error executing mysql.');
        }
        echo ' - Added CRM data to database.' . PHP_EOL;

        // Insert data that requires dynamic values (EG: active IDX listings)
        $container = \Container::getInstance();
        $userFormsSeeder = $container->make(UserFormsDataSeeder::class);
        try {
            $userFormsSeeder->run();
        } catch (Exception $e) {
            echo sprintf('Failed to generate dynamic data: %s', $e->getMessage()) . PHP_EOL;
        }

        // Copy uploads files to httpdocs
        $src = $this->getPathToDemoFiles();
        $out = $this->getPathToSiteFiles();
        $copy = sprintf(
            'chmod -R g+w %s/uploads/ && rsync -rpO --ignore-existing %s/uploads/* %s/uploads/',
            $src,
            $src,
            $out
        );
        exec($copy, $output, $error);
        if ($error) {
            throw new \Exception('Unable to copy uploads.');
        }
        echo ' - Copied demo files to website.' . PHP_EOL;
    }

    /**
     * Path to demo files
     * @return string
     */
    public function getPathToDemoFiles()
    {
        return sprintf('%s/demo', __DIR__);
    }

    /**
     * Path to site files
     * @return string
     */
    public function getPathToSiteFiles()
    {
        return sprintf('%s/../../httpdocs', __DIR__);
    }
}
