<?php

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;

/**
 * DB
 *
 */
class DB extends PDO implements DBInterface
{

    /**
     * DB_Collection Array
     * @var array[]
     */
    protected $collections = array();

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
     * @var LogInterface
     */
    private $log;

    /**
     * @var bool
     */
    private $loggingEnabled;

    /**
     * @var bool|null
     */
    private static $globalLoggingEnabled;

    /**
     * Create New DB
     *
     * @param string $host MySQL Hostname
     * @param string $user MySQL Username
     * @param string $pass MySQL Password
     * @param string $name MySQL Database
     * @param LogInterface $log
     * @see PDO::__construct
     */
    public function __construct($host = null, $user = null, $pass = null, $name = null, LogInterface $log = null)
    {
        if ($log === null) {
            $log = Container::getInstance()->get(LogInterface::class);
        }

        // Profile start
        $timer = Profile::timer()->stopwatch('<code>' . __METHOD__ . '</code>')->start();
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->name = $name;
        $this->log = $log;
        try {
            parent::__construct('mysql:dbname=' . $this->name .';host=' . $this->host, $this->user, $this->pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8', time_zone = '" . @date_default_timezone_get() . "';"));
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Throw Exceptions
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Default to Assoc. Array
            $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('DB_Query', array($this))); // Use DB_Query for Statements
            // Debug Log: Connected to Database
            $this->log->db(__CLASS__, 'Connected To: ' . $this->name . '@' . $this->host);
        } catch (PDOException $e) {
            $log->halt('Error connecting to database: ' . $e->getMessage(), 503);
        }
        // Profile end
        $timer->setDetails($user . '@' . $host . ' - ' . $name)->stop();

        $this->loggingEnabled = false;
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
     * @param PDO execute $params
     * @return array
     */
    public function fetchAll($query, array $params = null)
    {
        $result = $this->prepare($query);
        if ($result->execute($params)) {
            return $result->fetchAll();
        }
    }

    /**
     * Add profile timing to queries
     * @see PDO::query
     */
    public function query()
    {
        $arguments = func_get_args();
        $query = $arguments[0];
        $timer = null;
        if ($this->isLoggingEnabled()) {
            $timer = Profile::timer()->stopwatch('<code>' . __METHOD__ . '</code>')->start();
            $timer->setDetails('<code>' . (strlen($query) < 1024 * 6 ? htmlspecialchars($query) : '*Query Too Large*') . '</code>');
        }
        // Use Faster Argument Unpacking in PHP v5.6+ (also known as "splat", "scatter" or "spread" operator).
        // http://php.net/manual/functions.arguments.php#functions.variable-arg-list.new
        // https://wiki.php.net/rfc/argument_unpacking
        $result = parent::query(...$arguments);
        if ($timer) {
            $timer->stop();
        }
        return $result;
    }

    /**
     * Enables logging (to the profiler). This will reset the global logging flag and set our connection to true.
     */
    public function enableLogging()
    {
        static::$globalLoggingEnabled = null;
        $this->loggingEnabled = true;
    }

    /**
     * Disables logging (to the profiler)
     * @param bool $global Apply to all connections
     */
    public function disableLogging($global)
    {
        if ($global) {
            static::$globalLoggingEnabled = false;
            $this->loggingEnabled = false;
        } else {
            $this->loggingEnabled = false;
        }
    }

    /**
     * Checks if logging is enabled and returns true if so else false
     * @return bool
     */
    public function isLoggingEnabled()
    {
        return static::$globalLoggingEnabled === false ? false : $this->loggingEnabled;
    }

    /**
     * Load DB_Collection
     *
     * @param string $table Table Name
     * @return DB_Collection
     */
    public function getCollection($table)
    {
        $timer = Profile::timer()->stopwatch(__METHOD__)->start();
        if (empty($this->collections[$table])) {
            $this->collections[$table] = new DB_Collection($this, $table);
        }
        $timer->stop();
        return $this->collections[$table];
    }

    /**
     * Magic Collection Getter, Get Collection Easily $db->table is shortcut for $db->getCollection('table')
     *
     * @link http://php.net/language.oop5.overloading
     * @param string $name
     * @return mixed
     */
    public function &__get($name)
    {
        // Return DB PRoperty
        if (isset($this->$name)) {
            return $this->$name;
        }
        // Get DB Collection
        return $this->getCollection($name);
    }

    /**
     * Get the database name
     * @return string
     */
    public function getDatabase() {
        return $this->name;
    }

    /**
     * Get connection settings by name
     * @param string $name
     * @throws Exception
     * @return array|NULL
     * @deprecated This is part of DBFactory
     */
    public static function settings($name = 'default')
    {

        return Container::getInstance()->get(DBFactoryInterface::class)->settings($name);
    }

    /**
     * Load connection by name/alias
     * @param string $name
     * @throws Exception
     * @return DB|NULL
     * @deprecated This is part of DBFactory
     */
    public static function get($name = 'default')
    {

        return Container::getInstance()->get(DBFactoryInterface::class)->get($name);
    }

    /**
     * Get connection name
     * @param string $name
     * @throws Exception
     * @return string|NULL
     * @deprecated This is part of DBFactory
     */
    public static function getName($name)
    {

        return Container::getInstance()->get(DBFactoryInterface::class)->getName($name);
    }
}
