<?php

// Gallery Images
$images = $this->config('images');
$images = is_array($images) ? $images : array();

// Gallery Links
$links = $this->config('links');
$links = is_array($links) ? $links : array();

// Show All Photos
$enlarge = $this->config('enlarge');
$enlarge = isset($enlarge) ? $enlarge : true;

// Title
$title = $this->config('title');
$title = isset($title) ? $title : '';
