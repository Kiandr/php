<?php

use REW\Core\Interfaces\SourceInterface;

/**
 * Source is an abstract class used to handle source code
 * @package REW
 * @subpackage Source
 */
abstract class Source implements SourceInterface
{

    /**
     * External Link
     * @const string
     */
    const LINK = 'Source_Link';

    /**
     * Local File
     * @const string
     */
    const FILE = 'Source_File';

    /**
     * Inline Source
     * @const string
     */
    const CODE = 'Source_Code';

    /**
     * Source Type
     * @var Source_Type
     */
    protected $type;

    /**
     * Minified flag
     * @var bool
     */
    protected $minified;

    /**
     * Load options for scripts and stylesheets
     * Use the constants provided in Source_Type
     * Source_Type::LOAD["none"]
     * - fetch and execute the scripts synchronously during page load, pausing DOM content loading
     * Source_Type::LOAD["async"]
     * - fetch asynchronously while DOM content loads, once received it pauses DOM content loading to execute the script
     * Source_Type::LOAD["defer"]
     * - fetch asynchronously while DOM content loads, and execute the scripts synchronously after DOM content is loaded
     *
     * @var string
     */
    protected $load;

    /**
     * Create Source
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Get Type
     * @return Source_Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Is load option async
     * @return bool
     */
    public function isAsync()
    {
        return ($this->load == "async");
    }

    /**
     * Is load option defer
     * @return bool
     */
    public function isDefer()
    {
        return ($this->load == "defer");
    }

    /**
     * Set the script or stylesheet load option
     * @param string $load Use Source_Type::LOAD["none"] or Source_Type::LOAD["async"] or Source_Type::LOAD["defer"]
     * @see Source_Type::load
     */
    public function setLoadOption($load)
    {
        $this->load = $load;
    }

    /**
     * Get load option selected for Source
     * @return bool
     */
    public function getLoadOption()
    {
        return $this->load;
    }

    /**
     * Set minified flag
     * @param bool $minified
     */
    public function setMinified($minified)
    {
        $this->minified = (bool) $minified;
    }

    /**
     * If source minified
     * @return bool
     */
    public function isMinified()
    {
        return (bool) $this->minified;
    }

    /**
     * Load Source (from Link, File or Code)
     * @param string $type "Stylesheet" or "Javascript"
     * @param string $source
     * @param bool $critical
     * @param string $load
     * @return Source
     * @throws InvalidArgumentException If Invalid Type
     */
    public static function load($type, $source, $critical = false, $load = "none")
    {
        // External Link
        if (filter_var($source, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)
            || (substr($source, 0, 2) === '//' && filter_var('http:' . $source, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))) {
            $source = new Source_Link($source, $type, $load);
        // Local File
        } elseif (preg_match('#\.' . $type::$extension . '$#', $source)
            || preg_match('#\.less$#', $source)
            || (strpos($source, '//') === false && strpos($source, '/*') === false && strpos($source, '/') === 0 && is_file($source))
        ) {
            $source = new Source_File($source, $type, $load);
            if (!$source->checkExists()) {
                return null;
            }
        // Code
        } else {
            $source = new Source_Code($source, $type, $critical);
        }
        // Return Source
        return $source;
    }

    /**
     * Execute Source
     * @return string
     * @uses Source_Type::execute
     */
    public function execute()
    {
        $type = $this->type;
        return $type::execute($this);
    }
}
