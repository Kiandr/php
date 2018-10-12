<?php

/* @global Auth $authuser */

if (!Skin::hasFeature(Skin::REW_DEVELOPMENTS)) {
    throw new \REW\Backend\Exceptions\PageNotFoundException();
}

// TODO: validate 'website_url'

// Full width page
$body_class = 'full';

// Get Authorization Managers
$developmentsAuth = new REW\Backend\Auth\DevelopmentsAuth(Settings::getInstance());

// Authorized to Edit all Leads
if (!$developmentsAuth->canManageDevelopments($authuser)) {
    // Require permission to edit self
    if (!$developmentsAuth->canManageOwnDevelopments($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            'You do not have permission to add developments.'
        );
    } else {
        // Restrict to owned
        $agent_id = $authuser->info('id');
    }
}

// Notices
$success = [];
$errors = [];

// DB connection
$db = DB::get();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['reload'])) {
    try {
        // Slug-ify link URL
        $_POST['link'] = isset($_POST['link']) ? Format::slugify($_POST['link']) : null;

        // Required fields
        $required = [];
        $required['title'] = 'Development Title';
        $required['link'] = 'Development Link';
        foreach ($required as $name => $label) {
            if (empty($_POST[$name])) {
                throw new \InvalidArgumentException(sprintf(
                    '%s is a required field.',
                    $label
                ));
            }
        }

        // Require unique link URL
        $query = $db->prepare("SELECT `id` FROM `developments` WHERE `link` = ?;");
        $query->execute([$_POST['link']]);
        if ($query->fetchColumn() > 0) {
            throw new \InvalidArgumentException(sprintf(
                'The link "%s" is already being used.',
                Format::htmlspecialchars($_POST['link'])
            ));
        }

        // Search criteria
        switch ($_POST['search_criteria']) {
            case 'disabled':
                $_POST['idx_listings'] = 'N';
                $_POST['idx_snippet_id'] = 0;
                break;
            case 'builder':
                $_POST['idx_listings'] = 'Y';
                $_POST['idx_snippet_id'] = 0;
                break;
            case 'snippet':
                $_POST['idx_listings'] = 'Y';
                break;
        }

        // Prepare INSERT query
        $query = $db->prepare("INSERT INTO `developments` SET "
            // Basics
            . "`link` = :link, "
            . "`title` = :title, "
            . "`subtitle` = :subtitle, "
            . "`description` = :description, "
            . "`community_id` = :community_id, "
            . "`agent_id` = :agent_id, "
            // Display settings
            . "`is_enabled` = :is_enabled, "
            . "`is_featured` = :is_featured, "
            // Property criteria
            . "`idx_feed` = :idx_feed, "
            . "`idx_criteria` = :idx_criteria, "
            . "`idx_listings` = :idx_listings, "
            . "`idx_snippet_id` = :idx_snippet_id, "
            // Meta information
            . "`page_title` = :page_title,"
            . "`meta_description` = :meta_description,"
            . "`about_heading` = :about_heading,"
            // Development Info
            . "`website_url` = :website_url, "
            . "`completion_status` = :completion_status, "
            . "`completion_date` = :completion_date, "
            . "`completion_is_partial` = :completion_is_partial, "
            // Building information
            . "`num_stories` = :num_stories, "
            . "`num_units` = :num_units, "
            . "`unit_min_price` = :unit_min_price, "
            . "`unit_max_price` = :unit_max_price, "
            . "`unit_styles` = :unit_styles, "
            // Building features
            . "`common_features` = :common_features, "
            . "`construction` = :construction, "
            . "`parking` = :parking, "
            . "`views` = :views, "
            // Building address
            . "`address` = :address, "
            . "`city` = :city, "
            . "`state` = :state, "
            . "`zip` = :zip, "
            . "`timestamp_created` = NOW()"
        . ";");

        // Execute query
        $query->execute([
            // Basics
            'link' => $_POST['link'],
            'title' => $_POST['title'],
            'subtitle' => $_POST['subtitle'],
            'description' => $_POST['description'],
            'community_id' => $_POST['community_id'] ?: null,
            'agent_id' => $authuser->info('id'),
            // Display settings
            'is_enabled' => ($_POST['is_enabled'] === 'Y' ? 'Y' : 'N'),
            'is_featured' => ($_POST['is_featured'] === 'Y' ? 'Y' : 'N'),
            // Property criteria
            'idx_feed' => $_POST['feed'],
            'idx_criteria' => serialize($_POST),
            'idx_listings' => !empty($_POST['idx_listings']),
            'idx_snippet_id' => $_POST['idx_snippet_id'] ?: null,
            // Building information
            'website_url' => $_POST['website_url'],
            'completion_status' => $_POST['completion_status'],
            'completion_date' => $_POST['completion_date'],
            'completion_is_partial' => $_POST['completion_is_partial'],
            'num_stories' => $_POST['num_stories'],
            'num_units' => $_POST['num_units'],
            'unit_min_price' => preg_replace('/[^0-9]/', '', $_POST['unit_min_price']),
            'unit_max_price' => preg_replace('/[^0-9]/', '', $_POST['unit_max_price']),
            'unit_styles' => $_POST['unit_styles'],
            // Building features
            'common_features' => $_POST['common_features'],
            'construction' => $_POST['construction'],
            'parking' => $_POST['parking'],
            'views' => $_POST['views'],
            // Address
            'address' => $_POST['address'],
            'city' => $_POST['city'],
            'state' => $_POST['state'],
            'zip' => $_POST['zip'],
            // Meta information
            'page_title' => $_POST['page_title'],
            'meta_description' => $_POST['meta_description'],
            'about_heading' => $_POST['about_heading']
        ]);

        // Get newly insert record's ID
        $insert_id = $db->lastInsertId();

        // Save upload relations
        $upload_ids = $_POST['uploads'];
        if (!empty($upload_ids) && is_array($upload_ids)) {
            try {
                $queryString = sprintf(
                    "UPDATE `%s` SET `row` = ? WHERE `id` IN(%s);",
                    Settings::getInstance()->TABLES['UPLOADS'],
                    implode(', ', array_fill(0, count($upload_ids), '?'))
                );
                $query = $db->prepare($queryString);
                $params = array_merge([$insert_id], $upload_ids);
                $query->execute($params);
            } catch (\PDOException $e) {
                $errors[] = 'Error saving photos.';
                //$errors[] = $e->getMessage();
            }
        }

        // Save tag relations
        $tags = $_POST['tags'];
        if (!empty($tags) && is_array($tags)) {
            try {
                $queryString = "INSERT INTO `developments_tags`"
                    . " (`development_id`, `tag_name`, `tag_order`, `created_ts`)"
                    . " VALUES (?, ?, ?, NOW());";
                $query = $db->prepare($queryString);
                $tag_order = 0;
                foreach ($tags as $tag_name) {
                    $tag_name = Format::trim($tag_name);
                    if (empty($tag_name)) {
                        continue;
                    }
                    $query->execute([
                        $insert_id,
                        $tag_name,
                        ++$tag_order
                    ]);
                }
            } catch (\PDOException $e) {
                $errors[] = 'Error saving tags.';
                //$errors[] = $e->getMessage();
            }
        }

        // Save notices and redirect to edit form
        $success[] = 'Development has successfully been created.';
        header(sprintf('Location: ../edit/?id=%s&created', $insert_id));
        $authuser->setNotices($success, $errors);
        exit;

        // Submission error occurred
    } catch (\InvalidArgumentException $e) {
        $errors[] = $e->getMessage();

        // Database error occurred
    } catch (\PDOException $e) {
        $errors[] = 'Error saving development.';
        //$errors[] = $e->getMessage();
    }
}

// Fetch uploads
$uploads = [];
$upload_ids = $_POST['uploads'];
if (!empty($upload_ids) && is_array($upload_ids)) {
        $query = $db->prepare(sprintf(
            "SELECT `id`, `file` FROM `%s` WHERE `id` IN(%s) ORDER BY `order` ASC;",
            Settings::getInstance()->TABLES['UPLOADS'],
            implode(', ', array_fill(0, count($upload_ids), '?'))
        ));
        $query->execute(array_values($upload_ids));
        $uploads = $query->fetchAll();
}

// Load available communities
$query = $db->query("SELECT `id`, `title` FROM `featured_communities` ORDER BY `title` ASC;");
$communities = $query->fetchAll();

// Load available tags
$query = $db->query("SELECT DISTINCT `tag_name` FROM `developments_tags` ORDER BY `tag_name` ASC;");
$tags = $query->fetchAll(PDO::FETCH_COLUMN);

// Re-order tags based on $_POST
$tags_selected = $_POST['tags'];
if (!empty($tags_selected) && is_array($tags_selected)) {
        $tags = array_merge(array_filter($tags_selected, function ($tag) {
            return !empty($tag) && is_string($tag);
        }), $tags);
}

    // IDX Search Criteria
    $idx = Util_IDX::getIdx();
    $criteria = is_array($_POST) ? $_POST : [];
    $_REQUEST = search_criteria($idx, $criteria);

// IDX Search Panels
$panels = isset($_POST['panels']) ? $_POST['panels'] : false;
if (!is_array($panels)) {
    $panels = ['location' => ['display' => 1]];
}

// IDX Builder Panels
$builder = new IDX_Builder([
        'map' => false,
        'mode' => 'snippet',
        'panels' => $panels,
        'toggle' => false
]);

// Load available IDX snippets
$query = $db->prepare("SELECT `id`, `name` FROM `snippets` WHERE `type` = 'idx' AND `agent` = ? ORDER BY `name` ASC;");
$query->execute([$developmentsAuth->canManageDevelopments($authuser) ? 1 : $authuser->info('id')]);
$idx_snippets = $query->fetchAll();
