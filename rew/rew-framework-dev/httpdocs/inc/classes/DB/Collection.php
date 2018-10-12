<?php

use REW\Core\Interfaces\DB\CollectionInterface;

/**
 * DB_Collection
 *
 * @abstract
 */
class DB_Collection implements CollectionInterface
{

    /**
     * Database Connection
     * @var DB
     */
    protected $db;

    /**
     * Table Name
     * @var string
     */
    protected $table;

    /**
     * Setup DB_Collection
     *
     * @param DB $db
     * @param string $table
     * @return void
     */
    public function __construct(DB $db, $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    /**
     * Get Table Name
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get Row by ID
     *
     * @param int $id
     * @return array
     */
    public function getRow($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM `" . $this->table. "` WHERE `id` = ?;");
        $stmt->execute(array($id));
        return $stmt->fetch();
    }

    /**
     * Count Rows
     *
     * @param array $where
     * @return DB_Query
     */
    public function count($where = array())
    {

        // Extra Criteria
        $where = $this->buildWhere($where);

        // Generate SELECT Query
        $query = "SELECT COUNT(*) FROM `" . $this->table. "`" . $where['where'] . ";";

        // Prepar Query
        $stmt = $this->db->prepare($query);

        // Bind Criteria
        $stmt->bindValues($where['values']);

        // Execute Query
        $stmt->execute();

        // Return DB_Result
        return $stmt->fetchColumn();
    }

    /**
     * Search Rows
     *
     * @param array $where
     * @return DB_Query
     */
    public function search($where = array())
    {

        // Extra Criteria
        $where = $this->buildWhere($where);

        // Generate SELECT Query
        $query = "SELECT * FROM `" . $this->table. "`" . $where['where'] . $where['order'] . ";";

        // Prepar Query
        $stmt = $this->db->prepare($query);

        // Bind Criteria
        $stmt->bindValues($where['values']);

        // Execute Query
        $stmt->execute();

        // Return DB_Result
        return $stmt;
    }

    /**
     * Insert Row
     *
     * @param array $data
     * @return array
     */
    public function insert($data = array())
    {

        // Generate INSERT Query
        $query  = "INSERT INTO `" . $this->table. "` SET";
        foreach ($data as $col => $val) {
            // Raw SQL
            if (is_int($col)) {
                $query .= $val . ", ";
                unset($data[$col]);
            // Prepare Value
            } else {
                $query .= "`" . $col . "` = :" . $col . ", ";
            }
        }
        $query = rtrim($query, ', ') . ";";

        // Prepar Query
        $stmt = $this->db->prepare($query);

        // Bind Criteria
        $stmt->bindValues($data);

        // Execute Query
        $stmt->execute();

        // Return Array
        return $this->getRow($this->db->lastInsertId());
    }

    /**
     * Update Collection
     *
     * @param array $data     Row Data
     * @param array $where    SQL WHERE Criteria (Optional)
     * @return bool
     */
    public function update($data = array(), $where = array())
    {

        // Extra Criteria
        $where = $this->buildWhere($where);

        // Generate UPDATE Query
        $query  = "UPDATE `" . $this->table. "` SET ";
        foreach ($data as $col => $val) {
            // Raw SQL
            if (is_int($col)) {
                $query .= $val . ", ";
                unset($data[$col]);
            // Prepare Value
            } else {
                $query .= "`" . $col . "` = :" . $col . ", ";
            }
        }
        $query = rtrim($query, ', ') . $where['where'] . ";";

        // Prepar Query
        $stmt = $this->db->prepare($query);

        // Bind Data
        $stmt->bindValues($data);

        // Bind Criteria
        $stmt->bindValues($where['values']);

        // Execute Query
        return $stmt->execute();
    }

    /**
     * Delete Rows
     *
     * @param array $where
     * @return DB_Query
     */
    public function delete($where = array())
    {

        // Extra Criteria
        $where = $this->buildWhere($where);

        // Generate DELETE Query
        $query = "DELETE FROM `" . $this->table . "`" . $where['where'] . ";";

        // Prepare Query
        $stmt = $this->db->prepare($query);

        // Bind Criteria
        $stmt->bindValues($where['values']);

        // Execute Query
        $stmt->execute();

        // Return DB_Result
        return $stmt;
    }

    /**
     * Build WHERE String from Criteria
     * Match Types: $eq, $like, $gt, $gte, $lt, $lte
     *
     * @param array $where
     * @return string
     * @throws Exception
     */
    public function buildWhere($criteria)
    {
        $where = array();
        $values = array();
        foreach ($criteria as $match => $group) {
            // Match Type
            switch ($match) {
                // Equals
                case '$eq':
                    $match = "=";
                    break;
                // Not Equals
                case '$neq':
                    $match = "!=";
                    break;
                // Like
                case '$like':
                    $match = "LIKE";
                    break;
                // Greater Than
                case '$gt':
                    $match = ">";
                    break;
                // Greater Than or Equal To
                case '$gte':
                    $match = ">=";
                    break;
                // Less Than
                case '$lt':
                    $match = "<";
                    break;
                // Less Than or Equal To
                case '$lte':
                    $match = "<=";
                    break;
                // Special: ORDER BY
                case '$orderby':
                    $orderby = ' ORDER BY ' . $group;
                    unset($match);
                    break;
                // Error: Unknown Match
                default:
                    throw new Exception('Unknown Criteria Match: ' . $match);
                    break;
            }
            // Match Group
            if (!empty($match)) {
                foreach ($group as $key => $value) {
                    // NULL check
                    if (is_null($value) && in_array($match, array('=', '!='))) {
                        $where[] = "`$key` " . ($match == '=' ? "IS NULL" : "IS NOT NULL");
                    } else {
                        $values[$key] = $value;
                        $where[] = "`$key` " . $match . " :$key";
                    }
                }
            }
        }
        // Query Data
        return array(
            'where'  => !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '',
            'order'  => !empty($orderby) ? $orderby : null,
            'values' => $values
        );
    }
}
