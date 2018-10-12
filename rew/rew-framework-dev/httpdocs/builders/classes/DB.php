<?php

namespace BDX;

/**
 * DB
 *
 */
class DB extends \PDO
{

    /**
     * Database Host
     * @var string
     */
    private $host;

    /**
     * Database Username
     * @var string
     */
    private $user;

    /**
     * Database Password
     * @var string
     */
    private $pass;

    /**
     * Database Name
     * @var string
     */
    private $name;

    /**
     * Create New DB
     *
     * @param string $host MySQL Hostname
     * @param string $user MySQL Username
     * @param string $pass MySQL Password
     * @param string $name MySQL Database
     * @return void
     * @see PDO::__construct
     */
    public function __construct($host = null, $user = null, $pass = null, $name = null)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->name = $name;
        try {
            parent::__construct('mysql:dbname=' . $this->name .';host=' . $this->host, $this->user, $this->pass, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8', time_zone = '" . @date_default_timezone_get() . "';"));
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); // Throw Exceptions
            $this->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC); // Default to Assoc. Array
        } catch (\PDOException $e) {
            //Log::halt('Error connecting to database: ' . $e->getMessage(), 503);
        }
    }

    /**
     * Fetch Row from Query
     *
     * @param string $query
     * @param array $params
     * @return DB_Query
     */
    public function fetch($query, array $params = null)
    {
        $result = $this->prepare($query);
        if ($result->execute($params)) {
            return $result->fetch();
        }
    }

    /**
     * Fetch Array from Query
     *
     * @param string $query
     * @return array
     */
    public function fetchAll($query)
    {
        $result = $this->prepare($query);
        if ($result->execute()) {
            return $result->fetchAll();
        }
    }
}
