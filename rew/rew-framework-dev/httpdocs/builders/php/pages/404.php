<?php

namespace BDX;

try {
    // 404 Header
    header('HTTP/1.1 404 NOT FOUND');
    
    if (Settings::getInstance()->FRAMEWORK) {
        $query = $app->db->query("SELECT `category_html` FROM `pages` WHERE `agent` = '1' AND BINARY `file_name` = '404';");
        $notFound = $query->fetch();
        echo $notFound['category_html'];
    } else {
        // @TODO - Setup a 404 for the standalone
    }
        
    $app->page_title = '404: Page Not Found';
    
    
// Error Occurred
} catch (Exception $e) {
    //Log::error($e);
}
