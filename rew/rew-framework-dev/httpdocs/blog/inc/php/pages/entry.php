<?php

$backend_user = Auth::get();

// DB Connection
$db = DB::get('blog');

if ($backend_user->isValid()) {
    // Select Any Entry
    $query = $db->prepare("SELECT * FROM " . TABLE_BLOG_ENTRIES . " WHERE `link` = :entry" . (Settings::getInstance()->SETTINGS['agent'] != 1 ? " AND `agent` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']) : "") . ";");
} else {
    // Select Entry if Published Only
    $query = $db->prepare("SELECT * FROM " . TABLE_BLOG_ENTRIES . " WHERE `published` = 'true' AND `timestamp_published` < NOW() AND `link` = :entry" . (Settings::getInstance()->SETTINGS['agent'] != 1 ? " AND `agent` = " . $db->quote(Settings::getInstance()->SETTINGS['agent']) : "") . ";");
}

$query->execute(array('entry' => $_GET['entry']));
$entry = $query->fetch();
if (empty($entry)) {
    // Send 404 Header
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
} else {
    // Open Graph Images
    $og_images = array();
    $query = $db->prepare("SELECT `file` FROM `cms_uploads` WHERE `type` = 'blog:og:image' AND `row` = :id ORDER BY `order` ASC;");
    $query->execute(array('id' => $entry['id']));
    while ($og_image = $query->fetch()) {
        $og_images[] = Http_Host::getDomainUrl() . 'uploads/' . rawurlencode($og_image['file']);
    }
    $this->info('og:image', $og_images);

    // X-Pingback Header
    header('X-Pingback: ' . URL_BLOG . 'ping.php');

    try {
        // Select Author
        $query = $db->prepare("SELECT a.*, t.`time_diff`, t.`daylight_savings` FROM " . TABLE_BLOG_AUTHORS . " a LEFT JOIN " . LM_TABLE_TIMEZONES . " t ON a.`timezone` = t.`id` WHERE a.`id` = :author;");
        $query->execute(array('author' => $entry['agent']));
        $author = $query->fetch();
        if (!empty($query)) {
            $author['name'] = $author['first_name'] . ' ' . $author['last_name'];
            $author['link'] = Format::slugify($author['name']);
        }

    // Database error
    } catch (PDOException $e) {
        Log::error($e);
    }

    // Unsubscribe Comment Notifications
    if (!empty($_GET['unsubscribe'])) {
        try {
            $update = $db->prepare("UPDATE `" . TABLE_BLOG_COMMENTS . "` SET `subscribed` = 'false' WHERE `email` = :email;");
            $update->execute(array('email' => $_GET['unsubscribe']));
            $message = __('You have successfully been unsubscribed from receiving comment notifications for this blog entry.');
        } catch (PDOException $e) {
            $errors[] = __('You could not be unsubscribed from receiving comment notifications.');
            Log::error($e);
        }
    }

    // Backend User
    $backend_user = Auth::get();

    // Show Form
    $show_form = true;

    // Submit Comment
    if (isset($_GET['comment'])) {
        // Errors
        $errors = array();

        // Prepend HTTP:// (If not present)
        $_POST['comment_website'] = !empty($_POST['comment_website']) && !preg_match("#^(?:f|ht)tps?://#i", $_POST['comment_website']) ? 'http://' . $_POST['comment_website'] : $_POST['comment_website'];

        // Backend User
        if ($backend_user->isValid()) {
            // Set Fields
            $_POST['comment_name']  = $backend_user->getName();
            $_POST['comment_email'] = $backend_user->info('email');

            // Agent ID
            $agent_id = $backend_user->info('id');

            // Check Required Fields
            if (empty($_POST['comment'])) {
                $errors[] = __('You cannot leave an empty comment.');
            }

            // Auto Publish
            $published = 'true';
        } else {
            // Test Honeypot Variables
            $fake = false;
            if (!empty($_POST['email'])         || !isset($_POST['email'])) {
                $fake = true;
            }
            if (!empty($_POST['first_name'])    || !isset($_POST['first_name'])) {
                $fake = true;
            }
            if (!empty($_POST['last_name'])     || !isset($_POST['last_name'])) {
                $fake = true;
            }

            // Require Spam Check
            require_once Settings::getInstance()->DIRS['BACKEND'] . 'inc/php/routine.spam-stop.php';
            $spam = checkForSpam($package);
            $isSpam = (!empty($spam) || empty($package['is_browser']) || !empty($fake)) ? true : false;
            if (!empty($isSpam)) {
                $errors[] = __('Your comment submission was detected as SPAM.');
            }

            // Check Required Fields
            if (empty($_POST['comment_name']) || empty($_POST['comment'])) {
                $errors[] = __('One or more required fields were left blank.');
            }

            // Email Check
            if (!Validate::email($_POST['comment_email'])) {
                $errors[] = __('You must supply a valid email address.');
            }

            // Require CAPTCHA Code
            if ($blog_settings['captcha'] == 't') {
                $_POST['captcha'] = strtoupper($_POST['captcha']);
                if (md5($_POST['captcha']) != $_SESSION['captcha'] || empty($_SESSION['captcha']) || !isset($_SESSION['captcha'])) {
                    $errors[] = __('Invalid security code, please try again.');
                }
                unset($_SESSION['captcha']);
            }
        }

        try {
            // Check Duplicate Comment
            $check = $db->prepare("SELECT * FROM " . TABLE_BLOG_COMMENTS . " WHERE `agent` = :agent AND `entry` = :entry AND `name` = :name AND `email` = :email AND `comment` = :comment;");
            $check->execute(array('agent' => $author['id'], 'entry' => $entry['id'], 'name' => $_POST['comment_name'], 'email' => $_POST['comment_email'], 'comment' => $_POST['comment']));
            if (($check = $check->fetchColumn())) {
                if ($check == 'true') {
                    $errors[] = __('This exact comment has already been posted.');
                } else {
                    $errors[] = __('This exact comment has already been posted and is currently pending approval.');
                }
            }

        // Database error
        } catch (PDOException $e) {
            Log::error($e);
        }

        // Success
        if (empty($errors)) {
            // Agent ID
            $agent_id   = !empty($agent_id)     ? $agent_id     : 'NULL';
            $published  = !empty($published)    ? $published    : 'false';
            $website    = (!empty($_POST['comment_website']) && $_POST['comment_website'] != 'http://') ? $_POST['comment_website'] : '';

            try {
                // Prepare INSERT Query
                $insert = $db->prepare("INSERT INTO " . TABLE_BLOG_COMMENTS . " SET "
                    . "`agent`				= :agent,"
                    . "`entry`				= :entry,"
                    . "`name`				= :name,"
                    . "`email`				= :email,"
                    . "`comment`			= :comment,"
                    . "`website`			= :website,"
                    . "`ip_address`			= :ip_address,"
                    . "`published`			= :published,"
                    . "`timestamp_created`	= NOW()"
                . ";");

                // Execute INSERT Query
                $insert = $insert->execute(array(
                    'agent'     => $author['id'],
                    'entry'     => $entry['id'],
                    'name'      => $_POST['comment_name'],
                    'email'     => $_POST['comment_email'],
                    'comment'   => $_POST['comment'],
                    'website'   => $website,
                    'ip_address'=> $_SERVER['REMOTE_ADDR'],
                    'published' => $published
                ));

                // Success Message
                $message = ($published === 'true') ? __('Your blog comment has successfully been posted.') // Comment is from auth user (published)
                    : __('Your blog comment has successfully been sent and is now pending approval.'); // Comment is from a visitor (pending)

                // Notify Author
                if (!empty($author)) {
                    $notify = $author;

                // Notify Admin
                } else {
                    $notify = $db->fetch("SELECT * FROM `" . TABLE_BLOG_AUTHORS . "` WHERE `id` = 1;");
                }

                // Send Comment Notification to Author
                if (!empty($notify) && $notify['email'] != $_POST['comment_email']) {
                    // Create Mailer
                    $mailer = new \PHPMailer\RewMailer();
                    $mailer->IsHTML(true);

                    // Configure Sender
                    $mailer->FromName = $notify['first_name'] . ' ' . $notify['last_name'];
                    $mailer->From    = Settings::getInstance()->SETTINGS['EMAIL_NOREPLY'];

                    // Add Email Recipient
                    $mailer->AddAddress($notify['email'], $notify['first_name'] . ' ' . $notify['last_name']);

                    // Email Subject
                    $mailer->Subject = htmlspecialchars_decode('New Blog Comment - ' . $entry['title']);

                    // Email Message (HTML)
                    $mailer->Body = '';
                    $mailer->Body .= '<p>Your blog entry "<a href="' . sprintf(URL_BLOG_ENTRY, $entry['link']) . '" target="_blank">' . Format::htmlspecialchars($entry['title']) . '</a>" has a new comment.</p>';
                    $mailer->Body .= '<p>You can manage your blog\'s comment queue from <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . '" target="_blank">' . Settings::getInstance()->URLS['URL_BACKEND'] . '</a>.</p>';
                    $mailer->Body .= '<p>';
                    $mailer->Body .= '<strong>Entry Title:</strong> ' . Format::htmlspecialchars($entry['title']) . '<br />';
                    $mailer->Body .= '<strong>Entry URL:</strong> ' . '<a href="' . sprintf(URL_BLOG_ENTRY, $entry['link']) . '" target="_blank">' . sprintf(URL_BLOG_ENTRY, $entry['link']) . '</a>' . '<br />';
                    $mailer->Body .= '</p>';
                    $mailer->Body .= '<p>';
                    $mailer->Body .= '<strong>Name:</strong> ' . Format::htmlspecialchars($_POST['comment_name']) . '<br />';
                    $mailer->Body .= '<strong>Email:</strong> ' . Format::htmlspecialchars($_POST['comment_email']) . '<br />';
                    if (!empty($_POST['comment_website'])) {
                        $mailer->Body .= '<strong>Website:</strong> <a href="' . Format::htmlspecialchars($_POST['comment_website']) . '" target="_blank">' . Format::htmlspecialchars($_POST['comment_website']) . '</a>' . '<br />';
                    }
                    $mailer->Body .= '<strong>Comment:</strong> ' . Format::htmlspecialchars($_POST['comment']) . '<br />';
                    $mailer->Body .= '<strong>IP Address:</strong> ' . $_SERVER['REMOTE_ADDR'] . '<br />';
                    $mailer->Body .= '</p>';

                    // Email Message (Pain Text)
                    $mailer->AltBody = '';
                    $mailer->AltBody .= 'Your blog entry "' . Format::htmlspecialchars($entry['title']) . '" has a new comment.' . "\n\n";
                    $mailer->AltBody .= 'You can manage your blog\'s comment queue from ' . Settings::getInstance()->URLS['URL_BACKEND'] . '.' . "\n\n";
                    $mailer->AltBody .= 'Entry Title: ' . Format::htmlspecialchars($entry['title']) . "\n";
                    $mailer->AltBody .= 'Entry URL: ' . sprintf(URL_BLOG_ENTRY, $entry['link']) . "\n\n";
                    $mailer->AltBody .= 'Name: ' . Format::htmlspecialchars($_POST['comment_name']) . "\n";
                    $mailer->AltBody .= 'Email: ' . Format::htmlspecialchars($_POST['comment_email']) . "\n";
                    if (!empty($_POST['comment_website'])) {
                        $mailer->AltBody .= 'Website: ' . Format::htmlspecialchars($_POST['comment_website']) . "\n";
                    }
                    $mailer->AltBody .= 'Comment: ' . Format::htmlspecialchars($_POST['comment']) . "\n";
                    $mailer->AltBody .= 'IP Address: ' . $_SERVER['REMOTE_ADDR'] . "\n";

                    // Send Email
                    $mailer->Send();
                }

                // Unset Request Variables
                unset($_POST['comment_name'], $_POST['comment_email'], $_POST['comment_website'], $_POST['comment']);

            // Database error
            } catch (PDOException $e) {
                $errors[] = __('An error has occurred. Your blog comment could not be saved.');
                Log::error($e);
            }
        }
    }

    try {
        // Count Comments & Pingbacks
        $query = $db->prepare("SELECT SUM(`total`) AS `total` FROM ((SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_PINGS . "` WHERE `published` = 'true' AND `entry` = :entry) UNION ALL (SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_COMMENTS . "` WHERE `published` = 'true' AND `entry` = :entry)) AS `t`");
        $query->execute(array('entry' => $entry['id']));
        $count_comments = $query->fetch();

        // Check Comments & Pingbacks
        if (!empty($count_comments['total'])) {
            $comments = $db->prepare("(SELECT `id`, `agent`, `entry`, `website`, `timestamp_created`, CONCAT('[...]', `excerpt`, '[...]') AS `comment`, `page_title` AS `name` FROM `" . TABLE_BLOG_PINGS . "` WHERE `published` = 'true' AND `entry` = :entry) UNION ALL (SELECT `id`, `agent`, `entry`, `website`, `timestamp_created`, `comment`, `name` FROM `" . TABLE_BLOG_COMMENTS . "` WHERE `published` = 'true' AND `entry` = :entry) ORDER BY `timestamp_created` ASC");
            $comments->execute(array('entry' => $entry['id']));
        }

    // Database error
    } catch (PDOException $e) {
        unset($comments);
        Log::error($e);
    }

    try {
        // Blog Tags
        if (!empty($entry['tags'])) {
            $tags = array();
            $find_tag = $db->prepare("SELECT COUNT(`be`.`id`) AS `total`, `bt`.`link`, `bt`.`title` FROM `" . TABLE_BLOG_TAGS . "` `bt`, `" . TABLE_BLOG_ENTRIES . "` `be` WHERE `bt`.`title` LIKE :tag AND FIND_IN_SET(`bt`.`title`, `be`.`tags`) AND `be`.`published` = 'true' AND `be`.`timestamp_published` < NOW() GROUP BY `bt`.`title` ORDER BY `total` DESC");
            foreach (explode(',', $entry['tags']) as $tag) {
                if (empty($tag)) {
                    continue;
                }
                $find_tag->execute(array('tag' => '%' . $tag . '%'));
                $tag = $find_tag->fetch();
                if (!empty($tag)) {
                    $tags[] = $tag;
                }
            }
        }

    // Database error
    } catch (PDOException $e) {
        Log::error($e);
    }

    // Ensure Related Links Have Protocol
    $pattern = "#^(?:f|ht)tps?://#i";
    $entry['link_url1'] = !empty($entry['link_url1']) && !preg_match($pattern, $entry['link_url1']) ? 'http://' . $entry['link_url1'] : $entry['link_url1'];
    $entry['link_url2'] = !empty($entry['link_url2']) && !preg_match($pattern, $entry['link_url2']) ? 'http://' . $entry['link_url2'] : $entry['link_url2'];
    $entry['link_url3'] = !empty($entry['link_url3']) && !preg_match($pattern, $entry['link_url3']) ? 'http://' . $entry['link_url3'] : $entry['link_url3'];

    // Entry Meta Information
    $page_title = $entry['title'];
    $meta_desc  = !empty($entry['meta_tag_desc']) ? $entry['meta_tag_desc'] : Format::truncate($entry['body'], 170);

    // Increment Views
    $views = $_SESSION['blog-views'];
    $views = is_array($views) ? $views : array();
    if (!in_array($entry['id'], $views)) {
        $update = $db->prepare("UPDATE `" . TABLE_BLOG_ENTRIES . "` SET `views` = `views` + 1 WHERE `id` = :entry;");
        $update->execute(array('entry' => $entry['id']));
        $views[] = $entry['id'];
        $_SESSION['blog-views'] = $views;
    }

    // Blog Entry Snippets
    preg_match_all("!(#([a-zA-Z0-9_-]+)#)!", $entry['body'], $matches);
    if (!empty($matches)) {
        foreach ($matches[1] as $match) {
            $snippet = rew_snippet($match, false);
            $entry['body'] = str_replace($match, $snippet, $entry['body']);
        }
    }
}
