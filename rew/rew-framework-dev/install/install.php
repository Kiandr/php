<?php

// Command Line Only
if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain');
    die('Un-authorized access');
}

// Error handler to throw exceptions
set_error_handler(function ($errno, $errstr = '', $errfile = '', $errline = '') {
    if (error_reporting() & $errno) {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    }
    return true;
});

// Script argument
global $argv;
unset($argv[0]);
$args = array();
if (!empty($argv)) {
    // JSON array of site settings
    $arg = array_pop($argv);
    $args = json_decode($arg, true);
    $error = json_last_error();
    if ($error !== JSON_ERROR_NONE) {
        die('Invalid script argument');
    }
}

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../boot/app.php';

// DB Connection
$db = DB::get();

// Auth Instance
$authuser = Auth::get();

// Database Settings
$db_settings = DB::settings('cms');

// CMS Information
$cms_username    = !empty($args['cms_username'])    ? $args['cms_username']    : 'admin';
$cms_password    = !empty($args['cms_password'])    ? $args['cms_password']    : 'h3eA3qP3';
$cms_agent_fname = !empty($args['cms_agent_fname']) ? $args['cms_agent_fname'] : 'Super';
$cms_agent_lname = !empty($args['cms_agent_lname']) ? $args['cms_agent_lname'] : 'Admin';
$cms_agent_email = !empty($args['cms_agent_email']) ? $args['cms_agent_email'] : 'notsetup@realestatewebmasters.com';

// Require CMS Username and Password
if (empty($cms_password) || empty($cms_username)) {
    die('CMS Username and Password were not supplied.');
}

// Update REW Snippets
$matches = glob(__DIR__ . '/_snippets/*.txt');
if (!empty($matches)) {
    echo PHP_EOL . 'Creating ' . count($matches) . ' snippets: ' . PHP_EOL;
    foreach ($matches as $snippet_file) {
        try {
            // Check Language Specific Snippet
            $locale = __DIR__ . '/_snippets/' . Settings::getInstance()->LANG . '/' . basename($snippet_file);
            if (is_file($locale)) {
                $snippet_file = $locale;
            }

            // Get Snippet Name
            $snippet_name = basename($snippet_file);
            $snippet_name = substr($snippet_name, 0, strpos($snippet_name, '.'));

            // Get Snippet Code
            $snippet_code = file_get_contents($snippet_file);

            // Add snippet to database
            $update = $db->prepare("UPDATE `snippets` SET `code` = :code WHERE `name` = :name;");
            $update->execute(array('code' => $snippet_code, 'name' => $snippet_name));
            echo "\t" . str_replace(__DIR__, '', $snippet_file) . PHP_EOL;

        // Database error
        } catch (PDOException $e) {
            echo PHP_EOL . "\t" . 'ERROR [' . $e->getCode() . ']: ' . $e->getMessage() . PHP_EOL;
        }
    }
}

try {
    // Output
    echo PHP_EOL . 'Updating Super Admin: ' . PHP_EOL;

    //Encrypting Password
    $encryptedPassword = $authuser->encryptPassword($cms_password);

    // Update Auth Record
    $update = $db->prepare("UPDATE `auth` SET "
        . "`username`   = :username, "
        . "`password`   = :password"
        . " WHERE `id` = 1"
    . ";")->execute(array(
        'username'      => $cms_username,
        'password'      => $encryptedPassword
    ));

    // Update Super Admin Account
    $update = $db->prepare("UPDATE `agents` SET "
        . "`first_name` = :first_name, "
        . "`last_name`  = :last_name, "
        . "`email`      = :email"
        . " WHERE `id` = 1"
    . ";")->execute(array(
        'first_name'    => $cms_agent_fname,
        'last_name'     => $cms_agent_lname,
        'email'         => $cms_agent_email
    ));

    // Output
    echo PHP_EOL . "\t" . 'Username: '           . $cms_username;
    echo PHP_EOL . "\t" . 'Password: '           . $cms_password;
    echo PHP_EOL . "\t" . 'Encrypted Password: ' . $encryptedPassword;
    echo PHP_EOL;

// Database error
} catch (PDOException $e) {
    echo PHP_EOL . "\t" . 'ERROR [' . $e->getCode() . ']: ' . $e->getMessage() . PHP_EOL;
}

// Paths
$paths = array();

// DEFAULT Snippets
$path = __DIR__ . DIRECTORY_SEPARATOR . Skin::$directory . DIRECTORY_SEPARATOR;
if (is_dir($path)) {
    $paths[] = $path;
}

// BREW Snippets
$skinClass = Skin::getClass(Settings::getInstance()->SKIN);
if (is_subclass_of($skinClass, 'Skin_BREW')) {
    $path = __DIR__ . DIRECTORY_SEPARATOR . Skin_BREW::$directory . DIRECTORY_SEPARATOR;
    if (is_dir($path)) {
        $paths[] = $path;
    }
}

// Skin Snippets
$path = __DIR__ . DIRECTORY_SEPARATOR . $skinClass::$directory . DIRECTORY_SEPARATOR;
if (is_dir($path)) {
    $paths[] = $path;
} else if (is_dir($skinClass::$directory)) {
    // Maybe the skin directory contains an absolute path -- try it
    $paths[] = $skinClass::$directory;
}

// Check Paths for Install Files
foreach ($paths as $path) {
    // Import Extra MySQL File for Skin
    $mysql = $path . 'extra.sql';
    if (file_exists($mysql)) {
        // Output
        echo PHP_EOL . 'Importing sql: ' . str_replace(__DIR__, '', $mysql) . PHP_EOL;

        // Execute extra.sql for Skin
        $output = array();
        $command = 'mysql ' . ' -u ' . $db_settings['username'] . ' -p'  . $db_settings['password'] . ' -h ' . $db_settings['hostname'] . ' ' . $db_settings['database'] . ' < ' . $mysql . ' 2>&1';
        exec($command, $output, $error);
        if ($error === 0) {
            echo PHP_EOL . "\t" . 'Success' . PHP_EOL;
        } else {
            echo PHP_EOL . "\t" . implode(PHP_EOL . "\t", $output) . PHP_EOL;
        }
    }

    // Additional configuration
    $extra = $path . 'extra.php';
    if (file_exists($extra)) {
        // Execute extra.php script
        echo PHP_EOL . 'Running script: ' . str_replace(__DIR__, '', $extra) . PHP_EOL;

        try {
            ob_start();
            include_once $extra;
            echo PHP_EOL . "\t" . implode(PHP_EOL . "\t", array_filter(explode(PHP_EOL, ob_get_clean()))) . PHP_EOL;
        } catch (Exception $e) {
            echo PHP_EOL . "\t" . 'ERROR[' . $e->getCode() . ']: ' . $e->getMessage() . PHP_EOL;
        }
    }

    // Update Skin Specific Snippets and Pages
    foreach (array('snippet' => $path . '_snippets/*.txt', 'page' => $path . '_pages/*.txt') as $type => $pattern) {
        $matches = glob($pattern);
        if (!empty($matches)) {
            echo PHP_EOL . 'Creating ' . count($matches) . ' ' . $type . 's: ' . PHP_EOL . PHP_EOL;
            foreach ($matches as $content_file) {
                // Check Language Specific Snippet
                $locale = $path . '_' . $type . 's/' . Settings::getInstance()->LANG . '/' . basename($content_file);
                if (is_file($locale)) {
                    $content_file = $locale;
                }

                // Get Snippet Name
                $snippet_name = basename($content_file);
                $snippet_name = substr($snippet_name, 0, strpos($snippet_name, '.'));

                // Get Snippet Code
                $snippet_code = file_get_contents($content_file);

                try {
                    // Update Snippet
                    if ($type == 'snippet') {
                        $update = $db->prepare("UPDATE `snippets` SET `code` = :code WHERE `name` = :name;");
                    } else if ($type == 'page') {
                        $update = $db->prepare("UPDATE `pages` SET `category_html` = :code WHERE `file_name` = :name;");
                    } else {
                        throw new Exception('Invalid type: ' . $type);
                    }
                    $update->execute(array('code' => $snippet_code, 'name' => $snippet_name));
                    echo "\t" . str_replace(__DIR__, '', $content_file) . PHP_EOL;

                    // Database error
                } catch (PDOException $e) {
                    echo PHP_EOL . "\t" . 'ERROR [' . $e->getCode() . ']: ' . $e->getMessage() . PHP_EOL;
                }
            }
        }
    }

    // Post Install Hook
    Hooks::hook(Hooks::HOOK_POST_CONTENT_INSTALL)->run($db);
}

//Force Saved Searches Responsive Template for new installations
try {
    // Output
    echo PHP_EOL . 'Forcing Saved Searches Responsive Template ' . PHP_EOL;

    // Update Auth Record
    $update = $db->prepare("UPDATE `rewidx_system` SET "
        . "`force_savedsearches_responsive`   = :force "
        . ";")->execute(array(
        'force'      => 'true'
    ));

} catch (PDOException $e) {
    echo PHP_EOL . "\t" . 'ERROR [' . $e->getCode() . ']: ' . $e->getMessage() . PHP_EOL;
}
