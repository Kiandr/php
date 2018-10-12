<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\ModuleInterface;

// Include IDX Configuration
include_once $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';

// Send as Plain Text
header("Content-Type: text/plain");

// Load Module
if (isset($_GET['module']) && !empty($_GET['module'])) {
    // Create Page
    $page = Container::getInstance()->get(PageInterface::class);

    // Module Options
    $options = false;
    if (isset($_GET['options']) && is_string($_GET['options'])) {
        $options = unserialize($_GET['options']);
    } elseif (isset($_GET['options']) && is_array($_GET['options'])) {
        $options = $_GET['options'];

        // Cast booleans
        $cast = function (&$array) use (&$cast) {
            foreach ($array as $k => $value) {
                if (is_string($value)) {
                    if ($value === 'true') {
                        $array[$k] = true;
                    } else if ($value === 'false') {
                        $array[$k] = false;
                    }
                } else if (is_array($value)) {
                    $cast($array[$k]);
                }
            }
        };
        $cast($options);
    }

    /* Set AJAX */
    $options['ajax'] = true;

    // Load Module
    $module = null;
    $container = Container::getInstance();
    if ($container->has($_GET['module'])) {
        // Load the already-built module, if possible. This is a minor efficiency improvement so that 2 modules don't
        // get created for external packages, but, it is also the only way that said external packages can access
        // options other than superglobals or having a separate controller for ajax and html.
        $module = $container->get($_GET['module']);
        if ($module instanceof ModuleInterface && $module->getId() == $_GET['module']) {
            foreach ($options as $key => $val) {
                $module->config($key, $val);
            }
        } else {
            $module = null;
        }
    }
    if (!$module) {
        $module = $container->make(ModuleInterface::class, ['id' => $_GET['module'], 'config' => $options]);
    }
    $module = $page->container('ajax')->module($module, $options);

    // Display Module
    echo $module->display();
}
