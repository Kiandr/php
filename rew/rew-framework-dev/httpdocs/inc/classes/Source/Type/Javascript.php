<?php

/**
 * Source_Type_Javascript extends Source_Type and is used for working with Javascript Source Code
 * @package REW
 * @subpackage Source
 */
abstract class Source_Type_Javascript extends Source_Type
{

    /**
     * Source Type
     * @var string
     */
    public static $type = Source_Type::JAVASCRIPT;

    /**
     * Source Extension
     * @var string
     */
    public static $extension = 'js';

    /**
     * @inheritdoc
     */
    public static function includeLink($link, $load = "none")
    {
        return sprintf('<script src="%s" %s></script>', $link, self::LOAD[$load]);
    }

    /**
     * @inheritdoc
     */
    public static function includeFile($file, $load = "none")
    {
        return sprintf('<script src="%s" %s></script>', $file, self::LOAD[$load]);
    }

    /**
     * @inheritdoc
     */
    public static function includeCode($code, $critical = false, $load = "none")
    {
        return sprintf('<script %s>%s</script>', self::LOAD[$load], $code);
    }

    /**
     * @inheritdoc
     */
    public function minify($code, array $options = array())
    {

        // Do Not Minify JavaScript
        if (empty(Settings::getInstance()->SETTINGS['MINIFY_JS'])) {
            return str_replace(array('<script>', '</script>'), '', $code);
        }

        // Use JSMin
        if (class_exists('JSMin')) {
            return JSMin::minify($code);

        // Default Minify (Remove Comments)
        } else {
            return preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $code);
        }
    }
}
