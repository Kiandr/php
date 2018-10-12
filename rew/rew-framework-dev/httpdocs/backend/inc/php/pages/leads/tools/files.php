<?php

// Full Size
$body_class = 'full';

// Get Authorization Manager
$settings = Settings::getInstance();
$leadsAuth = new REW\Backend\Auth\LeadsAuth($settings);

// Authorized to Export All Leads
if (!$leadsAuth->canViewFiles($authuser)) {
    // Authorized to Export Own Leads
    if (!$leadsAuth->canManageOwnFiles($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to manage files.')
        );
    }
    $agent_id = $authuser->info('id');
} else {
    // Get Agent Filter
    $_GET['personal'] = isset($_POST['personal'])
        ? $_POST['personal'] : $_GET['personal'];
    if (isset($_GET['personal'])) {
        $agent_id = $authuser->info('id');
    }
}

    // Allowed Extensions
    $allowed_exts = array(
        'jpg', 'jpeg', 'png', 'gif', // Images
        'doc', 'docx', 'rtf', 'pdf', 'txt', 'xls', 'xlsx', 'odt', // Documents
        'pptx', 'ppt', 'pps', 'ppsx', 'odp', 'csv', 'log' // More
    );

    // Check Storage Limit
    $checkLimit = function (&$max_exceed = false, $addSize = 0) {

        // File Size Limit (in MB)
        $max_limit  = 100;
        $max_exceed = false;
        $query = "SELECT ROUND((SUM(`size`) + " . intval($addSize) . ") / 1024 /1024, 2) AS `size` FROM `cms_files` WHERE `category` NOT LIKE 'email' OR `category` IS NULL;";
        if ($result = mysql_query($query)) {
            $check = mysql_fetch_assoc($result);
            if (!empty($check['size']) && $check['size'] >= $max_limit) {
                $max_exceed = true;
            }
        }

        // Storage Usage
        return $check['size'] . ' MB (' . floor($check['size'] / $max_limit * 100) . '%) of ' . $max_limit . ' MB Used';
    };

    // Check Usage
    $usage = $checkLimit ($max_exceed);

    // Update Record
    $updateUpload = function (&$file) use ($authuser, $leadsAuth) {

        // Can Edit or Delete File
        $file['can_edit'] = $file['can_delete'] = $leadsAuth->canManageLeads($authuser)
        || ($authuser->info('id') === $file['agent_id']);

        // Can Share File
        $file['can_share'] = $leadsAuth->canManageLeads($authuser) && $file['agent_id'] == 1;

        // URL to File
        $file['url'] = Settings::getInstance()->URLS['URL'] . 'files/' . $file['id'] . '/' . $file['name'];

        // File Thumbnail
        $file['thumb'] = '/thumbs/60x60/img/404.gif';

        // File Extension
        $ext = substr($file['name'], strrpos($file['name'], '.') + 1);

        // Image File
        if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
            $file['thumb'] = '/backend/img/icons/file-image.svg';

        // Word Document
        } elseif (in_array($ext, array('doc', 'docx', 'rtf'))) {
            $file['thumb'] = '/backend/img/icons/file-word.png';
            // Excel Spreadsheet
        } elseif (in_array($ext, array('xls', 'xlsx', 'csv'))) {
            $file['thumb'] = '/backend/img/icons/file-excel.png';
            // Power Point Presentation
        } elseif (in_array($ext, array('pptx', 'ppt', 'pps', 'ppsx'))) {
            $file['thumb'] = '/backend/img/icons/file-powerpoint.png';
            // Open Office Format
        } elseif (in_array($ext, array('odt', 'odp'))) {
            $file['thumb'] = '/backend/img/icons/file-openoffice.png';
            // Adobe PDF
        } elseif (in_array($ext, array('pdf'))) {
            $file['thumb'] = '/backend/img/icons/file-pdf.png';
            // Text File
        } elseif (in_array($ext, array('txt', 'log'))) {
            $file['thumb'] = '/backend/img/icons/file.png';
        }
    };

    // Get Upload
    $getUpload = function ($id) use ($agent_id, $updateUpload) {

        // Get File from Database
        $query = "SELECT `f`.`id`, `f`.`name`, `f`.`type`, `f`.`size`, `f`.`views`, `f`.`share`, `f`.`password`"
            . ", UNIX_TIMESTAMP(`f`.`timestamp`) AS `date`"
            . ", `a`.`id` AS `agent_id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent`"
            . " FROM `cms_files` `f` LEFT JOIN `agents` `a` ON `f`.`agent` = `a`.`id`"
            . " WHERE `f`.`id` = '" . mysql_real_escape_string($id) . "'"
            .  (!empty($agent_id) ? " AND (`f`.`share` = 'true' OR `f`.`agent` = '" . $agent_id . "')" : "")
            . ";";
        if ($result = mysql_query($query)) {
            $upload = mysql_fetch_assoc($result);
            if (!empty($upload)) {
                // Get Upload Details
                $updateUpload ($upload);

                // Return Upload
                return $upload;
            }
        }

        // No Record
        return false;
    };

    // Generate HTML for Edit Form
    $generateFormHTML = function ($file) use ($authuser, $leadsAuth) {

        // Must be editable
        if (empty($file['can_edit'])) {
            return;
        }

    ?>
    <form data-upload="<?=$file['id']; ?>">
        <input type="hidden" name="edit" value="<?=$file['id']; ?>">
        <div class="mar8">
            <img src="<?=$file['thumb']; ?>" alt="">
            <a href="<?=$file['url']; ?>" target="_blank" style="vertical-align: text-bottom;"><?=Format::htmlspecialchars($file['name']); ?></a>
        </div>
        <div class="-pad8">
            <?php if ($leadsAuth->canViewFiles($authuser)) { ?>
                <div class="mar8"><b><?= __('Uploaded By:'); ?></b> <a href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>agents/agent/summary/?id=<?=$file['agent_id']; ?>"><?=$file['agent']; ?></a></div>
            <?php } elseif ($authuser->info('id') != $file['agent_id']) { ?>
                <div class="mar8"><b><?= __('Uploaded By:'); ?></b> <?=$file['agent']; ?></div>
            <?php } ?>
            <div class="mar8"><b><?= __('Uploaded On:'); ?></b> <?=date('l, F jS Y \@ g:ia', $file['date']); ?></div>
            <div class="mar8"><b><?= __('File Size:'); ?></b> <?=Format::filesize($file['size']); ?>, <b># <?= __('of Downloads:'); ?></b> <?=Format::number($file['views']); ?></div>
        </div>
        <div class="field">
            <label class="field__label"><?= __('Link to Download'); ?></label>
            <input class="w1/1 -marB8" value="<?=$file['url']; ?>" readonly>
            <label class="hint">
                <a data-clipboard-text="<?=Format::htmlspecialchars($file['url']); ?>" class="btn" style="font-size: 14px;"><?= __('Copy URL to Clipboard'); ?></a>
            </label>
        </div>
        <div class="field">
            <label class="field__label"><?= __('Filename'); ?></label>
            <input class="w1/1" name="name" value="<?=$file['name']; ?>">
        </div>
        <div class="field">
            <label><input type="checkbox" class="password"<?=(!empty($file['password']) ? ' checked' : ''); ?>> <?= __('Password Protected'); ?></label>
            <input type="password" name="password" value="<?=Format::htmlspecialchars($file['password']); ?>" placeholder="Enter Password..."<?=(empty($file['password']) ? ' class="hidden" disabled' : ''); ?> required>
        </div>
        <?php if (!empty($file['can_share'])) { ?>
            <div class="field">
                <label><input type="checkbox" name="share" value="true"<?=($file['share'] === 'true' ? ' checked' : ''); ?>> <?= __('Share with Agents'); ?></label>
            </div>
        <?php } ?>
    </form>
    <?php
    };

    // Generate HTML for Table Row
    $generateRowHTML = function ($file) use ($authuser, $leadsAuth) {

    ?>
    <li data-id='<?=$file['id']; ?>' data-upload="<?=htmlspecialchars(json_encode(array('id' => $file['id'], 'name' => $file['name']))); ?>" class="nodes__branch">

        <div class="nodes__wrap">

            <div class="nodes__toggle">
                <?php if (!empty($file['can_delete'])) { ?>
                <input type="checkbox" name="files[]" value="<?=$file['id']; ?>" class="check">
                <?php } ?>
            </div>

            <div class="article">
                <div class="article__body">
                    <div class="article__thumb thumb thumb--medium">
                        <img src="<?=$file['thumb']; ?>" alt="">
                    </div>
                    <div class="article__content">
                        <?php if (!empty($file['can_edit'])) { ?>
                        <a data-action="edit" href="javascript:void(0);" class="text text--strong">
                            <?=$file['name']; ?>
                            <?=($file['share'] === 'true') ? '(Shared)' : ''; ?>
                            <?php if (!empty($file['password'])) { ?>
                            <img class="lock" src="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/ico-lock.png" alt="" title="<?= __('Password Protected'); ?>">
                            <?php } ?>
                        </a>
                        <?php } else { ?>
                        <a class="text text--strong edit" href="<?=$file['url']; ?>" target="_blank">
                            <?=$file['name']; ?>
                            <?=($file['share'] === 'true') ? '(Shared)' : ''; ?>
                            <?php if (!empty($file['password'])) { ?>
                            <img class="lock" src="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/ico-lock.png" alt="" title="<?= __('Password Protected'); ?>">
                            <?php } ?>
                        </a>
                        <?php } ?>
                        <div class="text text--mute">
                            <?php if ($leadsAuth->canViewFiles($authuser)) {
                                echo '<a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/agent/summary/?id=' . $file['agent_id'] . '">' . $file['agent'] . '</a>';
                            } ?> &bull;
                            <time datetime="<?=date('c', $file['date']); ?>" title="<?=date('l, F jS Y \@ g:ia', $file['date']); ?>"><?=Format::dateRelative($file['date']); ?></time> &bull;
                            <?=Format::filesize($file['size']); ?>
                        </div>
                    </div>
                    <div class="nodes__actions">
                        <?php if (!empty($file['can_delete'])) { ?>
                        <button type="button" class="btn btn--ghost btn--ico" title="<?= __('Delete this file'); ?>" data-action="delete">
                            <svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg>
                        </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </li>
    <?php
    };

    // Upload File
    if (isset($_GET['upload'])) {
        // JSON Data
        $json = array();

        // Max File Size (10M)
        $size = 10 * 1024 * 1024;

        try {
            // Get File Uploader
            $uploader = Backend_Uploader::get();

            // Handle Upload
            $uploader->handleUpload();

            // File Details
            $filename = $uploader->getName();
            $filesize = $uploader->getSize();
            $filetype = $uploader->getType();

            // File Contents
            $contents = $uploader->read();

            // Storage Usage
            $json['usage'] = $checkLimit ($json['max_exceed'], isset($_GET['email']) ? 0 : $filesize);
            if (!empty($json['max_exceed'])) {
                unset($json['max_exceed'], $json['usage']);
                throw new Exception(__('Storage limit has been exceeded. This file is too large to upload.'));
            }

            // Build INSERT Query
            $query = "INSERT INTO `cms_files` SET "
                . "`agent` = '" . $authuser->info('id') . "', "
                . "`name`  = '" . mysql_real_escape_string($filename) . "', "
                . "`size`  = '" . mysql_real_escape_string($filesize) . "', "
                . "`type`  = '" . mysql_real_escape_string($filetype) . "', "
                . "`data`  = '" . mysql_real_escape_string($contents) . "' ,"
                . (isset($_GET['email']) ? "`category`  = 'email' ," : "")
                . "`timestamp` = NOW();";

            // Save to Database
            if (mysql_query($query)) {
                // Insert ID
                $insert_id = mysql_insert_id();

                // Success
                $json['success'] = true;

                // Get Upload
                $upload = $getUpload ($insert_id);
                if (!empty($upload)) {
                    // Row HTML
                    ob_start();
                    $generateRowHTML($upload);
                    $json['upload'] = ob_get_clean();
                }

            // Query Error
            } else {
                $json['error'] = __('An error occurred while attempting to save your upload.');
            }

        // Error Occurred
        } catch (Exception $e) {
            $json['error'] = $e->getMessage();
        }

        // Return JSON Data
        header('Content-type: application/json');
        die(json_encode($json));
    }

    // Edit File
    if (isset($_POST['edit'])) {
        // JSON Data
        $json = array();

        // Locate File
        $upload = $getUpload ($_POST['edit']);
        if (!empty($upload)) {
            // Able to Edit
            if (!empty($upload['can_edit'])) {
                // Save Changes
                if (isset($_GET['save'])) {
                    // Require Filename
                    if (empty($_POST['name'])) {
                        $json['error'] = __('Filename is a required field.');
                    } else {
                        // Update File
                        $query = "UPDATE `cms_files` SET "
                            . "`name`	= '" . mysql_real_escape_string($_POST['name']) . "'"
                            // Password Protected
                            . (!empty($_POST['password'])
                                ? ", `password`	= '" . mysql_real_escape_string($_POST['password']) . "'"
                                : ", `password`	= NULL"
                            )
                            // Share with Agents
                            . (!empty($upload['can_share']) && !empty($_POST['share'])
                                ? ", `share`	= 'true'"
                                : ", `share`	= 'false'"
                            )
                        . " WHERE `id` = '" . $upload['id'] . "';";
                        if (mysql_query($query)) {
                            // Success
                            $json['success'] = true;

                            // Get Updated Row
                            $upload = $getUpload ($upload['id']);
                            if (!empty($upload)) {
                                // Row HTML
                                ob_start();
                                $generateRowHTML($upload);
                                $json['upload'] = ob_get_clean();
                            }

                        // Query Error
                        } else {
                            $json['error'] = __('An error occurred while saving changes.');
                        }
                    }
                } else {
                    // Success
                    $json['success'] = true;

                    // Form HTML
                    ob_start();
                    $generateFormHTML($upload);
                    $json['form'] = ob_get_clean();
                }

            // Permission Denied
            } else {
                $json['error'] = __('You do not have permission to edit this file.');
            }

        // File Not Found
        } else {
            $json['error'] = __('The selected file could not be found.');
        }

        // Return JSON Data
        header('Content-type: application/json');
        die(json_encode($json));
    }

    // Delete File
    if (isset($_GET['delete'])) {
        // JSON Data
        $json = array();

        // Locate File
        $query = "SELECT `id` FROM `cms_files` WHERE `id` = '" . mysql_real_escape_string($_POST['upload']) . "'" . (!empty($agent_id) ? " AND `agent` = '" . $agent_id . "'" : "") .";";
        if ($result = mysql_query($query)) {
            $upload = mysql_fetch_assoc($result);
            if (!empty($upload)) {
                // Delete File
                $query = "DELETE FROM `cms_files` WHERE `id` = '" . $upload['id'] . "';";
                if (mysql_query($query)) {
                    // Success
                    $json['success'] = true;

                    // Storage Usage
                    $json['usage'] = $checkLimit ($json['max_exceed']);

                // Query Error
                } else {
                    $json['error'] = __('An error occurred while removing file.');
                }

            // File Not Found
            } else {
                $json['error'] = __('The selected file could not be found.');
            }

        // Query Error
        } else {
            $json['error'] = __('An error occurred while locating file.');
        }

        // Return JSON Data
        header('Content-type: application/json');
        die(json_encode($json));
    }

    // Files
    $files = array();

    // Query String
    list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
    parse_str($query, $query_string);

    // Sort Order
    $_GET['sort']  = ($_GET['sort'] === 'ASC') ? 'ASC' : 'DESC';
    $_GET['order'] = !empty($_GET['order']) ? $_GET['order'] : 'date';
    switch ($_GET['order']) {
        case 'agent':
            $sql_order = " ORDER BY `agent` " . $_GET['sort'];
            break;
        case 'type':
            $sql_order = " ORDER BY `f`.`type` " . $_GET['sort'];
            break;
        case 'size':
            $sql_order = " ORDER BY `f`.`size` " . $_GET['sort'];
            break;
        case 'name':
            $sql_order = " ORDER BY `f`.`name` " . $_GET['sort'];
            break;
        case 'views':
            $sql_order = " ORDER BY `f`.`views` " . $_GET['sort'];
            break;
        case 'date':
        default:
            $sql_order = " ORDER BY `date` " . $_GET['sort'];
            break;
    }

    // SQL Where
    $sql_where = array();

    // Filter by Agent
    if (!empty($agent_id)) {
        $sql_where[] = "(`f`.`agent` = '" . $agent_id . "' OR `f`.`share` = 'true')";
    }

    // Count Files
    $query = "SELECT COUNT(`id`) AS `total` FROM `cms_files` `f`"
        . (!empty($sql_where) ? " WHERE " . implode(" AND ", $sql_where) : "")
        . (!empty($sql_where) ? " AND " : " WHERE " ) . "(`category` NOT LIKE 'email' OR `category` IS NULL)"
    . ";";
    if ($result = mysql_query($query)) {
        // Check Count
        $count = mysql_fetch_assoc($result);
        if (!empty($count['total'])) {
            // SQL Limit
            $page_limit = 25;
            if ($count['total'] > $page_limit) {
                $limitvalue = (($_GET['p'] - 1) * $page_limit);
                $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
                $sql_limit  = " LIMIT " . $limitvalue . ", " . $page_limit;
            }

            // Pagination
            $pagination = generate_pagination($count['total'], $_GET['p'], $page_limit, $query_string);

            // Uploaded Files
            $query = "SELECT `f`.`id`, `f`.`name`, `f`.`type`, `f`.`size`, `f`.`views`, `f`.`share`, `f`.`password`"
                . ", UNIX_TIMESTAMP(`f`.`timestamp`) AS `date`"
                . ", `a`.`id` AS `agent_id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent`"
                . " FROM `cms_files` `f` LEFT JOIN `agents` `a` ON `f`.`agent` = `a`.`id`"
                . (!empty($sql_where) ? " WHERE " . implode(" AND ", $sql_where) : "")
                . (!empty($sql_where) ? " AND " : " WHERE " ) . "(`category` NOT LIKE 'email' OR `category` IS NULL)"
                . $sql_order
                . $sql_limit
            . ";";
            if ($result = mysql_query($query)) {
                while ($upload = mysql_fetch_assoc($result)) {
                    // Get Upload Details
                    $updateUpload ($upload);

                    // Add to Files
                    $files[] = $upload;
                }

            // Query Error
            } else {
                $errors[] = __('An error occurred while loading files.');
            }
        }

    // Query Error
    } else {
        $errors[] = __('An error occurred while counting files.');
    }

    if (isset($_GET['email'])) {
        // Count Email Attachment Files
        $query = "SELECT COUNT(`id`) AS `total` FROM `cms_files` `f`"
            . (!empty($sql_where) ? " WHERE " . implode(" AND ", $sql_where) : "")
            . (!empty($sql_where) ? " AND " : " WHERE " ) . "`category` LIKE 'email'"
        . ";";
        if ($result = mysql_query($query)) {
            // Check Count
            $count = mysql_fetch_assoc($result);
            if (!empty($count['total'])) {
                // SQL Limit
                $page_limit = 25;
                if ($count['total'] > $page_limit) {
                    $limitvalue = (($_GET['p'] - 1) * $page_limit);
                    $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
                    $sql_limit  = " LIMIT " . $limitvalue . ", " . $page_limit;
                }

                // Pagination
                $attachment_pagination = generate_pagination($count['total'], $_GET['p'], $page_limit, $query_string);

                // Uploaded Files
                $query = "SELECT `f`.`id`, `f`.`name`, `f`.`type`, `f`.`size`, `f`.`views`, `f`.`share`, `f`.`password`"
                    . ", UNIX_TIMESTAMP(`f`.`timestamp`) AS `date`"
                    . ", `a`.`id` AS `agent_id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent`"
                    . " FROM `cms_files` `f` LEFT JOIN `agents` `a` ON `f`.`agent` = `a`.`id`"
                    . (!empty($sql_where) ? " WHERE " . implode(" AND ", $sql_where) : "")
                    . (!empty($sql_where) ? " AND " : " WHERE " ) . "`category` LIKE 'email'"
                    . $sql_order
                    . $sql_limit
                . ";";
                if ($result = mysql_query($query)) {
                    while ($upload = mysql_fetch_assoc($result)) {
                        // Get Upload Details
                        $updateUpload ($upload);

                        // Add to Files
                        $email_files[] = $upload;
                    }

                // Query Error
                } else {
                    $errors[] = __('An error occurred while loading email attachment files.');
                }
            }

            // Total File Count Email Attachment And Regular Files
            $query = "SELECT COUNT(`id`) AS `total` FROM `cms_files` `f`"
                . (!empty($sql_where) ? " WHERE " . implode(" AND ", $sql_where) : "")
            . ";";
            $result = mysql_query($query);
            $count = mysql_fetch_assoc($result);

        // Query Error
        } else {
            $errors[] = __('An error occurred while counting email attachment files.');
        }
    }

    // Sort Direction
    $url_sort = ($_GET['sort'] == 'DESC' ? 'ASC' : 'DESC');
