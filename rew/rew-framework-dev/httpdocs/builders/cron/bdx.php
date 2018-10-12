<?php

namespace BDX;

// Restrict Access
if (isset($_SERVER['HTTP_HOST'])) {
    // Not Authorized
    die('Not Authorized');

// Set ENV Variables
} else {
    // Set HTTP Host & Document Root
    $_SERVER['HTTP_HOST']     = basename($_SERVER['HOME']);

    // Require Composer Vendor Auto loader
    require_once __DIR__ . '/../../../boot/app.php';

    if (!\Http_Host::isDev()) {
        $_SERVER['HTTP_HOST'] = 'www.' . $_SERVER['HTTP_HOST'];
        // Reset class caches
        \Http_Host::isDev(true);
    }
    $_SERVER['DOCUMENT_ROOT'] = $_SERVER['HOME'] . '/app/httpdocs';
}

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../../boot/app.php';

// Include BDX Settings
require_once __DIR__ . '/../classes/Settings.php';

// Start Time
$start = time();

// Turn on extra output
define('MAINT_DEBUG', true);
define('XML_FILE_LIMIT', 10);
define('FLUSH_LIMIT', 1000);

$num_listings = 0;

if (Settings::getInstance()->STATES) {
    if (MAINT_DEBUG) {
        echo PHP_EOL . 'BDX XML Generation: ' . PHP_EOL;
    }
    
    try {
        // Build XML File Name
        $xml_file_name = 'status_' . Settings::getInstance()->partnerID . '_' . date('Y-m-d') . '.xml.gz';
        
        // Get .gz file resource
        if ($gz = fopen(Settings::getInstance()->DIRS['BUILDER_XML'] . $xml_file_name, 'w9')) {
            $xmlWriter = new \XMLWriter();
            $xmlWriter->openMemory();
            $xmlWriter->startDocument('1.0', 'UTF-8');
            $xmlWriter->setIndent(4);
            $xmlWriter->startElement('listing-status');
                                
            // Listings Root
            $xmlWriter->startElement('listings');
            
            // BDX Database
            $db_settings = Settings::getInstance()->DATABASES['bdx'];
            $db_bdx = new DB($db_settings['hostname'], $db_settings['username'], $db_settings['password'], $db_settings['database']);
            
            // Listings Query
            $sql_where = "";
            if (is_array(Settings::getInstance()->STATES)) {
                $sql_where = " WHERE FIND_IN_SET(`ListingState`, " . $db_bdx->quote(implode(',', Settings::getInstance()->STATES)) . ")";
            }
            $query = $db_bdx->query("SELECT `SubdivisionID`, `ListingID` FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "`" . $sql_where);
            while ($listing_data = $query->fetch()) {
                // Build Listing URL
                $listing_url = Settings::getInstance()->SETTINGS['URL_BUILDERS'] . '/community/' . $listing_data['SubdivisionID'] . '/' . $listing_data['ListingID'] . '/';
    
                // Listing Root
                $xmlWriter->startElement('listing');
                
                    // Listing Attributes
                    $xmlWriter->writeElement('KeyID', $listing_data['ListingID']);
                    $xmlWriter->writeElement('Status', 'SUCCESS');
                    $xmlWriter->writeElement('URL', $listing_url);
                    $xmlWriter->writeElement('Message', '');
                    $xmlWriter->writeElement('Timestamp', '');
    
                    
                $num_listings++;
                        
                // End Listing
                $xmlWriter->endElement();
                
                if ($num_listings % FLUSH_LIMIT == 0) {
                    fwrite($gz, $xmlWriter->flush(true));
                }
            }
            
            // End Listings
            $xmlWriter->endElement();
            // End Listing Status
            $xmlWriter->endElement();
            
            // Output
            if (MAINT_DEBUG) {
                echo "The XML file was successfully saved." . PHP_EOL;
            }
            // Final flush to make sure we haven't missed anything
            fwrite($gz, $xmlWriter->flush(true));
        } else {
            // Output
            if (MAINT_DEBUG) {
                echo "An error occurred when opening the XML file." . PHP_EOL;
            }
        }
                    
    // Error Occurred
    } catch (Exception $e) {
        //Log::error($e);
    }
}

// Clean up Old XML files (Keep the past 10)
$xml_files = array_diff(scandir(Settings::getInstance()->DIRS['BUILDER_XML']), array('..', '.', '.gitignore'));

if (!empty($xml_files) && is_array($xml_files) && count($xml_files) > XML_FILE_LIMIT) {
    // Get first file name from directory as it is the oldest
    $xml_file_delete = array_shift($xml_files);
    
    // Delete file
    unlink(Settings::getInstance()->DIRS['BUILDER_XML'] . $xml_file_delete);
    
    // Output
    if (MAINT_DEBUG) {
        echo "Removed oldest XML file." . PHP_EOL;
    }
} else {
    // Output
    if (MAINT_DEBUG) {
        echo "10 or less files. Nothing to clean." . PHP_EOL;
    }
}
    
// Output
if (MAINT_DEBUG) {
    // Calculate Script Execution Time
    $runTime = time() - $start;
    $hours    = floor($runTime / 3600);
    $runTime -= ($hours * 3600);
    $minutes  = floor($runTime / 60);
    $runTime -= ($minutes * 60);
    $seconds  = $runTime;
    
    // Output
    echo PHP_EOL . PHP_EOL . 'Running time: ' . $hours . ' hrs, ' . $minutes . ' mins, ' . $seconds . ' secs.' . PHP_EOL;
}
