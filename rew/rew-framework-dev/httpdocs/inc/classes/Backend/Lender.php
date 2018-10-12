<?php

/**
 * Backend_Lender is a class used for interacting with Lenders
 *
 * @package Backend
 */
class Backend_Lender implements ArrayAccess
{

    /**
     * Lender ID
     * @var int $id
     */
    protected $id;

    /**
     * Lender Row
     * @var array $row
     */
    protected $row = array();

    /**
     * Create Lender from Row
     *
     * <code>
     * <?php
     *
     * // Create Lender from $row
     * $lender = new Backend_Lender ($row);
     *
     * // Display First Name
     * echo $lender->info('first_name'); // $row['first_name']
     *
     * ?>
     * </code>
     *
     * @param array $row Data Row
     */
    public function __construct($row = array())
    {

        // Lender ID
        if (isset($row['id'])) {
            $this->id = $row['id'];
        }

        // Lender Row
        $this->row = $row;
    }

    /**
     * Load Lender by ID
     *
     * <code>
     * <?php
     *
     * // Load Lender from ID
     * $lender = Backend_Lender::load(1);
     *
     * // Get Lender ID (1)
     * $lender->getId();
     *
     * ?>
     * </code>
     *
     * @param int $id Lender ID
     * @return Backend_Lender|null
     * @throws PDOException
     */
    public static function load($id)
    {

        // Load Lender Row
        $row = self::findById($id);

        // Lender not Found
        if (empty($row)) {
            return null;
        }

        // Return Backend_Lender
        return new self ($row);
    }

    /**
     * Load Lender by ID
     *
     * @param int $id Lender ID
     * @return array DB Row
     * @throws PDOException
     */
    public static function findById($id)
    {

        // App DB
        $db = DB::get('users');

        // Find Agent by ID
        $stmt = $db->prepare("SELECT `l`.*, `auth`.`username`, `auth`.`password`"
        . " FROM `lenders` `l` JOIN `" . Auth::$table . "` `auth` ON `l`.`auth` = `auth`.`id`"
        . " WHERE `l`.`id` = :id"
        . ";");

        // Execute Query
        $stmt->execute(array(
            'id' => $id
        ));

        // Return Row
        return $stmt->fetch();
    }

    /**
     * Assign Lead(s) to Lender
     * @param array|Backend_Lead $leads Collection of Leads or Single Lead
     * @param Auth $authuser Authorized User
     * @param array $errors If present, error report will be appended to collection
     * @throws InvalidArgumentException If $leads Param is Invalid
     * @return array Collection of Assigned Leads
     */
    public function assign($leads, $authuser = null, &$errors = array())
    {

        // Check Arguments
        if ($leads instanceof Backend_Lead) {
            $leads = array($leads);
        }
        if (!is_array($leads)) {
            throw new InvalidArgumentException;
        }

        // Assigned Leads
        $assigned = array();
        foreach ($leads as $lead) {
            // Require Backend_Lead
            if (!$lead instanceof Backend_Lead) {
                continue;
            }

            // Skip If Already Assigned to Lender
            if ($lead->info('lender') == $this->getId()) {
                continue;
            }

            try {
                // Assign Lead to Lender
                $lead->assign($this, $authuser);

                // Add to Collection
                $assigned[] = $lead;

            // Database Error
            } catch (PDOException $e) {
                $errors[] = $lead->getName() . ' could not be assigned to ' . $this->getName() . '.';
                Log::error($e);
            }
        }

        // Send "New Leads" Notification
        $this->notifyLender($assigned, $authuser);

        // Return Leads
        return $assigned;
    }

    /**
     * Notify Lender that they have been assigend new Leads
     * @param array $assigned
     * @param Auth $authuser
     * @return void
     */
    public function notifyLender($assigned, $authuser = null)
    {

        // Send Notification Email to Lender
        if (!empty($assigned) && (empty($authuser) || !$authuser->isLender())) {
            // Setup Notification Mailer
            $mailer = new Backend_Mailer_LenderAssigned(array(
                'leads' => $assigned
            ));

            // Send to Lender
            $mailer->setRecipient($this->getEmail(), $this->getName());

            // Send Email
            $mailer->Send();
        }
    }

    /**
     * Get ID
     *
     * @return int Lender ID
     */
    public function getId()
    {
        return intval($this->id);
    }

    /**
     * Get Lender Name
     * @return string
     */
    public function getName()
    {
        return $this->info('first_name') . ' ' . $this->info('last_name');
    }

    /**
     * Get Lender Email
     * @return string
     */
    public function getEmail()
    {
        return $this->info('email');
    }

    /**
     * Get Row
     *
     * @return array Lender Row
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
