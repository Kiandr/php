<?php

// Prevent default thumbnail size from being set (in default module.php)
$this->config['thumbnails'] = isset($this->config['thumbnails']) ? $this->config['thumbnails'] : false;

// Configure community details
if (is_numeric($this->config['mode'])) {
    $this->config['thumbnails'] = false;
    $this->config['truncate'] = false;
    $this->config['loadTags'] = true;
    //$this->config['loadExtra'] = true; // Load extra statistics (property stats & $/sqft)
    //$this->config['loadResults'] = 12; // Display community's matching IDX search results
    //$this->config['searchUrl'] = true;
}

// Load default module controller
$controller = $this->locateFile(basename(__FILE__), __FILE__);
if (!empty($controller)) {
    require $controller;
}
