<?php

// Thumbnail Size
$this->config['thumbnails'] = isset($this->config['thumbnails']) ? $this->config['thumbnails'] : '556x383/f';
$this->config['loadTags'] = $this->config['loadTags'] ?: $this->fileTemplate === 'results.tpl.php';

// Load default "module.php"
$controller = $this->locateFile('module.php', __FILE__);
if (!empty($controller)) {
    require $controller;
}
