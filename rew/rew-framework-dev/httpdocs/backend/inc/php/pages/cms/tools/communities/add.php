<?php

// Create Auth Classes
$toolsAuth = new REW\Backend\Auth\ToolsAuth(Settings::getInstance());
if (!$toolsAuth->canManageCommunities($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage communities.')
    );
}

// Skin Feature: Include input field for video link
$has_video_link_feature = Skin::hasFeature(Skin::COMMUNITY_VIDEO_LINKS);

// Skin Feature: Display "Anchor Links" section
$anchor_links = Skin::hasFeature(Skin::COMMUNITY_DISABLE_ANCHOR_LINKS) ? false : true;

// Skin Feature: No character limit for community description
$char_limit = Skin::hasFeature(Skin::COMMUNITY_DESCRIPTION_NO_LIMIT) ? 0 : 500;

// Skin Feature: Select a CMS page to link to
$page_select = Skin::hasFeature(Skin::LINK_COMMUNITY_TO_PAGE);

// Skin Feature: Tags & Keywords
$can_tag = Skin::hasFeature(Skin::COMMUNITY_TAGS);

// Limit to headings used on elite
$nonelite_headings = !in_array(Settings::getInstance()->SKIN, ['elite']);

// Notices
$success = [];
$errors = [];

// DB connection
$db = DB::get();

// Process submit
if (isset($_GET['submit'])) {
    // Required fields
    $required = [];
    $required[] = ['value' => 'title', 'title' => __('Community Title')];
    $required[] = ['value' => 'snippet', 'title' => __('Snippet Name')];
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Require unique snippet name
    $snippet_name = $_POST['snippet'];
    if (!empty($snippet_name)) {
        try {
            $snippet_name = Format::slugify($snippet_name);
            $check_snippet = $db->prepare("SELECT `name` FROM `snippets` WHERE `name` = :snippet_name AND (`agent` = :agent_id OR `agent` IS NULL);");
            $check_snippet->execute(['snippet_name' => $snippet_name, 'agent_id' => $authuser->info('id')]);
            $snippet_exists = $check_snippet->fetchColumn();
            if (empty($snippet_exists)) {
                $check_snippet = $db->prepare("SELECT `snippet` FROM `featured_communities` WHERE `snippet` = :snippet_name;");
                $check_snippet->execute(['snippet_name' => $snippet_name]);
                $snippet_exists = $check_snippet->fetchColumn();
            }
            if (!empty($snippet_exists)) {
                $errors[] = __('The snippet name %s is already in use.', '<strong>#' . Format::htmlspecialchars($snippet_exists) . '#</strong>');
            }
        } catch (PDOException $e) {
            $errors[] = __('Error validating snippet name.');
            //$errors[] = $e->getMessage();
        }
    }

    // Check errors
    if (empty($errors)) {
        try {
            // Community order
            $max_order = $db->query("SELECT MAX(`order`) FROM `featured_communities`;");
            $max_order = $max_order->fetchColumn() + 1;
        } catch (PDOException $e) {
            //$errors[] = $e->getMessage();
            $max_order = 0;
        }

        try {
            // Assigned community page
            $page_id = $_POST['page_id'];
            $page_id = $page_select && is_numeric($page_id) ? $page_id : 0;

            // INSERT params
            $query_params = [
                'page_id' => $page_id,
                'order' => $max_order
            ];

            // Video feature link
            if (!empty($has_video_link_feature)) {
                $query_params += [
                    'video_link' => $_POST['video_link']
                ];
            }

            // Anchor link info
            if (!empty($anchor_links)) {
                $query_params += [
                    'anchor_one_text' => $_POST['anchor_one_text'],
                    'anchor_one_link' => $_POST['anchor_one_link'],
                    'anchor_two_text' => $_POST['anchor_two_text'],
                    'anchor_two_link' => $_POST['anchor_two_link']
                ];
            }

            // Stats Headings
            if ($nonelite_headings) {
                $query_params += [
                    'stats_heading' => $_POST['stats_heading'],
                    'stats_highest' => $_POST['stats_highest'],
                    'stats_lowest'  => $_POST['stats_lowest']
                ];
            }

            // INSERT featured community to database
            $db->prepare("INSERT INTO `featured_communities` SET "
                . "`title`				= :title,"
                . "`subtitle`			= :subtitle,"
                . "`description`		= :description,"
                . ($nonelite_headings ? "`stats_heading`		= :stats_heading," : '')
                . "`stats_total`		= :stats_total,"
                . "`stats_average`		= :stats_average,"
                . ($nonelite_headings ? "`stats_highest` = :stats_highest, `stats_lowest` = :stats_lowest," : "")
                . ($anchor_links ?
                    "`anchor_one_text` = :anchor_one_text,"
                    . "`anchor_one_link` = :anchor_one_link,"
                    . "`anchor_two_text` = :anchor_two_text,"
                    . "`anchor_two_link` = :anchor_two_link,"
                : '')
                . ($has_video_link_feature
                    ? "`video_link` = :video_link,"
                : '')
                . "`idx_snippet`		= :idx_snippet,"
                . "`search_idx`			= :search_idx,"
                . "`search_criteria`	= :search_criteria,"
                . "`is_enabled`			= :is_enabled,"
                . "`snippet`			= :snippet,"
                . "`page_id`			= :page_id,"
                . "`order`				= :order,"
                . "`timestamp_created`	= NOW()"
            . ";")->execute($query_params + [
                'title'             => $_POST['title'],
                'subtitle'          => $_POST['subtitle'],
                'description'       => $_POST['description'],
                'stats_total'       => $_POST['stats_total'],
                'stats_average'     => $_POST['stats_average'],
                'idx_snippet'       => ($_POST['search_criteria'] === 'false' && !empty($_POST['idx_snippet']) ? $_POST['idx_snippet'] : null),
                'is_enabled'        => ($_POST['is_enabled'] === 'Y' ? 'Y' : 'N'),
                'search_idx'        => $_POST['feed'],
                'search_criteria'   => serialize($_POST),
                'snippet'           => $_POST['snippet']
            ]);

            // Community ID
            $insert_id = $db->lastInsertId();

            // Save community photos
            $upload_ids = $_POST['uploads'];
            if (!empty($upload_ids) && is_array($upload_ids)) {
                try {
                    $save_upload = $db->prepare("UPDATE `cms_uploads` SET `row` = :community_id WHERE `id` = :upload_id;");
                    foreach ($upload_ids as $upload_id) {
                        $save_upload->execute([
                            'community_id' => $insert_id,
                            'upload_id' => $upload_id
                        ]);
                    }
                } catch (PDOException $e) {
                    $errors[] = __('Error saving community photos.');
                    //$errors[] = $e->getMessage();
                }
            }

            try {
                // INSERT community tags
                $tags = $can_tag ? $_POST['tags'] : false;
                if (!empty($tags) && is_array($tags)) {
                    $tag_order = 0;
                    $insert_tag = $db->prepare("INSERT INTO `featured_communities_tags` SET "
                        . "`community_id`	= :community,"
                        . "`tag_name`		= :tag_name,"
                        . "`tag_order`		= :tag_order,"
                        . "`created_ts`		= NOW()"
                    . ";");
                    foreach ($tags as $tag) {
                        try {
                            if (is_string($tag)) {
                                $insert_tag->execute([
                                    'community' => $insert_id,
                                    'tag_order' => ++$tag_order,
                                    'tag_name'  => $tag
                                ]);
                                $community['tags'][] = $tag;
                            }
                        } catch (PDOException $e) {
                            $errors[] = __('Error saving community tag: ') . json_encode($tag);
                            //$errors[] = $e->getMessage();
                        }
                    }
                }

            // Database error occurred
            } catch (PDOException $e) {
                $errors[] = __('Error saving community tags.');
                //$errors[] = $e->getMessage();
            }

            // Trigger hook after featured community is added
            $query = $db->prepare("SELECT * FROM `featured_communities` WHERE `id` = ? LIMIT 1;");
            $query->execute([$insert_id]);
            Hooks::hook(Hooks::HOOK_FEATURED_COMMUNITY_CREATE)->run($query->fetch());

            // Save notices and redirect to edit form
            $success[] = __('Featured Community has successfully been created.');
            $authuser->setNotices($success, $errors);
            header('Location: ../edit/?id=' . $insert_id);
            exit;

        // Database error
        } catch (PDOException $e) {
            $errors[] = __('Error occurred while saving featured community.');
        }
    }
}

// Community photos
$uploads = [];
$upload_ids = $_POST['uploads'];
if (!empty($upload_ids) && is_array($upload_ids)) {
    try {
        $community_photos = $db->prepare("SELECT `id`, `file` FROM `cms_uploads` WHERE `id` IN (" . implode(',', array_fill(0, count($upload_ids), '?')) . ") ORDER BY `order` ASC;");
        $community_photos->execute(array_values($upload_ids));
        $uploads = $community_photos->fetchAll();
    } catch (PDOException $e) {
        $errors[] = __('Error loading community photos.');
        //$errors[] = $e->getMessage();
    }
}

// Assign to Page
if (!empty($page_select)) {
    // Community Pages
    $cms_pages = [];
    $cms_subpages = [];
    $query = $db->prepare("SELECT `page_id`, `link_name`, `file_name`, `category`, `is_main_cat` FROM `pages` WHERE `is_link` = 'f' AND `agent` = 1 AND `file_name` NOT IN ('404', 'error', 'unsubscribe') ORDER BY `category` ASC, IF(`is_main_cat` = 't', 1, 0) DESC;");
    $query->execute();
    foreach ($query->fetchAll() as $cms_page) {
        // Add page to list
        $index = $cms_page['category'];
        if ($cms_page['is_main_cat'] === 't') {
            $cms_pages[$index] = [
                'page_id' => $cms_page['page_id'],
                'link_name' => $cms_page['link_name']
            ];

        // Add sub-page to list
        } elseif (is_array($cms_pages[$index])) {
            $index = $cms_pages[$index]['link_name'];
            $cms_subpages[$index][] = [
                'page_id' => $cms_page['page_id'],
                'link_name' => $cms_page['link_name']
            ];
        }
    }
}

// Can use community tags
if (!empty($can_tag)) {
    // Community Tags
    $query = $db->query("SELECT DISTINCT `tag_name` FROM `featured_communities_tags` ORDER BY `tag_name` ASC;");
    $community_tags = $query->fetchAll(PDO::FETCH_COLUMN);

    // Re-order tags based on $_POST
    if (is_array($_POST['tags']) && !empty($_POST['tags'])) {
        $order_by = array_map(function ($community_tag) {
            $index = array_search($community_tag, $_POST['tags']);
            return $index === false ? PHP_INT_MAX : $index;
        }, $community_tags);
        array_multisort($community_tags, SORT_DESC, SORT_NUMERIC, $order_by);
    }
}

// Load IDX Snippets
$query = $db->prepare("SELECT `id`, `name` FROM `snippets` WHERE `type` = 'idx' AND `agent` = 1 AND `code` LIKE :feed ORDER BY `name` ASC;");
$query->execute([
    'feed' => '%s:4:"feed";s:' . strlen(Settings::getInstance()->IDX_FEED) . ':"' . Settings::getInstance()->IDX_FEED . '";%'
]);
$idx_snippets = $query->fetchAll();

// Search criteria
$idx = Util_IDX::getIDX();
$criteria = is_array($_POST) ? $_POST : [];
$_REQUEST = search_criteria($idx, $criteria);

// IDX search panels
$panels = isset($_POST['panels']) && is_array($_POST['panels']) ? $_POST['panels'] : [
    'subdivision' => ['display' => 1],
    'city' => ['display' => 1],
    'type' => ['display' => 1]
];

// IDX Builder Panels
$builder = new IDX_Builder([
    'map' => false,
    'mode' => 'snippet',
    'panels' => $panels
]);
