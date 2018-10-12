<?php

// Get Subdomain being Edited
$subdomainFactory = Container::getInstance()->get(\REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class);
$subdomain = $subdomainFactory->buildSubdomainFromRequest('canManageBackup');
if (!$subdomain) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage backups.')
    );
}
$subdomain->validateSettings();

// Raise Memory Limit
ini_set('memory_limit', '100M');

// Root Directory
$root_dir = $_SERVER['DOCUMENT_ROOT'];

// Backup Files
if (isset($_GET['files'])) {
    // Close the Session
    session_write_close();

    // ZipArchiver::addDir
    class ZipArchiver extends ZipArchive
    {
        public function addDir($path, $dir = '')
        {
            if (is_dir($path)) {
                $dir = (!empty($dir) ? $dir . '/' : '') . basename($path);
                $this->addEmptyDir($dir);
                $nodes = glob($path . '/*');
                foreach ($nodes as $node) {
                    if (preg_match('#^\.(svn|ht*)#i', $node)) {
                        continue;
                    }
                    if (is_dir($node)) {
                        $this->addDir($node, $dir);
                    } else if (is_file($node)) {
                        $file = (!empty($dir) ? $dir . '/' : '') . basename($node);
                        $this->addFile($node, $file);
                    }
                }
            }
        }
        public function getErrorFromCode($code)
        {
            switch ($code) {
                case ZipArchive::ER_EXISTS:
                    return __('File already exists');
                    break;
                case ZipArchive::ER_INCONS:
                    return __('Zip archive inconsistent');
                    break;
                case ZipArchive::ER_MEMORY:
                    return __('Malloc failure');
                    break;
                case ZipArchive::ER_NOENT:
                    return __('No such file');
                    break;
                case ZipArchive::ER_NOZIP:
                    return __('Not a zip archive');
                    break;
                case ZipArchive::ER_OPEN:
                    return __('Can\'t open file');
                    break;
                case ZipArchive::ER_READ:
                    return __('Read error');
                    break;
                case ZipArchive::ER_SEEK:
                    return __('Seek error');
                    break;
                default:
                    return __('Unknown error');
                    break;
            }
        }
    }

    // Zip File
    $temppath = $root_dir . '/inc/cache/tmp/Backup_' . date('Y-m-d-Gi');
    $filename = $temppath . '.zip';

    $zip = new ZipArchiver();
    $open = $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    if ($open === true) {
        // Add CMS Uploads
        $temp = array();
        $files = DB::get('cms')->query("SELECT `id`, `name`, `data` FROM `cms_files`;");
        if (!empty($files)) {
            $zip->addEmptyDir('files');
            foreach ($files as $file) {
                $tempname = $file['id'] . '-' . $file['name'];
                if (file_put_contents($temppath . $tempname, $file['data'])) {
                    $temp[] = $temppath . $tempname;
                    $zip->addFile($temppath . $tempname, 'files/' . $tempname);
                }
            }
            unset($files, $file);
        }

        // Add Paths
        $zip->addDir($root_dir . '/uploads');
        $zip->addDir($root_dir . '/uploads');
        $zip->close();

        // Download Zip
        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename="Backup_' . date('Y-m-d-Gi') . '.zip"');

        // Turn off output buffering
        while (@ob_end_flush()) {
        }

        // Send Zip
        readfile($filename);

        // Delete Zip
        unlink($filename);
        foreach ($temp as $tmp) {
            unlink($tmp);
        }
    } else {
        // Error Occurred
        echo '<p>' . __('A problem was encountered creating zip archive. Please contact customer support. ') . '(' . $zip->getErrorFromCode($open) . ')</p>';
    }

    // All Done
    exit;
}

// Set a filename thats in the temporyary dir of the system and contains the domain name, the date, and a unique id
$filename = $root_dir . '/inc/cache/tmp/Database_Backup_' . date('Y-m-d-Gi') . '.sql';

// Database Settings
$db_settings = DB::settings('cms');
$db_host = $db_settings['hostname'];
$db_user = $db_settings['username'];
$db_pass = $db_settings['password'];
$db_name = $db_settings['database'];

// CMS Content
$tables = array(
    'default_info',
    'pages',
    'snippets',
);

// Blog Tables
if (!empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'])) {
    $tables = array_merge($tables, array(
        'blog_entries',
        'blog_comments',
        'blog_pings',
        'blog_categories',
        'blog_links',
        'blog_settings',
    ));
}

// Directory Tables
if (!empty(Settings::getInstance()->MODULES['REW_DIRECTORY'])) {
    $tables = array_merge($tables, array(
        'directory_listings',
        'directory_categories',
        'directory_settings',
    ));
}

// REW Add-Ons
if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) {
    $tables[] = 'featured_communities';
}
if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_LISTINGS'])) {
    $tables[] = 'featured_listings';
}
if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_OFFICES'])) {
    $tables[] = 'featured_offices';
}
if (!empty(Settings::getInstance()->MODULES['REW_REWRITE_MANAGER'])) {
    $tables[] = 'pages_rewrites';
}
if (!empty(Settings::getInstance()->MODULES['REW_TESTIMONIALS'])) {
    $tables[] = 'testimonials';
}

/**
 * use mysqldump to get the default_info, pages, and snippets tables.
 * Skip the optimized version so that each insert is on its own line.
 * use complete inserts so that the fieldnames are used
 * Quick: prevents table locking during large pulls
 * order by primary so that the snippets are in alpha order
 */
$exec = "mysqldump --skip-opt --complete-insert --quick --order-by-primary --default-character-set=utf8 --set-charset --host='$db_host' --password='$db_pass' -u '$db_user' '$db_name' " . implode(' ', $tables) . " > " . $filename;
exec($exec, $dump, $return);
if ($return != 0) {
    // Error Occurred
    echo '<p>' . __('A problem was encountered creating the temporary output file.  Please contact customer support. (%s)' , $return) . '</p>';
    if (Settings::isREW()) {
        var_dump($exec, $dump, $return);
    }
} else {
    // Download SQL
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="Database_Backup_' . date('Y-m-d-Gi') . '.sql"');

    // read the file a bit at a time to prevent memory issues
    $fp = fopen($filename, 'r');
    while (!feof($fp)) {
        echo fread($fp, 1024);
    }
    fclose($fp);
}

// be sure to delete the file
unlink($filename);
exit;
