<?php

use REW\Core\Interfaces\SourceInterface;
use REW\Core\Interfaces\Source\TypeInterface;

/**
 * Source_Type is an abstract used for building and executing CSS and Javascript Source Code
 * @package REW
 * @subpackage Source
 */
abstract class Source_Type implements TypeInterface
{

    /**
     * @const string
     */
    const STYLESHEET = 'Source_Type_Stylesheet';

    /**
     * @const string
     */
    const JAVASCRIPT = 'Source_Type_Javascript';

    /**
     * @const string array
     * Source_Type::LOAD["none"]
     * - fetch and execute the scripts synchronously during page load, pausing DOM content loading
     * Source_Type::LOAD["async"]
     * - fetch asynchronously while DOM content loads, once received it pauses DOM content loading to execute the script
     * Source_Type::LOAD["defer"]
     * - fetch asynchronously while DOM content loads, and execute the scripts synchronously after DOM content is loaded
     * @desc load options
     */
    const LOAD = ["none" => "", "async" => "async", "defer" => "defer"];

    /**
     * Execute Source
     * @param SourceInterface $source
     * @return string Source Code
     */
    public static function execute(SourceInterface $source)
    {
        $self = get_called_class();
        $type = get_class($source);
        switch ($type) {
            case 'Source_Link':
                return $self::includeLink($source->getLink(), $source->getLoadOption());
                break;
            case 'Source_File':
                return $self::includeFile(str_replace($_SERVER['DOCUMENT_ROOT'], '', $source->getFile()), $source->getLoadOption());
                break;
            case 'Source_Code':
                return $self::includeCode($source->getData(), $source->isCritical(), $source->getLoadOption());
                break;
        }
    }
}
