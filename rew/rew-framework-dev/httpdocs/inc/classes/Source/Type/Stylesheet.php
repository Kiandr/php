<?php

/**
 * Source_Type_Stylesheet extends Source_Type and is used for working with CSS Source Code
 * @package REW
 * @subpackage Source
 */
abstract class Source_Type_Stylesheet extends Source_Type
{

    /**
     * Source Type
     * @var string
     */
    public static $type = Source_Type::STYLESHEET;

    /**
     * Source Extension
     * @var string
     */
    public static $extension = 'css';

    /**
     * Include CSS Link
     * @inheritdoc
     */
    public static function includeLink($link, $async = false)
    {
        return sprintf('<link type="text/css" href="%s" rel="stylesheet">', $link);
    }

    /**
     * Include CSS File
     * @inheritdoc
     */
    public static function includeFile($file, $async = false)
    {
        return sprintf('<link type="text/css" href="%s" rel="stylesheet">', $file);
    }

    /**
     * Include CSS Code
     * @inheritdoc
     */
    public static function includeCode($code, $critical = false)
    {
        return sprintf('<style> %s </style>', $code);
    }

    /**
     * Minify Source Code
     * @see Source_Type::minify
     */
    public function minify($code, array $options = array())
    {
        // Compile LESS Code
        if (!empty($options['less'])) {
            // Setup Cache
            $cache = new Cache;

            // Save LESS File
            $cache->save('tmp/' . md5($code) . '.less', $code);

            // LESS File
            $less = $cache->getPath() . $cache->getName();

            $lessc = realpath($_SERVER['DOCUMENT_ROOT'] . '/../node_modules/less/bin/lessc');
            if (empty($lessc)) {
                throw new Exception('LESS compiler not found.');
            }

            // Parse LESS
            $command = $lessc . ' ' . $less . (!empty(Settings::getInstance()->SETTINGS['MINIFY_CSS']) ? ' --clean-css' : '') . ' 2>&1';
            exec($command, $output, $return_var);

            // Catch Error
            if ($return_var != 0) {
                // Error Message
                $error = implode(PHP_EOL, $output);
                $error = preg_replace('/\e\[\d+m/', '', $error);

                // Error Occurred
                Log::halt('<pre>' . $error . '</pre>');
            }

            // Compress CSS to Single Line
            if (!empty(Settings::getInstance()->SETTINGS['MINIFY_CSS'])) {
                return implode($output);

            // Un-Minified CSS
            } else {
                return implode(PHP_EOL, $output);
            }

        // Do not Minify
        } elseif (empty(Settings::getInstance()->SETTINGS['MINIFY_CSS'])) {
            return $code;

        // Use Minify_CSS
        } else if (class_exists('Minify_CSS')) {
            return Minify_CSS::minify($code);

        // Default Minify (Remove Comments, Compact to Single Line)
        } else {
            return preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', str_replace(array("\r\n", "\r", "\n", "\t"), '', $code));
        }
    }
}
