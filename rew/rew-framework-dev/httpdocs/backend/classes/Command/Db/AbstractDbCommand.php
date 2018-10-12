<?php

namespace REW\Backend\Command\Db;

use REW\Backend\Command\AbstractCommand;
use REW\Core\Interfaces\Factories\DBFactoryInterface;
use \Container;
use \Exception;
use \PDO;

/**
 * @package REW\Backend\Command\Db
 */
abstract class AbstractDbCommand extends AbstractCommand
{

    /**
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @throws Exception
     * @return PDO
     */
    public function getDbConnection($hostname, $username, $password, $database)
    {
        try {
            $dsn = sprintf('mysql:host=%s;dbname=%s', $hostname, $database);
            return new PDO($dsn, $username, $password, [
                PDO::MYSQL_ATTR_INIT_COMMAND => sprintf(
                    "SET NAMES 'utf8', `time_zone` = '%s';",
                    @date_default_timezone_get()
                )
            ]);
        } catch (\PDOException $e) {
            throw new Exception(sprintf(
                'Fail to connect to database: %s',
                $e->getMessage()
            ));
        }
    }

    /**
     * @param string $name
     * @return array|NULL
     */
    public function getDbSettings($name = 'default')
    {
        $container = Container::getInstance();
        $dbFactory = $container->get(DBFactoryInterface::class);
        $dbSettings = $dbFactory->settings($name);
        return $dbSettings;
    }

    /**
     * @param PDO $db
     * @return array
     */
    public function getTableNames(PDO $db)
    {
        if ($result = $db->query('SHOW TABLES;')) {
            return $result->fetchAll(PDO::FETCH_COLUMN);
        }
        return [];
    }

    /**
     * @param PDO $db
     * @param array $tables
     */
    public function dropDbTables(PDO $db, array $tables)
    {
        $db->query('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $table) {
            $db->query(sprintf('DROP TABLE `%s`;', $table));
        }
        $db->query('SET FOREIGN_KEY_CHECKS=1;');
    }
}
