<?php

// App DB
$db = DB::get();

/* Full Page */
$body_class = 'full';

// Get Authorization Managers
$blogAuth = new REW\Backend\Auth\BlogsAuth(Settings::getInstance());

// Require permission to edit all blog entries
if (!$blogAuth->canManageEntries($authuser)) {
    // Require permission to edit own blog entries
    if (!$blogAuth->canManageSelf($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to edit blog entries.')
        );
    }
}

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/* Process Submit */
if (isset($_GET['submit'])) {
    /* Required Fields */
    $required   = array();
    $required[] = array('value' => 'title', 'title' => __('Entry Title'));

    /* Process Required Fields */
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    /* Check Errors */
    if (empty($errors)) {
        /* Check Input */
        $_POST['categories'] = is_array($_POST['categories'])  ? implode(",", $_POST['categories']) : $_POST['categories'];

        /* Create Link */
        $_POST['link'] = Format::slugify($_POST['title']);

        /* Make Sure Link is Unique */
        $_POST['link'] = uniqueLink($_POST['link'], TABLE_BLOG_ENTRIES, 'link');

        /* Published Timestamp */
        if ($_POST['published'] == 'true') {
            $published = 'true';
            $publish = !empty($_POST['timestamp_published']) ? date('Y-m-d H:i:s', strtotime($_POST['timestamp_published'])) : 'NOW()';
        } else {
            $published = 'false';
            $publish = '0';
        }

        /* Process Blog Tags */
        $_POST['tags'] = strip_tags($_POST['tags']);
        if (!empty($_POST['tags'])) {
            $tags = array();
            foreach (explode(",", $_POST['tags']) as $tag) {
                $tag_name = trim($tag);
                $tag_link = Format::slugify($tag);
                try {
                    $count = $db->fetch("SELECT COUNT(id) AS `total` FROM `" . TABLE_BLOG_TAGS . "` WHERE `link` = :link AND `title` = :title;", ["link" => $tag_link, "title" => $tag_name]);
                } catch (PDOException $e) {}

                if (empty($count['total'])) {
                    try {
                        $db->prepare("REPLACE INTO `" . TABLE_BLOG_TAGS . "` SET `link` = :link, `title` = :title, `timestamp_created` = NOW();", ["link" => $tag_link, "title" => $tag_name]);
                    } catch (PDOException $e) {}
                }
                $tags[] = $tag_name;
            }
            $_POST['tags'] = implode(",", $tags);
        }

        try {

            /* Build INSERT Query */
            $query = "INSERT INTO `" . TABLE_BLOG_ENTRIES . "` SET "
                   . "`agent`               = :agent, "
                   . "`link`                = :link, "
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
                   . "`timestamp_created`   = NOW();";

            $db->prepare($query)->execute([
                "agent"                 => $authuser->info('id'),
                "link"                  => $_POST['link'],
                "title"                 => $_POST['title'],
                "body"                  => $_POST['body'],
                "meta_tag_desc"         => $_POST['meta_tag_desc'],
                "categories"            => (empty($_POST['categories']) ? "" : $_POST['categories']),
                "tags"                  => $_POST['tags'],
                "link_title1"           => $_POST['link_title1'],
                "link_title2"           => $_POST['link_title2'],
                "link_title3"           => $_POST['link_title3'],
                "link_url1"             => $_POST['link_url1'],
                "link_url2"             => $_POST['link_url2'],
                "link_url3"             => $_POST['link_url3'],
                "published"             => $published,
                "timestamp_published"   => $publish
            ]);

            /* Insert ID */
            $insert_id = $db->lastInsertId();

            //force blog-rss-reader to refresh cache
            $index =  Http_Host::getDomainUrl() . 'blog/rss/';
            Cache::deleteCache($index);

            // Update og:image records
            if (!empty($_POST['og_image']) && is_array($_POST['og_image'])) {
                foreach ($_POST['og_image'] as $og_image) {
                    try {
                        $db->prepare("UPDATE `" . Settings::getInstance()->TABLES['UPLOADS'] . "`"
                            . " SET `row` = :row"
                            . " WHERE `id` = :id AND"
                            . " `type` = 'blog:og:image'"
                            . ";")->execute(["row" => $insert_id, "id" => $og_image]);
                    } catch (PDOException $e) {}
                }
            }

            /* Fetch Row */
            try {
                $entry = $db->fetch("SELECT * FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `id` = :id;", ["id" => $insert_id]);
            } catch (PDOException $e) {}

            /* Process Pingback */
            $pingback = pingback($entry);

            /* Redirect to Edit Form */
            header('Location: ../edit/?id=' . $insert_id . '&success=add');

            /* Exit Script */
            exit;
        } catch (PDOException $e) {
            $errors[] = __('Error occurred, Blog Entry could not be saved.');
            $errors[] = $query;
            $errors[] = $e->getMessage();
        }
    }
}

/* Blog Categories */
$categories = array();

/* Select Blog Categories */
/* Build Collection */
try {
    foreach ($db->fetchAll("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `parent` = '' ORDER BY `order`;") as $category) {
        /* Select Sub-Categories */
        $category['subcategories'] = array();
        try {
            $category['subcategories'] = $db->fetchAll("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `parent` = :parent ORDER BY `order`;", ["parent" => $category['link']]);
        } catch (PDOException $e) {}

        /* Add to Collection */
        $categories[] = $category;
    }
} catch (PDOException $e) {}

// Open graph images
$og_image = array();
if (!empty($_POST['og_image']) && is_array($_POST['og_image'])) {
    foreach ($_POST['og_image'] as $og_image) {
        if (is_numeric($og_image)) {
            try {
                foreach ($db->fetchAll("SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `id` = :id LIMIT 1;", ["id" => $og_image]) as $row) {
                    $og_image[] = $row;
                }
            } catch (PDOException $e) {}
        }
    }
}

/* Require Array */
$_POST['categories'] = is_array($_POST['categories']) ? $_POST['categories'] : explode(",", $_POST['categories']);

/* Publish by Default */
if (!isset($_GET['submit'])) {
    $_POST['published'] = 'true';
    $_POST['timestamp_published'] = time();
}
