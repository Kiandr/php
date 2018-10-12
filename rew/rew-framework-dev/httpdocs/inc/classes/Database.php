<?php

use REW\Core\Interfaces\DatabaseInterface;

/**
 * Database
 *
 */
abstract class Database implements DatabaseInterface
{

    /**
     * Database Server Host
     * @var string
     */
    protected $host = null;

    /**
     * Database User
     * @var string
     */
    protected $user = null;

    /**
     * Database Password
     * @var string
     */
    protected $pass = null;

    /**
     * Database Name
     * @var string
     */
    protected $db = null;

    /**
     * Last Ran Query
     * @var string
     */
    protected $last_query = null;

    /**
     * Create Database Connection
     *
     * @param string $host        Database Server
     * @param string $user        Database Username
     * @param string $pass        Database Password
     * @param string $database    Database Name
     * @param bool   $connect     Should we connect immediately or defer?
     */
    public function __construct($host, $user, $pass, $database, $connect = true)
    {
        // Profile start
        $timer = Profile::timer()->stopwatch('<code>' . get_class($this) . '::' . __FUNCTION__ . '</code>')->start();
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->db   = $database;

        if ($connect) {
            $this->connect();
        }

        // Profile end
        $timer->setDetails($user . '@' . $host . ' - ' . $database)->stop();
    }

    /**
     * Get Database Name
     *
     * @return string
     */
    public function db()
    {
        return $this->db;
    }

    /**
     * Get Last Ran Query
     *
     * @return string
     */
    public function getLastQuery()
    {
        return $this->last_query;
    }

    /**
     * Fetch Array from Query
     *
     * @param string $query    Database Query to Execute
     * @return false|array
     */
    public function fetchQuery($query)
    {
        $result = $this->query($query);
        return $this->fetchArray($result);
    }

    /**
     * Establish Database Connection
     *
     * @return void
     */
    abstract public function connect();

    /**
     * Execute Database Query
     *
     * @param string $query
     * @return mysqli_result
     */
    abstract public function query($query);

    /**
     * Fetch Array from Database Result
     *
     * @param mysqli_result $result
     * @return false|array
     */
    abstract public function fetchArray($result);

    /**
     * Get Row from Database
     *
     * @param string $row      SQL SELECT
     * @param string $table    SQL Table
     * @param string $where    SQL WHERE
     * @return array
     */
    abstract public function getRow($row, $table, $where = '');

    /**
     * Build & Perform Query
     *
     * @param string $row        SQL SELECT
     * @param string $table      SQL Table
     * @param string $where      SQL WHERE
     * @param string $order_by   SQL ORDER BY
     * @param string $group_by   SQL GROUP BY
     * @param string $limit      SQL LIMIT
     * @param string $having     SQL HAVING
     * @return mysqli_result
     */
    abstract public function getQuery($row, $table, $where = '', $order_by = '', $group_by = '', $limit = '', $having = '');

    /**
     * Get the number of results in a result set
     *
     * @param mysqli_result $result
     * @return int
     */
    abstract public function num_rows($result);

    /**
     * Get the number of affects rows by the last query
     *
     * @return int
     */
    abstract public function affected_rows();

    /**
     * Prepare / Clean String for Query
     *
     * @param string $input
     * @return string
     */
    abstract public function cleanInput($input);

    /**
     * Get Last Insert ID
     *
     * @return int
     */
    abstract public function insert_id();

    /**
     * Get Last DB Error
     *
     * @return null|string
     */
    abstract public function error();

    /**
     * Close Database Connection
     *
     * @return void
     */
    abstract public function close();

    /**
     * Build Query String
     *
     * @param string $field
     * @param mixed  $values
     * @param string $match
     * @return string
     */
    abstract public function buildQueryString($field, $values, $match);

    /**
     * Get Formatted String to use for Matching in buildQueryString
     *
     * @param string $match
     * @return string
     */
    abstract protected function matchString($match);
}
