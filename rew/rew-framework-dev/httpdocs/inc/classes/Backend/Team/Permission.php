<?php

/**
 * Backend_Team_Permission is an interface providing the methods used by Teams to access permissions
 * @package Backend_Team
 */
abstract class Backend_Team_Permission
{

    /**
     * Permission Key
     * @var string $key
     */
    protected $key;

    /**
     * Column Storing Permission Settings in the DB.  This Field must be set to use a class.
     * @var string $column
     */
    protected $column;

    /**
     * String giving shorthand description of the permission
     * @var string $title
     */
    protected $title = "";

    /**
     * Full description of Permission and Possible options
     * @var string $title
     */
    protected $description = "";

    /**
     * Full description for the primary agent owning this team
     * @var string $title
     */
    protected $primaryDescription = "";

    /**
     * Possible Values that can be assigned to permission.  An empty array allows any value
     * @var array $values
     */
    protected $values = [];

    /**
     * Permissions default value
     * @var string $default
     */
    protected $default = 0;

    /**
     * Permissions Display Priority
     * @var int $priority
     */
    protected $priority = 0;

    /**
     * Can Agents Edit this Field
     * @var bool $agent_editable
     */
    protected $agent_editable = false;

    /**
     * Get Permission Database Column Name
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * Get Permission Title
     * @return string
     */
    public function getTitle()
    {
        return $this->title ?: '';
    }

    /**
     * Get Description of what Permission Allows
     * @param bool $primary_agent
     * @return string
     */
    public function getDescription($primary_agent = false)
    {
        return $primary_agent==true
            ? ($this->description ?: '')
            : ($this->primaryDescription ?: '');
    }

    /**
     * Get Possible Permission Values
     * @return array
     */
    public function getValues()
    {
        $values = (isset($this->values) && is_array($this->values)) ? $this->values : [];
        return $values;
    }

    /**
     * Get the permissions default value
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Get the disply priority
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Get the permission keysum
     * @return int
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Check if non-owning agents can edit this field
     * @return int
     */
    public function isAgentEditable()
    {
        return $this->agent_editable ?: false;
    }

    /**
     * Check if the field should use its defaults
     * @return int
     */
    public function useDefaults()
    {
        return $this->use_default ?: true;
    }

    /**
     * Load all permissions a team can grant to an agent
     */
    public static function loadGrantedPermissions()
    {

        $permissions = [];
        foreach (glob(__DIR__.'/Permission/Granted/*.php') as $file) {
            $class = 'Backend_Team_Permission_Granted_' . basename($file, '.php');
            if (class_exists($class)) {
                $permission = new $class();
                if ($permission instanceof self) {
                    $permissions[$permission->getColumn()] = $permission;
                }
            }
        }
        return $permissions;
    }

    /**
     * Load all permissions an agent can grant to a team
     */
    public static function loadGrantingPermissions()
    {

        $permissions = [];
        foreach (glob(__DIR__.'/Permission/Granting/*.php') as $file) {
            $class = 'Backend_Team_Permission_Granting_' . basename($file, '.php');
            if (class_exists($class)) {
                $permission = new $class();
                if ($permission instanceof self) {
                    $permissions[$permission->getColumn()] = $permission;
                }
            }
        }
        return $permissions;
    }
}
