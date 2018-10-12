<?php

/**
 * Backend_Associate is a class used for interacting with Associates
 *
 * @package Backend
 */
class Backend_Associate implements ArrayAccess
{

    /**
     * Associate ID
     * @var int $id
     */
    protected $id;

    /**
     * Associate Row
     * @var array $row
     */
    protected $row = array();

    /**
     * Create Associate from Row
     *
     * <code>
     * <?php
     *
     * // Create Associate from $row
     * $associate = new Backend_Associate ($row);
     *
     * // Display First Name
     * echo $associate->info('first_name'); // $row['first_name']
     *
     * ?>
     * </code>
     *
     * @param array $row Data Row
     */
    public function __construct($row = array())
    {

        // Associate ID
        if (isset($row['id'])) {
            $this->id = $row['id'];
        }

        // Associate Row
        $this->row = $row;
    }

    /**
     * Load Associate by ID
     *
     * <code>
     * <?php
     *
     * // Load Associate from ID
     * $associate = Backend_Associate::load(1);
     *
     * // Get Associate ID (1)
     * $associate->getId();
     *
     * ?>
     * </code>
     *
     * @param int $id Associate ID
     * @return Backend_Associate|null
     * @throws PDOException
     */
    public static function load($id)
    {

        // Load Associate Row
        $row = self::findById($id);

        // Associate Not Found
        if (empty($row)) {
            return null;
        }

        // Return Backend_Associate
        return new self ($row);
    }

    /**
     * Load Associate by ID
     *
     * @param int $id Associate ID
     * @return array DB Row
     * @throws PDOException
     */
    public static function findById($id)
    {

        // App DB
        $db = DB::get('users');

        // Find Agent by ID
        $stmt = $db->prepare("SELECT `a`.*, `auth`.`username`, `auth`.`password`, `auth`.`last_logon`"
        . " FROM `associates` `a` JOIN `" . Auth::$table . "` `auth` ON `a`.`auth` = `auth`.`id`"
        . " WHERE `a`.`id` = :id"
        . ";");

        // Execute Query
        $stmt->execute(array(
            'id' => $id
        ));

        // Return Row
        return $stmt->fetch();
    }

    /**
     * Get ID
     *
     * @return int Associate ID
     */
    public function getId()
    {
        return intval($this->id);
    }

    /**
     * Get Associate Name
     * @return string
     */
    public function getName()
    {
        return $this->info('first_name') . ' ' . $this->info('last_name');
    }

    /**
     * Get Associate Email
     * @return string
     */
    public function getEmail()
    {
        return $this->info('email');
    }

    /**
     * Get Row
     *
     * @return array Associate Row
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Get / Set Info
     *
     * @param string $info
     * @param mixed $value
     * @return void|mixed
     */
    public function info($info, $value = null)
    {
        // Get Information
        if (is_string($info) && is_null($value)) {
            return $this->row[$info];
        }
        // Set Information
        if (is_string($info) && !is_null($value)) {
            $this->row[$info] = $value;
        }
    }

    /************************* ArrayAccess *************************/

    /**
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($index)
    {
        return isset($this->row[$index]);
    }

    /**
     * @see ArrayAccess::offsetGet()
     */
    public function &offsetGet($index)
    {
        if ($this->offsetExists($index)) {
            return $this->row[$index];
        }
        return false;
    }

    /**
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($index, $value)
    {
        if ($index) {
            $this->row[$index] = $value;
        } else {
            $this->row[] = $value;
        }
        return true;
    }

    /**
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($index)
    {
        unset($this->row[$index]);
        return true;
    }
}
