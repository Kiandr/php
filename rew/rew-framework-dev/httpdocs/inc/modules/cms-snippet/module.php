<?php

// Code Supplied
if (isset($this->config['code'])) {
    $code = $this->config['code'];

// Load Snippet by Name
} else if (isset($this->config['name'])) {
    // Load Snippet
    $code = '#' . htmlspecialchars($this->config['name']) . '#';
    $code = rew_snippet($code, true);
}

// Snippet Code
echo $code;
