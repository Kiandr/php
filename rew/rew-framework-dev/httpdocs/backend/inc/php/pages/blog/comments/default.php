<?php

// App DB
$db = DB::get();

// Full Page
$body_class = 'full';

// Get Authorization Managers
$blogAuth = new REW\Backend\Auth\BlogsAuth(Settings::getInstance());

// Require permission to edit all associates
if (!$blogAuth->canManageComments($authuser)) {
    // Require permission to edit self
    if (!$blogAuth->canManageSelf($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to edit blog comments.')
        );
    } else {
        $sql_agent = "`agent` = :agent";
        $sql_agent_id = $authuser->info('id');
    }
// Filter By Agent
} else if (!empty($_GET['filter'])) {
    // Set Agent Filter
    $filterAgent = Backend_Agent::load($_GET['filter']);
    if (isset($filterAgent) && $filterAgent instanceof Backend_Agent) {
        $sql_agent = "`agent` = :agent";
        $sql_agent_id = $filterAgent->getId();
    }
}

    // Success
    $success = array();

    // Errors
    $errors = array();

    /**
     * Publish Blog Comment
     * @param int $id
     * @param array $success
     * @param array $errors
     * @uses $sql_agent
     * @return boolean True on success
     */
    $publishBlogComment = function ($id, &$success = array(), &$errors = array()) use ($db, $sql_agent, $sql_agent_id) {

    	// Require Blog Comment
        $params = ["id" => $id];
        if (!empty($sql_agent)) {
            $params["agent"] = $sql_agent_id;
        }
        try {
            $row = $db->fetch("SELECT * FROM `" . TABLE_BLOG_COMMENTS . "` WHERE `id` = :id" . (!empty($sql_agent) ? " AND " . $sql_agent : "") . ";", $params);
        } catch (PDOException $e) {}

    	if (!empty($row)) {
    		// Already Published
    		if ($row['published'] == 'true') return false;

    		// Publish Comment
            try {
    		    $db->prepare("UPDATE `" . TABLE_BLOG_COMMENTS . "` SET `published` = 'true' WHERE `id` = :id;")->execute(["id" => $row['id']]);

    			// Success
    			$success[] = __('The selected blog comment has successfully been published.');

    			// Blog Entry
                try {
                    $entry = $db->fetch("SELECT * FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `id` = :id;", ["id" => $row['entry']]);
                } catch (PDOException $e) {}

    			// Blog Author
                try {
                    $author = $db->fetch("SELECT * FROM `" . TABLE_BLOG_AUTHORS . "` WHERE `id` = :id;", ["id" => $entry['agent']]);
                } catch (PDOException $e) {}

    			// Comment Subscription Notification
                try {
                    foreach ($db->fetchAll("SELECT * FROM `" . TABLE_BLOG_COMMENTS . "` WHERE `entry` = :entry AND `published` = 'true' AND `subscribed` = 'true' GROUP BY `email`;", ["entry" => $entry['id']]) as $comment) {
                        // Don't send to author
                        if ($comment['email'] == $author['email']) continue;

                        // Create \PHPMailer\RewMailer
                        $mailer = new \PHPMailer\RewMailer();
                        $mailer->isHTML(true);

                        // Set Sender
                        $mailer->FromName = $author['first_name'] . ' ' . $author['last_name'];
                        $mailer->From = $author['email'];

                        // Set Recipient
                        $mailer->AddAddress($comment['email'], $comment['name']);

                        // Email Subject */
                        $mailer->Subject = htmlspecialchars_decode(__('Share your thoughts about %s', $entry['title']));

                        // Email Message
                        $mailer->Body    = '<p>Hi ' . $comment['name'] . ',</p>';
                        $mailer->Body   .= '<p>The blog entry that you have subscribed to titled "<a href="' . sprintf(URL_BLOG_ENTRY, $entry['link']) . '" target="_blank">' . Format::htmlspecialchars($entry['title']) . '</a>" has received a new comment!</p>';
                        $mailer->Body   .= '<p>Click the link below to view the blog entry and its new comment.</p>';
                        $mailer->Body   .= '<p><a href="' . sprintf(URL_BLOG_ENTRY, $entry['link']) . '" target="_blank">' . sprintf(URL_BLOG_ENTRY, $entry['link']) . '</a></p>';
                        $mailer->Body   .= '<p>Keep yourself in the loop. Share your thoughts and be heard!</p>';
                        $mailer->Body   .= '<p style="font-size: 80%;"><a href="' . sprintf(URL_BLOG_UNSUBSCRIBE, $entry['link'], $comment['email']) . '">Click here to stop receiving notifications about this blog entry.</a></p>';

                        // Send Email
                        if ($mailer->Send()) {

                            // Success
                            $success[] = __('Subscription Notification has been sent to') . ' <strong>' . $comment['email'] . '<strong>.';
                            return true;

                        // Mailer Error
                        } else {
                            $errors[] = __('Error sending Subscription Notification to') . ' <strong>' . $comment['email'] . '<strong>.';

                        }

                    }
                } catch (PDOException $e) {}

   			// Query Error
   			} catch (PDOException $e) {
   				$errors[] = __('The selected blog comment could not be published.');
   			}

        // Query Error
        } else {
            $errors[] = __('The selected blog comment could not be found.');
        }

        // Not Successful
        return false;
    };

    /**
     * Delete Blog Comment
     * @param int $id
     * @param array $success
     * @param array $errors
     * @uses $sql_agent
     * @return boolean True on success
     */
    $deleteBlogComment = function ($id, &$success, &$errors) use ($db, $sql_agent, $sql_agent_id) {

    	// Delete from Database
        $params = ["id" => $id];
        if (!empty($sql_agent)) {
            $params["agent"] = $sql_agent_id;
        }
        try {
            $db->prepare("DELETE FROM `" . TABLE_BLOG_COMMENTS . "` WHERE `id` = :id" . (!empty($sql_agent) ? " AND " . $sql_agent : "") . ";")->execute($params);

    		// Success
    		$success[] = __('The selected blog comment has successfully been deleted.');
    		return true;

    	// Query Error
        } catch (PDOException $e) {
    		$errors[] = __('The selected blog comment could not be deleted.');
    	}

        // Not Successful
        return false;
    };

    // Delete Blog Comment
    if (!empty($_POST['delete'])) {
        $deleteBlogComment($_POST['delete'], $success, $errors);
    }

    // Publish Blog Comment
    if (!empty($_GET['publish'])) {
        $publishBlogComment($_GET['publish'], $success, $errors);
    }

    // Group Acttion
    if (!empty($_POST['comments']) && is_array(($_POST['comments']))) {
        // Delete Comments
        $deleted = 0;
        if ($_POST['action'] === 'delete') {
            foreach ($_POST['comments'] as $comment) {
                if (is_numeric($comment)) {
                    $delete = $deleteBlogComment($comment);
                    if ($delete) {
                        $deleted++;
                    }
                }
            }
        }

        // Publish Comments
        $published = 0;
        if ($_POST['action'] === 'publish') {
            foreach ($_POST['comments'] as $comment) {
                if (is_numeric($comment)) {
                    $publish = $publishBlogComment($comment);
                    if ($publish) {
                        $published++;
                    }
                }
            }
        }

        // Comments Deleted
        if (!empty($deleted)) {
            $success[] = __('%s blog %s successfully been deleted.', Format::number($deleted), Format::plural($deleted, 'comments have', 'comment has') );
        }

        // Comments Published
        if (!empty($published)) {
            $success[] = __('%s blog %s successfully been published.', Format::number($published), Format::plural($published, 'comments have', 'comment has'));
        }

        // Save Notices
        $authuser->setNotices($success, $errors);

        // Redirect to Manage List
        header('Location: ?success' . (isset($_POST['filter']) ? '&filter=' . $_POST['filter'] : ''));
        exit;
    }

    // Row ID
    $_GET['edit'] = isset($_POST['edit']) ? $_POST['edit'] : $_GET['edit'];

    // Filter
    $_GET['filter'] = isset($_POST['filter']) ? $_POST['filter'] : $_GET['filter'];

    // Edit Row
    if (!empty($_GET['edit'])) {
        // Require Comment
        try {
            $params = ["id" => $_GET['edit']];
            if (!empty($sql_agent)) {
                $params["agent"] = $sql_agent_id;
            }
            $edit_comment = $db->fetch("SELECT * FROM `" . TABLE_BLOG_COMMENTS . "` WHERE `id` = :id" . (!empty($sql_agent) ? " AND " . $sql_agent : "") . ";", $params);
        } catch (PDOException $e) {}

	    if (!empty($edit_comment)) {
	        // Process Submit
	        if (isset($_GET['submit'])) {

                // Required Fields
                $required   = array();
                $required[] = array('value' => 'comment_name',  'title' => __('Name'));
                $required[] = array('value' => 'comment_email', 'title' => __('Email'));
                $required[] = array('value' => 'comment',       'title' => __('Comment'));

                // Process Required Fields
                foreach ($required as $require) {
                    if (empty($_POST[$require['value']])) {
                        $errors[] = __('%s is a required field.', $require['title']);
                    }
                }

                // Require Valid Email Address
                if (!Validate::email($_POST['comment_email'], true)) {
                    $errors[] = __('Please supply a valid email address.');
                }

                // ENUM Values
                $_POST['published']  = ($_POST['published']  == 'true') ? 'true' : 'false';
                $_POST['subscribed'] = ($_POST['subscribed'] == 'true') ? 'true' : 'false';

                // Check Errors
	            if (empty($errors)) {
	                try {
                        // Build UPDATE Query
                        $db->prepare("UPDATE `" . TABLE_BLOG_COMMENTS . "` SET "
                               . "`name`       = :name, "
                               . "`email`      = :email, "
                               . "`website`    = :website, "
                               . "`comment`    = :comment, "
                               . "`published`  = :published, "
                               . "`subscribed` = :subscribed, "
                               . "`timestamp_updated` = NOW()"
                               . " WHERE "
                               . "`id` = :id;")->execute([
                                "name"          => $_POST['comment_name'],
                                "email"         => $_POST['comment_email'],
                                "website"       => $_POST['comment_website'],
                                "comment"       => $_POST['comment'],
                                "published"     => $_POST['published'],
                                "subscribed"    => $_POST['subscribed'],
                                "id"            => $edit_comment['id']
                        ]);

                    	// Success
    	                $success[] = __('The selected blog comment has been updated.');

    	            // Query Error
    	            } catch (PDOException $e) {
    	                $errors[] = __('The selected blog comment could not be updated.');
    	            }
                }
            }

        // Comment not found
        } else {
            $errors[] = __('The selected blog comment could not be found.');
        }
    }

    // Filter Query
    $_GET['filter'] = !empty($_GET['filter']) ? $_GET['filter'] : 'pending';
    if ($_GET['filter'] == 'published') {
        $published = "`published` = 'true'";
    } elseif ($_GET['filter'] == 'pending') {
        $published = "`published` = 'false'";
    } else {
        $published = "(`published` = 'false' OR `published` = 'true')";
    }

    // Pagination
    // Cursor details
    $beforeCursor = $_GET['before'];
    $afterCursor = $_GET['after'];
    $primaryKey = 'id';
    $searchLimit = 10;
    $orderBy = 'timestamp_created';
    $sortDir = 'DESC';

    // Next
    if (!empty($afterCursor)) {
        $cursor = REW\Pagination\Cursor\After::decode($afterCursor);

        // Prev
    } else if (!empty($beforeCursor)) {
        $cursor = REW\Pagination\Cursor\Before::decode($beforeCursor);

        // First
    } else {
        $cursor = new REW\Pagination\Cursor($primaryKey, $searchLimit, $orderBy, $sortDir);

    }

    // Create pagination instance
    $pagination = new REW\Pagination\Pagination($cursor);

    $params = [];
    if (!empty($sql_agent)) {
        $params["agent"] = $sql_agent_id;
    }

    $limit = $pagination->getLimit();
    $limitQuery = $limit ? " LIMIT " . $limit : "";
    $order = $pagination->getOrder();
    $orderQuery = "";
    foreach ($order as $sort => $dir) {
        $sortString = "`" . $sort . "` ";
        // Need to CAST field `published` to a CHAR as it is an enum and can cause ordering issues
        if ($sort === 'published') $sortString = "CAST(`" . $sort . "` AS CHAR) ";
        $orderQuery .= $sortString . $dir . ", ";
    };
    $orderQuery = rtrim(" ORDER BY " . $orderQuery, ", ");
    $paginationWhere = $pagination->getWhere();
    $paramsPagination = $pagination->getParams();
    $params = array_merge($params, $paramsPagination);
    if (!empty($paginationWhere)) {
        $sql_agent = !empty($sql_agent) ? $sql_agent . " AND " . $paginationWhere : $paginationWhere;
    }
    $appendFilter = ($beforeCursor || $afterCursor) ? '&' : '';
    $appendFilter = !empty($_GET['filter']) ? 'filter=' . $_GET['filter'] : '';

    // Blog Comments
    $comments = array();
    try {
        foreach ($db->fetchAll("SELECT *, UNIX_TIMESTAMP(`timestamp_created`) AS `date` FROM `" . TABLE_BLOG_COMMENTS . "` WHERE " . $published . (!empty($sql_agent) ? " AND " . $sql_agent : "") . $orderQuery . $limitQuery, $params) as $manage_comment) {
            // Blog Entry
            try {
                $entry = $db->fetch("SELECT `agent`, `link`, `body`, `title` FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `id` = :id;", ["id" => $manage_comment['entry']]);
                $manage_comment['entry'] = $entry;
            } catch (PDOException $e) {}

            // Blog Author
            try {
                $author = $db->fetch("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `" . TABLE_BLOG_AUTHORS . "` WHERE `id` = :id;", ["id" => $entry['agent']]);
                $manage_comment['author'] = $author;
            } catch (PDOException $e) {}

            // Delete Link and Publish Link
            if (!empty($_GET['after'])) {
                $manage_comment['deleteLink'] = sprintf('?after=%s&%s', $_GET['after'], $appendFilter);
            } else if (!empty($_GET['before'])) {
                $manage_comment['deleteLink'] = sprintf('?before=%s&%s', $_GET['before'], $appendFilter);
            } else {
                $manage_comment['deleteLink'] = sprintf('?%s', $appendFilter);
            }
            if ($manage_comment['published'] == 'false') {
                $manage_comment['publishLink'] = str_replace('delete=', 'publish=', $manage_comment['deleteLink']);
            }

            // Add to Collection
            $comments[] = $manage_comment;
        }
    } catch (PDOException $e) {}

    $pagination->processResults($comments);

	// Count Un-Published Blog Comments
    try {
        $count_pending = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_COMMENTS . "` WHERE `published` = 'false'" . (!empty($sql_agent) ? " AND " . $sql_agent : "") . ";", $params);
        $count_pending = $count_pending['total'];
    } catch (PDOException $e) {}

	// Count Published Blog Comments
    try {
        $count_published =  $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_COMMENTS . "` WHERE `published` = 'true'" . (!empty($sql_agent) ? " AND " . $sql_agent : "") . ";", $params);
        $count_published = $count_published['total'];
    } catch (PDOException $e) {}

    // Pagination link URLs
    $nextLink = $pagination->getNextLink();
    $nextLink .= !empty($nextLink) ? $appendFilter : '';
    $prevLink = $pagination->getPrevLink();
    $prevLink .= !empty($prevLink) ? $appendFilter : '';
    $paginationLinks = ['nextLink' => $nextLink, 'prevLink' => $prevLink];
