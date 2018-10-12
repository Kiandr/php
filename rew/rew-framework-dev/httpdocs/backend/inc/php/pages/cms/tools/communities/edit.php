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
$success = array();
$errors = array();

// DB connection
$db = DB::get();

try {
    // Load featured community
    $community_id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
    $find_community = $db->prepare("SELECT * FROM `featured_communities` WHERE `id` = :community_id LIMIT 1;");
    $find_community->execute(array('community_id' => $community_id));
    $community = $find_community->fetch();
} catch (PDOException $e) {
    $errors[] = __('Error occurred while loading the selected community.');
    //$errors[] = $e->getMessage();
}

/* Throw Missing Community Exception */
if (empty($community)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingCommunityException();
}

// Select IDX
if (!empty($community['search_idx'])) {
    Util_IDX::switchFeed($community['search_idx']);
    $idx = Util_IDX::getIdx();
    $db_idx = Util_IDX::getDatabase();
}

// Process submit
if (isset($_GET['submit'])) {
    // Required fields
    $required = array();
    $required[] = array('value' => 'title', 'title' => __('Community Title'));
    $required[] = array('value' => 'snippet', 'title' => __('Snippet Name'));
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
            $check_snippet->execute(array('snippet_name' => $snippet_name, 'agent_id' => $authuser->info('id')));
            $snippet_exists = $check_snippet->fetchColumn();
            if (empty($snippet_exists)) {
                $check_snippet = $db->prepare("SELECT `snippet` FROM `featured_communities` WHERE `snippet` = :snippet_name AND `id` != :community_id;");
                $check_snippet->execute(array('snippet_name' => $snippet_name, 'community_id' => $community['id']));
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
            // Assigned community page
            $page_id = $_POST['page_id'];
            $page_id = $page_select && is_numeric($page_id) ? $page_id : 0;

            // UPDATE params
            $query_params = [
                'community_id' => $community['id'],
                'page_id' => $page_id
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

            // UPDATE featured community record
            $db->prepare("UPDATE `featured_communities` SET "
                . "`title`				= :title,"
                . "`subtitle`			= :subtitle,"
                . "`description`		= :description,"
                . ($nonelite_headings ? "`stats_heading` = :stats_heading," : '')
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
                . "`search_criteria`	= :search_criteria,"
                . "`is_enabled`         = :is_enabled,"
                . "`page_id`			= :page_id,"
                . "`snippet`			= :snippet,"
                . "`timestamp_updated`	= NOW()"
                . " WHERE `id` = :community_id"
            . ";")->execute($query_params + [
                'title'             => $_POST['title'],
                'subtitle'          => $_POST['subtitle'],
                'description'       => $_POST['description'],
                'stats_total'       => $_POST['stats_total'],
                'stats_average'     => $_POST['stats_average'],
                'idx_snippet'       => ($_POST['search_criteria'] === 'false' && !empty($_POST['idx_snippet']) ? $_POST['idx_snippet'] : null),
                'is_enabled'        => ($_POST['is_enabled'] === 'Y' ? 'Y' : 'N'),
                'search_criteria'   => serialize($_POST),
                'snippet'           => $_POST['snippet']
            ]);

            try {
                // Save community tags
                $tags = $_POST['tags'];
                $community['tags'] = array();

                // Must be able to tag
                if (!empty($can_tag)) {
                    // INSERT new tags and UPDATE existing tags
                    if (!empty($tags) && is_array($tags)) {
                        $tag_order = 0;
                        $insert_tag = $db->prepare("INSERT INTO `featured_communities_tags` SET "
                            . "`community_id`	= :community,"
                            . "`tag_name`		= :tag_name,"
                            . "`tag_order`		= :tag_order,"
                            . "`created_ts`		= NOW()"
                            . " ON DUPLICATE KEY UPDATE "
                            . "`tag_order`		= :tag_order,"
                            . "`updated_ts` 	= NOW()"
                        . ";");
                        foreach ($tags as $tag) {
                            try {
                                if (is_string($tag)) {
                                    $insert_tag->execute(array(
                                        'community' => $community['id'],
                                        'tag_order' => ++$tag_order,
                                        'tag_name'  => $tag
                                    ));
                                    $community['tags'][] = $tag;
                                }
                            } catch (PDOException $e) {
                                $errors[] = __('Error saving community tag: ') . json_encode($tag);
                                //$errors[] = $e->getMessage();
                            }
                        }
                    }

                    // DELETE old tags - but not the new tags
                    $delete_where = false;
                    $delete_params = array($community['id']);
                    if (!empty($community['tags'])) {
                        $delete_params = array_merge($delete_params, $community['tags']);
                        $delete_where = implode(',', array_fill(0, count($community['tags']), '?'));
                        $delete_where = ' AND `tag_name` NOT IN (' . $delete_where . ')';
                    }
                    $delete_query = $db->prepare('DELETE FROM `featured_communities_tags` WHERE `community_id` = ?' . $delete_where . ';');
                    $delete_query->execute($delete_params);
                }

            // Database error occurred
            } catch (PDOException $e) {
                $errors[] = __('Error saving community tags.');
                //$errors[] = $e->getMessage();
            }

            // Success, redirect back to form
            $success[] = __('Featured Community has successfully been saved.');

            // Trigger hook after featured community is updated
            $query = $db->prepare("SELECT * FROM `featured_communities` WHERE `id` = ? LIMIT 1;");
            $query->execute(array($community['id']));
            Hooks::hook(Hooks::HOOK_FEATURED_COMMUNITY_CREATE)->run($query->fetch());

            // Save notices and redirect to edit form
            $authuser->setNotices($success, $errors);
            header('Location: ?id=' . $community['id'] . '&success');
            exit;

        // Database error
        } catch (PDOException $e) {
            $errors[] = __('Error occurred while saving featured community.');
            //$errors[] = $e->getMessage();
        }
    }
}

try {
    // Community photos
    $uploads = array();
    $community_photos = $db->prepare("SELECT `id`, `file` FROM `cms_uploads` WHERE `type` = 'community' AND `row` = :community_id ORDER BY `order` ASC;");
    $community_photos->execute(array('community_id' => $community['id']));
    $uploads = $community_photos->fetchAll();
} catch (PDOException $e) {
    $errors[] = __('Error loading community photos.');
    //$errors[] = $e->getMessage();
}

// Assign to Page
if (!empty($page_select)) {
    // Community Pages
    $cms_pages = array();
    $cms_subpages = array();
    $query = $db->prepare("SELECT `page_id`, `link_name`, `file_name`, `category`, `is_main_cat` FROM `pages` WHERE `is_link` = 'f' AND `agent` = 1 AND `file_name` NOT IN ('404', 'error', 'unsubscribe') ORDER BY `category` ASC, IF(`is_main_cat` = 't', 1, 0) DESC;");
    $query->execute();
    foreach ($query->fetchAll() as $cms_page) {
        // Add page to list
        $index = $cms_page['category'];
        if ($cms_page['is_main_cat'] === 't') {
            $cms_pages[$index] = array(
                'page_id' => $cms_page['page_id'],
                'link_name' => $cms_page['link_name']
            );

        // Add sub-page to list
        } elseif (is_array($cms_pages[$index])) {
            $index = $cms_pages[$index]['link_name'];
            $cms_subpages[$index][] = array(
                'page_id' => $cms_page['page_id'],
                'link_name' => $cms_page['link_name']
            );
        }
    }
}

// Load community tags
if (!empty($can_tag)) {
    // Community Tags
    $query = $db->query("SELECT DISTINCT `tag_name` FROM `featured_communities_tags` ORDER BY `tag_name` ASC;");
    $community_tags = $query->fetchAll(PDO::FETCH_COLUMN);

    // Community Tags
    $query = $db->prepare("SELECT `tag_name` FROM `featured_communities_tags` WHERE `community_id` = :community_id ORDER BY `tag_order` ASC;");
    $query->execute(array('community_id' => $community['id']));
    $community['tags'] = $query->fetchAll(PDO::FETCH_COLUMN);

    // Re-order tags
    $community_tags = array_unique(array_merge($community['tags'], $community_tags));
}

// Load IDX Snippets
$query = $db->prepare("SELECT `id`, `name` FROM `snippets` WHERE `type` = 'idx' AND `agent` = 1 AND `code` LIKE :feed ORDER BY `name` ASC;");
$query->execute(array(
    'feed'     => '%s:4:"feed";s:' . strlen(Settings::getInstance()->IDX_FEED) . ':"' . Settings::getInstance()->IDX_FEED . '";%'
));
$idx_snippets = $query->fetchAll();

// Search criteria
$criteria = !empty($community['search_criteria']) ? unserialize($community['search_criteria']) : $_POST;
$criteria = is_array($criteria) ? $criteria : array();
$_REQUEST = search_criteria($idx, $criteria);

// IDX search panels
$panels = isset($_REQUEST['panels']) && is_array($_REQUEST['panels']) ? $_REQUEST['panels'] : [];

// IDX Builder Panels
$builder = new IDX_Builder(array(
    'map' => false,
    'mode' => 'snippet',
    'panels' => $panels
));
