<?php

use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\SettingsFileMergerInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;

class DBFactory implements DBFactoryInterface
{
    /**
     * DB Array
     * @var DB[]
     */
    protected $connections = array();

    /**
     * @var SettingsFileMergerInterface
     */
    private $settings;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * DBFactory constructor.
     * @param SettingsFileMergerInterface $settings
     * @param ContainerInterface $container
     */
    public function __construct(SettingsFileMergerInterface $settings, ContainerInterface $container)
    {
        $this->settings = $settings;
        $this->container = $container;
    }

    /**
     * Get connection settings by name
     * @param string $name
     * @throws Exception
     * @return array|NULL
     */
    public function settings($name = 'default')
    {
        $originalName = $name;
        $name = $this->getName($name);

        // Get DB Settings
        $dbs = $this->settings->importAndMergeSources(
            $this->container->get(SettingsInterface::CONFIG_IMPORTS_KEY),
            'db'
        )['databases'];
        if (isset($dbs[$name])) {
            return $dbs[$name];
        }

        if (!empty($name)) {
            // Check IDX Feeds
            $feed = realpath(__DIR__ . '/../../idx/settings/' . str_replace('_', '-', $name));
            $feed = !empty($feed) ? $feed : realpath(__DIR__ . '/../../idx/settings/' . str_replace('-', '_', $name));
        }

        if (empty($feed)) {
            // Check IDX Feeds against the original name.
            $feed = realpath(__DIR__ . '/../../idx/settings/' . str_replace('_', '-', $originalName));
            $feed = !empty($feed) ? $feed : realpath(__DIR__ . '/../../idx/settings/' . str_replace('-', '_', $originalName));
        }

        if (!empty($feed)) {
            $settings = $feed . '/Database.settings.php';
            if (file_exists($settings)) {
                require $settings;
                /** @var array $DATABASE */
                $db = $DATABASE[0]['settings'];
                return array(
                    'hostname' => $db['host'],
                    'username' => $db['user'],
                    'password' => $db['pass'],
                    'database' => $db['db']
                );
            }
        }

        // Unknown Database
        throw new Exception('Unknown Database Connection: ' . $name);
    }

    /**
     * Load connection by name/alias
     * @param string $name
     * @throws Exception
     * @return DB|NULL
     */
    public function get($name = 'default')
    {
        $timer = Profile::timer()->stopwatch(__METHOD__)->start();
        $originalName = $name;
        $name = $this->getName($name);
        if (empty($name)) {
            // Try the original name too
            $name = $originalName;
        }
        if (empty($this->connections[$name])) {
            try {
                $db = $this->settings($name);
                $this->connections[$name] = $this->container->make(DB::class, ['host' => $db['hostname'], 'user' => $db['username'], 'pass' => $db['password'], 'name' => $db['database']]);
            } catch (Exception $e) {
                throw $e;
            }
            $timer->setDetails('<strong>New DB Connection: '.htmlspecialchars($name).' ('.htmlspecialchars($db['username']).'@'.$db['hostname'].')</strong>');
        } else {
            $timer->setDetails('<strong>Cached DB Connection: '.htmlspecialchars($name).'</strong>');
        }
        $timer->stop();
        return $this->connections[$name];
    }

    /**
     * Get connection name
     * @param string $name
     * @throws Exception
     * @return string|NULL
     */
    public function getName($name)
    {

        $dbs = $this->settings->importAndMergeSources(
            $this->container->get(SettingsInterface::CONFIG_IMPORTS_KEY),
            'db'
        )['databases'];

        if (!is_array($dbs)) {
            throw new Exception('Missing Database Settings');
        }
        if (isset($dbs[$name])) {
            return $name;
        }
        foreach ($dbs as $dbName => $db) {
            if (isset($db['alias']) && is_array($db['alias']) && in_array($name, $db['alias'])) {
                return $dbName;
            }
        }
        return null;
    }
}
