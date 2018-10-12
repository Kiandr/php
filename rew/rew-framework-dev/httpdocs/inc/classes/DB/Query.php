<?php

use REW\Core\Interfaces\DB\QueryInterface;

/**
 * DB_Query
 *
 */
class DB_Query extends PDOStatement implements QueryInterface
{

    /**
     * DB Connection
     * @var DB
     */
    protected $dbh;

    /**
     * Create DB_Query
     *
     * @param DB $dbh
     */
    protected function __construct(DB $dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * When execute is called record the time it takes and then log the query
     *
     * @param parameters array[optional]
     * @return PDO result set
     * @uses Log::db
     * @throws PDOException
     */
    public function execute($parameters = null)
    {
        $timer = null;
        if ($this->dbh->isLoggingEnabled()) {
            $timer = Profile::timer()->stopwatch('<code>' . __METHOD__ . '</code>')->start();
            $query = strlen($this->queryString) < 1024 * 6 ? $this->queryString : '*Query Too Large*';
            $timer->setDetails('<code>' . htmlspecialchars($this->_getSQL($query, $parameters)) . '</code>');
        }
        try {
            $result = parent::execute($parameters);
        } catch (PDOException $e) {
            throw $e;
        }

        if ($timer) {
            $timer->stop();
        }
        return $result;
    }

    /**
     * Bind Values to Query
     *
     * @param DB_Query $stmt
     * @param array $array
     * @param bool $typeArray
     * @return void
     */
    public function bindValues($values, $typeArray = false)
    {
        foreach ($values as $key => $value) {
            if ($typeArray) {
                $this->bindValue(':' . key, $value, $typeArray[$key]);
            } else {
                if (is_int($value)) {
                    $param = PDO::PARAM_INT;
                } elseif (is_bool($value)) {
                    $param = PDO::PARAM_BOOL;
                } elseif (is_null($value)) {
                    $param = PDO::PARAM_NULL;
                } elseif (is_string($value)) {
                    $param = PDO::PARAM_STR;
                } else {
                    $param = false;
                }
                if (isset($param)) {
                    $this->bindValue(":$key", $value, $param);
                }
            }
        }
    }

    /**
     * Get raw SQL query using parameters
     *
     * @param string $preparedQuery
     * @param array $queryParams
     * @return string
     */
    private function _getSQL($preparedQuery, $queryParams)
    {
        $queryString = $preparedQuery;
        if (!$queryParams) {
            return $queryString;
        }
        foreach ($queryParams as $key => $value) {
            $value = $this->dbh->quote($value);
            if (is_numeric($key)) {
                $queryString = preg_replace('/\?/', $value, $queryString, 1);
            } else {
                $queryString = preg_replace('/:' . $key . '(?![a-zA-Z0-9_])/', $value, $queryString);
            }
        }
        return $queryString;
    }
}
