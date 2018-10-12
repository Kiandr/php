<?php

use REW\Core\Interfaces\LogInterface;

/**
 * Database_MySQLImproved
 *
 */
class Database_MySQLImproved extends Database
{

    /**
     * Database Connections
     * @var array
     */
    static $connections = array();

    /**
     * Database Connection
     * @var mysqli
     */
    private $connection;

    /**
     * @var LogInterface
     */
    private $log;

    /**
     * Gets the current connection. If not connected yet, connects.
     *
     * @return mysqli
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connect();
        }

        return $this->connection;
    }

    /**
     * Initialize deferred database Connection
     *
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $database
     * @param LogInterface $log
     * @param bool $connect
     */
    public function __construct($host, $user, $pass, $database, $connect = false, LogInterface $log = null)
    {
        parent::__construct($host, $user, $pass, $database, $connect);

        if ($log === null) {
            $log = Container::getInstance()->get(LogInterface::class);
        }

        $this->log = $log;
    }

    /**
     * Establish Database Connection
     *
     * @return void
     */
    public function connect()
    {

        // Profile start
        $timer = Profile::timer()->stopwatch('<code>' . get_class($this) . '::' . __FUNCTION__ . '</code>')->start();

        // Check Active Connections (Only Connect Once)
        $key = $this->user . '@' . $this->db . '@' . $this->host;
        if (isset(self::$connections[$key])) {
            // Use Active Connection
            $this->connection = self::$connections[$key];
        } else {
            // Setup Connection
            $this->connection = mysqli_init();
            if (!$this->connection) {
                die($this->log->halt('mysqli_init failed', 503));
            }

            // Set Database Timeout
            if (!$this->connection->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10)) {
                die($this->log->halt('Setting Options Failed', 503));
            }

            // Connect to Database
            if (!$this->connection->real_connect($this->host, $this->user, $this->pass, $this->db)) {
                die($this->log->halt('Connect fatal error: could not connect to host - ' . mysqli_connect_error(), 503));
            }

            // UTF-8 Character Set
            if (!$this->connection->set_charset('utf8')) {
                die($this->log->halt('Error loading character set utf8 - ' . $this->connection->error, 503));
            }

            // Set connection Timezone
            $timezone = @date_default_timezone_get();
            if (!$this->connection->query("SET time_zone = '" . $timezone . "';")) {
                die($this->log->halt('Error setting timezone to ' . $timezone . ' - ' . $this->connection->error, 503));
            }

            // Debug Log: Connected to Database
            $this->log->db(__CLASS__, 'Connected To: ' . $this->db . '@' . $this->host);

            // Store Connection
            self::$connections[$key] = $this->connection;
        }

        // Profile end
        $timer->stop();
    }

    /**
     * Execute Database Query
     *
     * @param string $query
     * @param int $resultmode
     * @return mysqli_result
     */
    public function query($query, $resultmode = MYSQLI_STORE_RESULT)
    {
        // Save Last Query
        $this->last_query = $query;

        $timer = Profile::timer()->stopwatch('<code>' . __METHOD__ . '</code>')->setDetails('<code>' . htmlspecialchars($query) . '</code>')->start();
        $result = $this->getConnection()->query($query, $resultmode);
        $timer->stop();

        // Return Result
        return $result;
    }

    /**
     * Fetch Array from Database Result
     *
     * @param mysqli_result $result
     * @return array
     */
    public function fetchArray($result)
    {
        $return = false;
        if (is_a($result, 'mysqli_result')) {
            $return = $result->fetch_assoc();
        }
        return $return;
    }

    /**
     * Get Row from Database
     *
     * @param string $row      SQL SELECT
     * @param string $table    SQL Table
     * @param string $where    SQL WHERE
     * @return array
     */
    public function getRow($row, $table, $where = '')
    {

        // Build SQL Query
        $mySqlQuery = 'SELECT ' . $row . ' FROM ' . $table . (!empty($where) ? ' WHERE ' . $where : '');

        // Execute Query
        $mySqlRow = $this->query($mySqlQuery);
        if ($mySqlRow === false) {
            return false;
        }

        // Return Array
        return $mySqlRow->fetch_assoc();
    }

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
    public function getQuery($row, $table, $where = '', $order_by = '', $group_by = '', $limit = '', $having = '')
    {

        // Build SQL Query
        $where    = !empty($where)    ? ' WHERE ' . $where : '';
        $having   = !empty($having)   ? ' HAVING ' . $having : '';
        $group_by = !empty($group_by) ? ' GROUP BY `' . $group_by . '`' : '';
        $order_by = !empty($order_by) ? ' ORDER BY ' . $order_by . '' : '';
        $limit    = !empty($limit)    ? ' LIMIT ' . $limit : '';
        $mySqlQuery = 'SELECT ' . $row . ' FROM ' . $table . $where . $group_by . $having . $order_by . $limit;

        // Execute Query
        return $this->query($mySqlQuery);
    }

    /**
     * Get the number of results in a result set
     *
     * @param mysqli_result $result
     * @return int
     */
    public function getNumRows($result)
    {
        return (int) $result->num_rows;
    }

    /**
     * Get the number of results in a result set
     *
     * @param mysqli_result $result
     * @return int
     * @deprecated Use Database_MySQLImproved::getNumRows() instead.
     */
    public function num_rows($result)
    {
        return $this->getNumRows($result);
    }

    /**
     * Get the number of affects rows by the last query
     *
     * @return int
     */
    public function getAffectedRows()
    {
        if (!$this->connection) {
            return 0;
        }
        return $this->connection->affected_rows;
    }

    /**
     * Get the number of affects rows by the last query
     *
     * @return int
     * @deprecated Use Database_MySQLImproved::getAffectedRows() instead.
     */
    public function affected_rows()
    {
        return $this->getAffectedRows();
    }

    /**
     * Prepare / Clean String for Query
     *
     * @param string $input
     * @return string
     */
    public function cleanInput($input)
    {
        return $this->getConnection()->escape_string($input);
    }

    /**
     * Get Last Insert ID
     *
     * @return int
     */
    public function getInsertId()
    {
        return $this->getConnection()->insert_id;
    }

    /**
     * Get Last Insert ID
     *
     * @return int
     * @deprecated Use Database_MySQLImproved::getInsertId() instead.
     */
    public function insert_id()
    {
        return $this->getInsertId();
    }

    /**
     * Get Last DB Error
     *
     * @return null|string
     */
    public function error()
    {
        return $this->getConnection()->error;
    }

    /**
     * Close Database Connection
     *
     * @return void
     */
    public function close()
    {
        // Access directly so we don't open a connection if there isn't one open.
        if (!$this->connection) {
            return true;
        }

        return @$this->connection->close();
    }

    /**
     * Build Query String
     *
     * Builds a query string for the match type.
     *
     * If $sql_match_type is lessthaninterval or greaterthaninterval, the SQL will be formatted as `FieldName` (>=|<=) NOW() (+/-) INTERVAL ($mysql_values)
     * $mysql_values will be sanitized to remove non-alphanumeric/space characters.
     *
     * The sign defaults to minus but can be reversed by specifying + as the first character. For example, lessthaninterval "+1 day" will search NOW() + INTERVAL 1 day whereas
     * lessthaninterval "1 day" or "-1 day" will search NOW() - INTERVAL 1 day)
     *
     * If $sql_match_type is reduced, 2-3 fields are expected in the order: current,old,change date. Change date is optional. This is implemented on MRED.
     *
     * @param string|array $mysql_field
     * @param mixed  $mysql_values
     * @param string $sql_match_type
     * @param string $sql_conjunction
     * @return string
     */
    public function buildQueryString($mysql_field, $mysql_values, $sql_match_type, $sql_conjunction = 'AND')
    {

        // Process complete SQL statements
        switch ($sql_match_type) {
            case 'reduced':
                $sql_where = "(";
                $sql_where .= "`" . $mysql_field['current'] . "` < `" . $mysql_field['old'] . "`";
                $sql_conjunction = ') ' . $sql_conjunction;

                if (!empty($mysql_field['changed'])) {
                    $sql_where .= ' AND ';

                    // Only include listings changed in the last max_age days
                    $sql_match_type = 'morethaninterval';
                    $mysql_field = $mysql_field['changed'];
                } else {
                    $mysql_values = 'any';
                }
                break;
            default:
                if (is_array($mysql_field)) {
                    $mysql_field = reset($mysql_field);
                }
            // That is all.
        }

        // Build Query Sting from Match Type
        $sql_match = $this->matchString($sql_match_type);
        if (is_array($mysql_values)) {
            $sql_or_where = '';
            foreach ($mysql_values as $mysql_value) {
                if (is_null($mysql_value)) {
                    if (in_array($sql_match_type, array('equals', 'like', 'beginslike'))) {
                        $sql_or_where .= "`" . $mysql_field . "` IS NULL OR";
                    } else if (in_array($sql_match_type, array('notequals', 'notlike'))) {
                        $sql_or_where .= "`" . $mysql_field . "` IS NOT NULL AND";
                    }
                }
                if (!empty($mysql_value) && $mysql_value != 'any') {
                    $mysql_value = $this->cleanInput($mysql_value);
                    if ($sql_match_type == 'findinset') {
                        $sql_or_where .= sprintf($sql_match, $mysql_value, $mysql_field) . ' OR ';
                    } else {
                        if (substr($sql_match_type, -12) == 'thaninterval') {
                            $mysql_value = 'NOW() ' . ($mysql_value[0] == '+' ? '+' : '-') . ' INTERVAL ' . preg_replace('/[^0-9a-z ]/i', '', $mysql_value);
                        }
                        $sql_or_where .= sprintf($sql_match, $mysql_field, $mysql_value) . (in_array($sql_match_type, array('notequals', 'notlike')) ? ' AND ' : ' OR ');
                    }
                }
            }
            if (!empty($sql_or_where)) {
                $sql_where .= '(' . rtrim($sql_or_where, (in_array($sql_match_type, array('notequals', 'notlike')) ? ' AND ' : ' OR ')) . ')';
            }
        } else {
            if (!empty($mysql_values) && $mysql_values != 'any') {
                $mysql_values = $this->cleanInput($mysql_values);
                if ($sql_match_type == 'findinset') {
                    $sql_where .= sprintf($sql_match, $mysql_values, $mysql_field);
                } else {
                    if (substr($sql_match_type, -12) == 'thaninterval') {
                        $mysql_values = 'NOW() ' . ($mysql_values[0] == '+' ? '+' : '-') . ' INTERVAL ' . preg_replace('/[^0-9a-z ]/i', '', $mysql_values);
                    }
                    $sql_where .= sprintf($sql_match, $mysql_field, $mysql_values);
                }
            }
        }
        if (!empty($sql_where) && !empty($sql_conjunction)) {
            $sql_where .= ' ' . $sql_conjunction . ' ';
        }

        // Return SQL WHERE
        return $sql_where;
    }

    /**
     * Get the format string to use in the where for different conditions
     *
     * @param string $match_type
     * @return string
     */
    protected function matchString($match_type)
    {
        switch ($match_type) {
            case 'equals':
                $sql_match = "`%s` = '%s'";
                break;
            case 'notequals':
                $sql_match = "`%s` != '%s'";
                break;
            case 'morethan':
                $sql_match = "`%s` >= '%s'";
                break;
            case 'morethaninterval':
                $sql_match = "`%s` >= %s";
                break;
            case 'lessthan':
                $sql_match = "`%s` <= '%s'";
                break;
            case 'lessthaninterval':
                $sql_match = "`%s` <= %s";
                break;
            case 'like':
                $sql_match = "`%s` LIKE '%%%s%%'";
                break;
            case 'beginslike':
                $sql_match = "`%s` LIKE '%s%%'";
                break;
            case 'endslike':
                $sql_match = "`%s` LIKE '%%%s'";
                break;
            case 'notlike':
                $sql_match = "`%s` NOT LIKE '%%%s%%'";
                break;
            case 'findinset':
                $sql_match = "FIND_IN_SET('%s', REPLACE(`%s`, ', ', ','))";
                break;
            case 'replace':
                $sql_match = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`%s`, '.', ''), '/', ''), ')', ''), '(', ''), '  ', ' ') = UPPER('%s')";
                break;
        }
        return $sql_match;
    }
}
