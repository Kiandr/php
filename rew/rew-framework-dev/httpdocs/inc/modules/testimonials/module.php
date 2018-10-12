<?php

// Get CMS Database
$db = DB::get('cms');

// Title (Default: False)
$title = !empty($this->config['title']) ? $this->config['title'] : false;

// Link (Default: False)
$link = !empty($this->config['link']) ? $this->config['link'] : false;

// Class Name (Default: 'module testimonials')
$class = !empty($this->config['class']) ? $this->config['class'] : 'module testimonials';

// HTML (Default: True)
$html = isset($this->config['html']) && empty($this->config['html']) ? false : true;

// Truncate (Default: False)
$truncate = !empty($this->config['truncate']) && is_int($this->config['truncate']) ? $this->config['truncate'] : false;

// Limit (Default: False)
$limit = isset($this->config['limit']) && !empty($this->config['limit']) ? ' LIMIT ' . (int) $this->config['limit'] : false;

// Show client name (Default: True)
$client = isset($this->config['client']) ? $this->config['client'] : true;

// Only select fields we need
$select_fields = !empty($this->config['select_fields']) ? $this->config['select_fields'] : array('client', 'testimonial');

if (is_array($select_fields)) {
    $select_fields = "`" . implode("`, `", $select_fields) . "`";
}

// Get Testimonials
$testimonials = $db->fetchAll('SELECT ' . $select_fields . ' FROM `testimonials` ORDER BY RAND()' . (!empty($limit) ? $limit : '') . ';');

// Parse Testimonials
$testimonials = array_map(function ($testimonial) use ($html, $truncate, $client) {
        
    // Hide client from being displayed
    if (empty($client)) {
        unset($testimonial['client']);
    }

    // Strip HTML
    if (empty($html)) {
        $testimonial['testimonial'] = Format::stripTags($testimonial['testimonial']);
    }

    // Truncate
    if (!empty($truncate)) {
        $testimonial['testimonial'] = Format::truncate($testimonial['testimonial'], $truncate, '&hellip;', false, true);
    }

    // New Lines
    if (empty($html)) {
        $testimonial['testimonial'] = preg_replace('/(\r\n)+|(\n|\r)+/', '<br>', trim($testimonial['testimonial'], "\r\n "));
    }

    // Return Testimonial
    return $testimonial;
}, $testimonials);
