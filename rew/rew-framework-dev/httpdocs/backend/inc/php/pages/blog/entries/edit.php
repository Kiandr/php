<?php

// App DB
$db = DB::get();

// Full Page
$body_class = 'full';

// Get Authorization Managers
$settings = Settings::getInstance();
$blogAuth = new REW\Backend\Auth\BlogsAuth($settings);
$toolsAuth = new REW\Backend\Auth\ToolsAuth($settings);


// Require permission to edit all associates
if (!$blogAuth->canManageEntries($authuser)) {
    // Require permission to edit self
    if (!$blogAuth->canManageSelf($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to edit blog entries.')
        );
    } else {
        // Agent Mode, Only show agent's blog entries
        $sql_agent = "AND `agent` = :agent";
        $sql_agent_id = $authuser->info('id');
    }
// Filter By Agent
} else if (!empty($_GET['filter'])) {
    // Set Agent Filter
    $filterAgent = Backend_Agent::load($_GET['filter']);
    if (isset($filterAgent) && $filterAgent instanceof Backend_Agent) {
        $sql_agent = "AND `agent` = :agent";
        $sql_agent_id = $filterAgent->getId();
    }
}

// Success
$success = array();

// Error
$errors = array();

// Row ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Require Record
try {
    $params = ["id" => $_GET['id']];
    if(!empty($sql_agent)) {
        $params["agent"] = $sql_agent_id;
    }
    $edit_entry = $db->fetch("SELECT * FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `id` = :id $sql_agent;", $params);
} catch (PDOException $e) {}

// Throw Missing Entry Exception
if (empty($edit_entry)) {
    throw new \REW\Backend\Exceptions\MissingId\Blog\MissingEntryException();
}

// New Row Successful
if (!empty($_GET['success']) && $_GET['success'] == 'add') {
    $success[] = __('Blog Entry has successfully been created.');
}

// Process Submit
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'title', 'title' => __('Entry Title'));
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Check Errors
    if (empty($errors)) {
        // Check Input
        $_POST['categories'] = is_array($_POST['categories'])  ? implode(",", $_POST['categories']) : $_POST['categories'];

        // Published Timestamp
        if ($edit_entry['published'] == 'true') {
            if ($_POST['published'] == 'true') {
                $published = 'true';
                $publish   = date('Y-m-d H:i:s', strtotime($_POST['timestamp_published']));
            } else {
                $published = 'false';
                $publish   = "0";
            }
        } else {
            if ($_POST['published'] == 'true') {
                $published = 'true';
                $publish = !empty($_POST['timestamp_published']) ? date('Y-m-d H:i:s', strtotime($_POST['timestamp_published'])) : 'NOW()';
            } else {
                $published = 'false';
                $publish   = '0';
            }
        }

        // Process Blog Tags
        $_POST['tags'] = strip_tags($_POST['tags']);
        if (!empty($_POST['tags'])) {
            $tags = array();
            foreach (explode(",", $_POST['tags']) as $tag) {
                $tag_name = trim($tag);
                $tag_link = Format::slugify($tag);
                try {
                    $count = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_TAGS . "` WHERE `link` = :link AND `title` = :title;", ["link" => $tag_link, "title" => $tag_name]);
                } catch (PDOException $e) {}

                if (empty($count['total'])) {
                    try {
                        $db->prepare("REPLACE INTO `" . TABLE_BLOG_TAGS . "` SET `link` = :link, `title` = :title, `timestamp_created` = NOW();")->execute(["link" => $tag_link, "title" => $tag_name]);
                    } catch (PDOException $e) {}
                }
                $tags[] = $tag_name;
            }
            $_POST['tags'] = implode(",", $tags);
        }

        // Build UPDATE Query
        try {
            $query = "UPDATE `" . TABLE_BLOG_ENTRIES . "` SET "
                   . "`title`               = :title, "
                   . "`body`                = :body, "
                   . "`meta_tag_desc`       = :meta_tag_desc, "
                   . "`categories`          = :categories, "
                   . "`tags`                = :tags, "
                   . "`link_title1`         = :link_title1, "
                   . "`link_title2`         = :link_title2, "
                   . "`link_title3`         = :link_title3, "
                   . "`link_url1`           = :link_url1, "
                   . "`link_url2`           = :link_url2, "
                   . "`link_url3`           = :link_url3, "
                   . "`published`           = :published, "
                   . "`timestamp_published` = :timestamp_published, "
                   . "`timestamp_updated`   = NOW()"
                   . " WHERE "
                   . "`id` = :id;";

            $db->prepare($query)->execute([
                "title"                 => $_POST['title'],
                "body"                  => $_POST['body'],
                "meta_tag_desc"         => $_POST['meta_tag_desc'],
                "categories"            => $_POST['categories'],
                "tags"                  => $_POST['tags'],
                "link_title1"           => $_POST['link_title1'],
                "link_title2"           => $_POST['link_title2'],
                "link_title3"           => $_POST['link_title3'],
                "link_url1"             => $_POST['link_url1'],
                "link_url2"             => $_POST['link_url2'],
                "link_url3"             => $_POST['link_url3'],
                "published"             => $published,
                "timestamp_published"   => $publish,
                "id"                    => $edit_entry['id']
            ]);

            //force blog-rss-reader to refresh cache
            $index =  Http_Host::getDomainUrl() . 'blog/rss/';
            Cache::deleteCache($index);

            // Fetch Updated Row
            try {
                $edit_entry = $db->fetch("SELECT * FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `id` = :id;", ["id" => $edit_entry['id']]);
            } catch (PDOException $e) {}

            // Process Pingback
            $pingback = pingback($edit_entry);

            // Success
            $success[] = __('Blog Entry has successfully been saved.');

        // Query Error
        } catch (PDOException $e) {
            $errors[] = __('Error occurred, Blog Entry could not be saved.');
        }
    }

    // Use $_POST Data
    foreach ($edit_entry as $k => $v) {
        $edit_entry[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}

// Blog Categories
$categories = array();

// Select Blog Categories
try {
    foreach ($db->fetchAll("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `parent` = '' ORDER BY `order`;") as $category) {
        // Select Sub-Categories
        $category['subcategories'] = array();
        try {
            $category['subcategories'] = $db->fetchAll("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `parent` = :parent ORDER BY `order`;", ["parent" => $category['link']]);
        } catch (PDOException $e) {}

        // Add to Collection
        $categories[] = $category;
    }
} catch (PDOException $e) {}

// Require Array
$edit_entry['categories'] = is_array($edit_entry['categories']) ? $edit_entry['categories'] : explode(",", $edit_entry['categories']);

// Admin Mode
if ($blogAuth->canManageEntries($authuser)) {
    // Blog Entry's Author
    try {
        $author = $db->fetch("SELECT `id`, `first_name`, `last_name` FROM `" . TABLE_BLOG_AUTHORS . "` WHERE `id` = :id;", ["id" => $edit_entry['agent']]);
    } catch (PDOException $e) {
        $errors[] = __('Error Occurred while loading Blog Entry Author.');
    }
}

// Require UNIX Timestamp
$edit_entry['timestamp_published'] = (empty($edit_entry['timestamp_published']) || $edit_entry['timestamp_published'] == '0000-00-00 00:00:00') ? strtotime(date("Y-m-d H:i:s")) : strtotime($edit_entry['timestamp_published']);

// Snippets Used on this Page
preg_match_all("!#([a-zA-Z0-9_-]+)#!", $edit_entry['body'], $matches);
if (!empty($matches)) {
    $edit_entry['snippets'] = array();
    foreach ($matches[1] as $match) {
        try {
            $snippet = $db->fetch("SELECT `name`, `type` FROM `" . TABLE_SNIPPETS . "` WHERE (`agent` IS NULL OR `agent` = :agent) AND `name` = :name;", ["agent" => Settings::getInstance()->SETTINGS['agent'], "name" => $match]);
        } catch (PDOException $e) {}

        if (!empty($snippet)) {
            $edit_entry['snippets'][] = $snippet;
        } else {
            if ($toolsAuth->canManageCommunities($authuser)) {
                try {
                    $snippet = $db->fetch("SELECT `id`, `snippet` as `name`, 'Featured Community' AS `type` FROM `" . TABLE_FEATURED_COMMUNITIES . "` WHERE `snippet` = :snippet;", ["snippet" => $match]);
                } catch (PDOException $e) {}

                if (!empty($snippet)) {
                    $edit_entry['snippets'][] = $snippet;
                }
            }
        }
    }
}

// Open graph images
$og_image = array();
try {
    $og_image = $db->fetchAll("SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `type` = 'blog:og:image' AND `row` = :row ORDER BY `order` ASC;", ["row" => $edit_entry['id']]);
} catch (PDOException $e) {}
