<?php

// Mode (Default: 'list')
$mode = !empty($this->config['mode']) ? $this->config['mode'] : 'list';

// Class (Default: False)
$class = !empty($this->config['class']) ? $this->config['class'] : false;

// Link (Default: False)
$link = !empty($this->config['link']) ? $this->config['link'] : false;

// Limit (Default: False)
$limit = !empty($this->config['limit']) ? $this->config['limit'] : false;

// Truncate (Default: 125)
$truncate = !empty($this->config['truncate']) && is_int($this->config['truncate']) ? $this->config['truncate'] : 125;

// Office Specified
$office = !empty($this->config['office']) ? $this->config['office'] : false;

// Office Thumbnail Size
$office_thumbnails = isset($this->config['office_thumbnails']) ? $this->config['office_thumbnails'] : '380x285/f';

// Office Placeholder Image
$office_placeholder = !empty($this->config['office_placeholder']) ? $this->config['office_placeholder'] : '/img/blank.gif';
$office_placeholder = !empty($office_thumbnails) ? '/thumbs/' . $office_thumbnails . $office_placeholder : $office_placeholder;

// Agent Thumbnail Size (defaults to same default as agents module)
$agent_thumbnails = isset($this->config['agent_thumbnails']) ? $this->config['agent_thumbnails'] : '275x275/r';

// Agent Placeholder Image
$agent_placeholder = !empty($this->config['agent_placeholder']) ? $this->config['agent_placeholder'] : '/img/blank.gif';
$agent_placeholder = !empty($agent_thumbnails) ? '/thumbs/' . $agent_thumbnails . $agent_placeholder : $agent_placeholder;

// CMS Database
$db = DB::get('cms');

// Module Mode
switch ($mode) {
    // Office Details
    case 'details':
        $this->fileTemplate = 'details.tpl.php'; // Force 'Details' TPL File
        $truncate = false;
        $limit = 1;
        break;

    // Office Spotlight
    case 'spotlight':
        $sql_where = "`display` = 'Y'";
        $sql_order = "RAND()";
        if (empty($limit)) {
            $limit = 1;
        }
        break;

    // Office List
    case 'list':
    default:
        $sql_where = "`display` = 'Y'";
        $sql_order = "`sort` ASC";
        break;
}

// Office
if (!empty($office)) {
    $sql_where = "`display` = 'Y' AND `id` = " . $db->quote($office);
    $limit = 1;
}

// SQL Limit
$sql_limit = !empty($limit) ? ' LIMIT ' . (int) $limit : '';

// SQL Where
$sql_where = !empty($sql_where) ? ' WHERE ' . $sql_where : '';

// SQL Order
$sql_order = !empty($sql_order) ? ' ORDER BY ' . $sql_order : '';

try {
    // Build Query
    $query = "SELECT `id`, `title`, `description`, `email`, `phone`, `fax`, `address`, `city`, `state`, `zip`, `image` FROM `featured_offices`" . $sql_where . $sql_order . $sql_limit . ";";

    // Select Offices
    $offices = $db->fetchAll($query);

    // Process Offices
    $offices = array_map(function ($office) use ($db, $mode, $truncate, $office_thumbnails) {

        // Office Location
        $office['location'] = implode(', ', array_filter(array($office['address'], $office['city'], $office['state'], $office['zip'])));

        // Obfuscate Email
        if (!empty($office['email'])) {
            $office['email'] = implode(array_map(function ($char) {
                return '&#' . ord($char);
            }, str_split($office['email'])));
        }

        // Office Photo
        $office['image'] = (!empty($office_thumbnails) ? '/thumbs/' . $office_thumbnails : '') . '/' . (!empty($office['image']) ? 'uploads/offices/' . $office['image'] : 'img/404.gif');

        // Truncate Description
        if (!empty($truncate)) {
            $office['description'] = Format::truncate($office['description'], $truncate, '&hellip;');
        }

        // New Lines
        $office['description'] = nl2br(trim($office['description'], "\r\n "));

        // Return Office
        return $office;
    }, $offices);

    // Office Details
    if ($mode == 'details') {
        // Single Office
        $office = array_pop($offices);

        // Agents in this Office
        $agents = $db->fetchAll("SELECT CONCAT(`first_name`, ' ', `last_name`) AS `name`, `email`, `image`, `remarks`, `title`, `office_phone`, `cell_phone`, `home_phone`, `fax`, `website`  FROM `agents` WHERE `office` = " . $db->quote($office['id']) . " AND `display` = 'Y' ORDER BY `last_name` ASC, `first_name` ASC;");
        if (!empty($agents)) {
            // Office Agents
            $office['agents'] = array_map(function ($agent) use ($agent_thumbnails) {

                // Agent Link
                $agent['link'] = Format::slugify($agent['name']);

                // Agent Photo
                $agent['image'] = (!empty($agent_thumbnails) ? '/thumbs/' . $agent_thumbnails : '') . '/' . (!empty($agent['image']) ? 'uploads/agents/' . $agent['image'] : 'img/404.gif');

                // Obfuscate Email
                $agent['email'] = implode(array_map(function ($char) {
                    return '&#' . ord($char);
                }, str_split($agent['email'])));

                // New Lines
                $agent['remarks'] = nl2br(trim($agent['remarks'], "\r\n "));

                // Return Agent
                return $agent;
            }, $agents);
        }
    }

// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}
