<?php

// Page Instance
$page = $this->getContainer()->getPage();

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

// Agent Specified
$agent = !empty($this->config['agent']) ? $this->config['agent'] : false;

// Include alpha bar
$show_letters = isset($this->config['letters']) ? (bool) $this->config['letters'] : true;

// Thumbnail Size
$thumbnails = isset($this->config['thumbnails']) ? $this->config['thumbnails'] : '275x275/r';

// Placeholder Image
$placeholder = !empty($this->config['placeholder']) ? $this->config['placeholder'] : '/img/blank.gif';
$placeholder = !empty($thumbnails) ? '/thumbs/' . $thumbnails . $placeholder : $placeholder;

// Agent testimonials (Default: 0)
$testimonials = is_int($this->config['testimonials']) ? (int) $this->config['testimonials'] : (bool) $this->config['testimonials'];

// Config: Exclude agents with id
$exclude_agents = !empty($this->config['exclude']) ? $this->config['exclude'] : false;
$exclude_agents = is_array($exclude_agents) ? $exclude_agents : [$exclude_agents];
$exclude_agents = array_filter($exclude_agents, 'is_numeric');

/** @var int|bool $agent_listings Include agent's IDX listings*/
$agent_listings = isset($this->config['listings']) ? $this->config['listings'] : true;

// 404 Image
$image_404 = 'img/404.gif';

// CMS Database
$db = DB::get('cms');

// SQL WHERE
$sql_where = array();
$sql_params = array();

// Search by Agent Name
if (!empty($_GET['search_aname'])) {
    $search_aname = Format::trim(explode(',', $_GET['search_aname']));
    $sql_or_where = array();
    foreach ($search_aname as $search_name) {
        $sql_or_where[] = "(CONCAT(`first_name`, ' ', `last_name`) LIKE ?)";
        $sql_params[] = '%' . $search_name . '%';
    }
    $sql_where[] = '(' . implode(' OR ', $sql_or_where) . ')';
}

// Search by First Name
if (!empty($_POST['search_fname'])) {
    $sql_where[] = "`first_name` LIKE " . $db->quote("%" . $_POST['search_fname'] . "%");
}

// Search by Last Name
if (!empty($_POST['search_lname'])) {
    $sql_where[] = "`last_name` LIKE " . $db->quote("%" . $_POST['search_lname'] . "%");
}

// Only select fields we need
$select_fields = '`id`, `office`, `first_name`, `last_name`, `email`, `image`, `remarks`, `title`, `cell_phone`, `office_phone`, `home_phone`, `fax`, `website`, `agent_id`, `cms`, `cms_link`, `display`';
$select_fields = !empty($this->config['select_fields']) ? $this->config['select_fields'] : $select_fields;

if (is_array($select_fields)) {
    $select_fields = "`" . implode("`, `", $select_fields) . "`";
}

// Exclude agents from list
if (is_array($exclude_agents) && !empty($exclude_agents)) {
    $sql_where[] = sprintf('`id` NOT IN (%s)', implode(', ', array_fill(0, count($exclude_agents), '?')));
    $sql_params += array_values($exclude_agents);
}

$add_og_images = false;

// Module Mode
switch ($mode) {
    // Agent Details
    case 'details':
        $this->fileTemplate = 'details.tpl.php'; // Force 'Details' TPL File
        $this->fileJavascript = 'details.js.php';
        $truncate = false;
        $add_og_images = true;
        $limit = 1;
        break;

    // Agent Spotlight
    case 'spotlight':
        $sql_where[] = "`display` = 'Y' AND `display_feature` = 'Y'";
        $sql_order = "RAND()";
        if (empty($limit)) {
            $limit = 1;
        }
        break;

    // Featured Agents
    case 'featured':
        $sql_where[] = "`display_feature` = 'Y'";
        $sql_order = "RAND()";
        break;

    // Agent List
    case 'list':
    default:
        // Force 'List' TPL File
        $this->fileTemplate = 'list.tpl.php';
        // Only Show Certain Agents
        $sql_where[] = "`display` = 'Y'";
        $add_og_images = true;

        // Filter by letter
        $letters = array();
        if (!empty($show_letters)) {
            // Letter filters
            $result = $db->query("SELECT SUBSTR(`last_name`, 1, 1) AS `letter` FROM `agents` WHERE `display` = 'Y' GROUP BY `letter` ORDER BY `last_name` ASC;");
            while ($letter = $result->fetch()) {
                if (!empty($letter['letter'])) {
                    $letters[] = strtoupper($letter['letter']);
                }
            }

            // Filter by Letter
            if (!empty($_GET['letter'])) {
                // Check Available Letters
                if (in_array($_GET['letter'], $letters)) {
                    $sql_where[] = "`last_name` LIKE " . $db->quote($_GET['letter'] . '%');
                } else {
                    // 302 Redirect
                    header('Location: ' . Http_Uri::getUri(), 302);
                    exit;
                }
            }
        }

        // Filter by Office
        if (!empty($_GET['office'])) {
            $query = $db->prepare("SELECT `id`, `title` FROM `featured_offices` WHERE `id` = :id LIMIT 1;");
            $query->execute(array('id' => $_GET['office']));
            $office = $query->fetch();
            if (!empty($office)) {
                $sql_where[] = "`office` = ?";
                $sql_params[] = $office['id'];
            }
        }
        // Order By Last Name, First Name
        $sql_order = "`last_name` ASC, `first_name` ASC";
        break;
}

// Agent
if (!empty($agent)) {
    $limit = 1;
    if (is_int($agent)) {
        $sql_where[] = "`display` = 'Y' AND `id` = " . $db->quote($agent);
    } else {
        $sql_where[] = "`display` = 'Y' AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(CONCAT(`first_name`, ' ', `last_name`), '.', ''), '/', ''), ')', ''), '(', ''), '-', ' '), '  ', ' '), '\'', ''), ':', '') LIKE " . $db->quote(str_replace('-', ' ', $agent));
    }
}

// SQL Limit
$sql_limit = !empty($limit) ? ' LIMIT ' . (int) $limit : '';

// SQL Where
$sql_where = !empty($sql_where) ? ' WHERE ' . implode(" AND ", $sql_where) : '';

// SQL Order
$sql_order = !empty($sql_order) ? ' ORDER BY ' . $sql_order : '';

try {
    // Load agents
    $agents = array();
    $query = $db->prepare("SELECT " . $select_fields . " FROM `agents`" . $sql_where . $sql_order . $sql_limit . ";");
    $query->execute($sql_params);
    foreach ($query->fetchAll() as $agent) {
        // Agent Name
        $agent['name'] = $agent['first_name'] . ' ' . $agent['last_name'];

        // Agent Office
        $office = $db->fetch("SELECT * FROM `featured_offices` WHERE `id` = " . $db->quote($agent['office']) . " AND `display` = 'Y'");
        if (!empty($office)) {
            $office['location'] = implode(', ', array_filter(array($office['address'], $office['city'], $office['state'], $office['zip'])));
            $agent['office'] = $office;
        } else {
            unset($agent['office']);
        }

        // Agent Link
        if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER']) && $agent['display'] == 'Y') {
            $agent['link'] = '/agents/' . Format::slugify($agent['name']) . '/';
        }

        // Agent Photo
        $agent['image'] = (!empty($thumbnails) ? '/thumbs/' . $thumbnails : '') . '/' . (!empty($agent['image']) ? 'uploads/agents/' . $agent['image'] : $image_404);

        // Agent Website
        if (!empty(Settings::getInstance()->MODULES['REW_AGENT_CMS']) && $agent['cms'] == 'true' && empty($agent['website'])) {
            $agent['website'] = sprintf(Settings::getInstance()->SETTINGS['URL_AGENT_SITE'], $agent['cms_link']);
        }

        // Prepend http:// to Website URL
        $agent['website'] = $agent['website'] == 'http://' ? '' : $agent['website'];
        if (!empty($agent['website']) && !preg_match('#^https?://#i', $agent['website'])) {
            $agent['website'] =  'http://' . $agent['website'];
        }

        // Obfuscate Email
        $agent['email'] = implode(array_map(function ($char) {
            return '&#' . ord($char) . ';';
        }, str_split($agent['email'])));

        // Strip HTML Tags from Remarks
        $agent['remarks'] = Format::stripTags($agent['remarks']);

        // Truncate Description
        if (!empty($truncate)) {
            $agent['remarks'] = Format::truncate($agent['remarks'], $truncate, '&hellip;');
        }

        // New Lines
        $agent['remarks'] = nl2br(trim(Format::htmlspecialchars($agent['remarks']), "\r\n "));

        // Add to list
        $agents[] = $agent;
    }

    if (!isset($_GET['aname']) && $add_og_images) {
        // Open Graph Images
        $images = array_filter(array_map(function ($agent) use ($image_404) {
            if (preg_match('/' . preg_quote($image_404, '/') . '$/', $agent['image'])) {
                return false;
            }
            return Settings::getInstance()->SETTINGS['URL_RAW'] . $agent['image'];
        }, $agents));
        if (!is_null($page->info('og:image'))) {
            $images = array_merge($page->info('og:image'), $images);
        }

        $page->info('og:image', $images);
    }

    // Agent Details
    if ($mode == 'details') {
        // Single Agent
        $agent = array_pop($agents);

        // 404 Agent Not Found
        if (empty($agent)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true);
        } else {
            // Open Graph Images
            if (!preg_match('/' . preg_quote($image_404, '/') . '$/', $agent['image'])) {
                $page->info('og:image', $agent['image']);
            }

            // Update Page Title (Prepend Agent's Name)
            $page->info('title', $agent['name'] . ' | ' . $page->info('title'));

            // Agent testimonials
            if (!empty($testimonials)) {
                $queryString = "SELECT `client`, `testimonial` FROM `testimonials` WHERE `agent_id` = :agent_id ORDER BY RAND()%s;";
                $query = $db->prepare(sprintf($queryString, is_int($testimonials) ? ' LIMIT ' . $testimonials : ''));
                $query->execute(array('agent_id' => $agent['id']));
                $agent['testimonials'] = $query->fetchAll();
            }

            // Agent's Listings
            if (!empty($agent['agent_id'])) {
                // Agent IDs
                $agent_ids = json_decode($agent['agent_id'], true);

                // New 'feed => agent_id' format?
                if (is_array($agent_ids)) {
                    unset($agent['agent_id']);
                    foreach ($agent_ids as $feed => $id) {
                        if (empty($id)) {
                            continue;
                        }
                        if ($feed == Settings::getInstance()->IDX_FEED) {
                            $agent['agent_id'] = $id;
                            break;
                        }
                    }
                } else {
                    // Force new format
                    $agent_ids = array(Settings::getInstance()->IDX_FEED => $agent_ids);
                }

                // Unset feed keys with no agent ID
                foreach ($agent_ids as $k => $v) {
                    if (empty($v)) {
                        unset($agent_ids[$k]);
                    }
                }

                // No Agent ID for current feed?
                if (empty($agent['agent_id'])) {
                    // Are there Agent IDs for *any* feed? - use first one
                    if (!empty($agent_ids)) {
                        $id_feeds = array_keys($agent_ids);
                        $feed_key = $id_feeds[0];
                        Util_IDX::switchFeed($feed_key);
                        $agent['agent_id'] = $agent_ids[$feed_key];
                    }
                }

                // Require Agent ID
                if (!empty($agent['agent_id']) && !empty($agent_listings)) {
                    $page = $this->getContainer()->getPage();
                    $_REQUEST['snippet'] = true;
                    if (is_numeric($agent_listings)) {
                        $_REQUEST['page_limit'] = (int) $agent_listings;
                    }
                    $_REQUEST['agent_id'] = $agent['agent_id'];
                    $listings = $page->load('idx', 'search', Settings::getInstance()->IDX_FEED);
                    if (preg_match('/No listings were found matching your search criteria./', $listings['category_html'])) {
                        unset($_REQUEST['snippet'], $listings);
                    } else {
                        $listings = $listings['category_html'];
                    }

                    // Multi-IDX
                    if (!empty(Settings::getInstance()->IDX_FEEDS)) {
                        $agent_feeds = array_keys($agent_ids);
                        $show_picker = true;

                        // About to display single feed - is the picker needed?
                        if (count($agent_feeds) === 1) {
                            if (empty(Settings::getInstance()->IDX_FEED_DEFAULT)) {
                                // Current feed is the default - only show picker if agent feed is different
                                if ($agent_feeds[0] === Settings::getInstance()->IDX_FEED) {
                                    $show_picker = false;
                                }
                            }
                        }

                        // Prepend feed picker
                        if ($show_picker) {
                            $feeds = $page->container('feeds')->addModule('idx-feeds', array(
                                'mode' => 'inline',
                                'feeds' => $agent_feeds,
                            ));
                            $listings = $feeds->display(false) . $listings;
                        }
                    }
                }
            }
        }
    }

// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}
