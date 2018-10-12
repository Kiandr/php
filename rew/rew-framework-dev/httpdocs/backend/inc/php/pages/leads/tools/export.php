<?php

// Full Page
$body_class = 'full';

// Get Authorization Manager
$settings = Settings::getInstance();
$leadsAuth = new REW\Backend\Auth\LeadsAuth($settings);

// Authorized to Export All Leads
if (!$leadsAuth->canExportLeads($authuser)) {
    // Authorized to Export Own Leads
    if (!$leadsAuth->canExportOwn($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            'You do not have permission to export leads.'
        );
    }
    $agent_id = $authuser->info('id');
} else {
    if (isset($_POST['agent'])) {
        $agent_id = $_POST['agent'];
    }

    $db = DB::get();
    $agentsQuery = $db->prepare("SELECT `id`, `first_name`, `last_name`"
        ." FROM `" . LM_TABLE_AGENTS . "`"
        ." WHERE `id` != :id"
        ." ORDER BY `first_name` DESC, `last_name` DESC");
    $agentsQuery->execute(['id' => $authuser->info('id')]);
    $agents = $agentsQuery->fetchAll();
    array_unshift($agents, [
        'id' => $authuser->info('id'),
        'first_name' => $authuser->info('first_name'),
        'last_name' => $authuser->info('last_name')
    ]);
}

// Success
$success = array();

// Errors
$errors = array();

// Count Leads
$count = false;

// Export All
if (isset($_GET['all'])) {
    $_SESSION['back_lead_search'] = false;
}

// Export Select Leads
$leads = isset($_POST['leads']) ? Format::htmlspecialchars($_POST['leads']) : Format::htmlspecialchars($_GET['leads']);
if (!empty($leads) && is_array($leads)) {
    $query = "SELECT COUNT(DISTINCT `id`) AS `total` FROM `users` WHERE FIND_IN_SET(`id`, '" . implode(',', $leads) . "')"
        . (!empty($agent_id) ? " AND `agent` = '" . $agent_id . "'" : '')
        . ";";
    if ($result = mysql_query($query)) {
        $count = mysql_fetch_assoc($result);
        $count = $count['total'];
    } else {
        $errors[] = 'An error has occurred. Please contact Support.';
        Log::error('Query Error: ' . mysql_error());
    }

// Export Last Search
} elseif (!empty($_SESSION['back_lead_search']) && is_array($_SESSION['back_lead_search'])) {
    $query = "SELECT COUNT(DISTINCT `u`.`id`) AS `total` FROM `users` `u` "
        . (!empty($_SESSION['back_lead_search']['sql_join']) ? $_SESSION['back_lead_search']['sql_join'] : "")
        . " WHERE 1"
        . (!empty($_SESSION['back_lead_search']['sql_where']) ? " AND " . $_SESSION['back_lead_search']['sql_where'] : '')
        . (!empty($agent_id) ? " AND `agent` = '" . $agent_id . "'" : '')
        . ";";
    if ($result = mysql_query($query)) {
        $count = mysql_fetch_assoc($result);
        $count = $count['total'];
    } else {
        $errors[] = 'An error has occurred. Please contact Support.';
        Log::error('Query Error: ' . mysql_error());
    }

// Export All Leads
} else {
    $query = "SELECT COUNT(DISTINCT `id`) AS `total` FROM `users`" . (!empty($agent_id) ? " WHERE `agent` = '" . $agent_id . "'" : '') . ";";
    if ($result = mysql_query($query)) {
        $count = mysql_fetch_assoc($result);
        $count = $count['total'];
    } else {
        $errors[] = 'An error has occurred. Please contact Support.';
        Log::error('Query Error: ' . mysql_error());
    }
}
// If updating count
if (isset($_GET['ajax'])) {
    // Return JSON Data
    if (!empty($errors)) {
        $json['errors'] = $errors;
    } else {
        $json['count'] = Format::number($count);
    }
    header('Content-type: application/json');
    die(json_encode($json));
}

// Require Leads
if (empty($count)) {
    $errors[] = 'No leads were found to export.';
}

// Export Filename
$filename = 'Leads_' . date('Y-m-d-Gi');

// Remember for Next Time
$_SESSION['export-columns'] = !empty($_POST['export']) && is_array($_POST['export']) ? $_POST['export'] : array();

// Default Fields
$export = (!empty($_SESSION['export-columns']) && is_array($_SESSION['export-columns'])) ? $_SESSION['export-columns'] : array(
    'agent',
    'first_name',
    'last_name',
    'email',
    'phone',
    'status'
);

// Load Export Settings from Session
$settings = $authuser->data('export-settings');
$filename = !empty($settings['filename']) ? $settings['filename'] : $filename;
$export = !empty($settings['export']) ? $settings['export'] : $export;

// Fields to Export
$columns = array(
    'Lead Information' => array(
        'first_name'                => array('title' => 'First Name'),
        'last_name'                 => array('title' => 'Last Name'),
        'status'                    => array('title' => 'Status'),
        'heat'                      => array('title' => 'Heat'),
        'agent'                     => array('title' => 'Assigned Agent'),
        'groups'                    => array('title' => 'Assigned Groups'),
        'opt_marketing'             => array('title' => 'Subscribed to Campaigns'),
        'opt_searches'              => array('title' => 'Subscribed to Searches'),
        'opt_texts'                 => array('title' => 'Subscribed to Texts'),
        'bounced'                   => array('title' => 'Bounced Email'),
        'verified'                  => array('title' => 'Verified Email'),
    ),
    'Contact Details' => array(
        'email'                     => array('title' => 'Email Address'),
        'email_alt'                 => array('title' => 'Alternate Email'),
        'phone'                     => array('title' => 'Primary Number'),
        'phone_home_status'         => array('title' => 'Primary Number (Status)'),
        'phone_cell'                => array('title' => 'Secondary Number'),
        'phone_cell_status'         => array('title' => 'Secondary Number (Status)'),
        'phone_work'                => array('title' => 'Work #'),
        'phone_work_status'         => array('title' => 'Work # (Status)'),
        'phone_fax'                 => array('title' => 'Fax #'),
    ),
    'Mailing Address' => array(
        'address1'                  => array('title' => 'Street Address'),
        'address2'                  => array('title' => 'Street Address (Line 2)'),
        'city'                      => array('title' => 'City'),
        'state'                     => array('title' => Locale::spell('State')),
        'zip'                       => array('title' => Locale::spell('Zip Code')),
    ),
    'Search Preferences' => array(
        'search_type'               => array('title' => 'Property Types'),
        'search_city'               => array('title' => 'Cities'),
        'search_subdivision'        => array('title' => 'Subdivisions'),
        'search_minimum_price'      => array('title' => 'Minimum Price'),
        'search_maximum_price'      => array('title' => 'Maximum Price'),
    ),
    'Additional Information' => array(
        'notes'                     => array('title' => 'Quick Notes'),
        'remarks'                   => array('title' => 'Agent Remarks'),
        'comments'                  => array('title' => 'User\'s Comments'),
    ),
    'Lead Activity' => array(
        'ip'                        => array('title' => 'IP Address'),
        'referer'                   => array('title' => 'Origin / Referer'),
        'keywords'                  => array('title' => 'Search Engine Keywords'),
        'num_visits'                => array('title' => '# of Visits'),
        'num_pages'                 => array('title' => '# of Page Views'),
        'num_forms'                 => array('title' => '# of Inquiries'),
        'num_listings'              => array('title' => '# of Viewed Listings'),
        'num_favorites'             => array('title' => '# of Favorites'),
        'num_searches'              => array('title' => '# of Viewed Searches'),
        'num_saved'                 => array('title' => '# of Saved Searches'),
        'num_texts_incoming'        => array('title' => '# of Incoming Texts'),
    ),
    'Agent Activity' => array(
        'num_calls'             => array('title' => '# of Calls'),
        'num_emails'                => array('title' => '# of Emails'),
        'num_texts_outgoing'        => array('title' => '# of Outgoing Texts'),
// 			'# of Notes'				=> array('title' => '# of Notes'),
// 			'Recommended Listings'		=> array('title' => 'Recommended Listings'),
// 			'Suggested Searches'		=> array('title' => 'Suggested Searches'),
    ),
    'Timestamps' => array(
        'timestamp'                 => array('title' => 'Join Date'),
        'timestamp_active'          => array('title' => 'Last Active'),
        'timestamp_assigned'        => array('title' => 'Assigned On'),
        'last_call'                 => array('title' => 'Last Call'),
        'last_email'                => array('title' => 'Last Email'),
// 			'Last Note'					=> array('title' => 'Last Note'),
    ),
);

// Cannot Access Email
if (!$leadsAuth->canEmailLeads($authuser) && (!isset($agent_id) || $agent_id != $authuser->info('id'))) {
    unset($columns['Contact Details']['email']);
    unset($columns['Contact Details']['email_alt']);
    unset($columns['Agent Activity']['num_emails']);
    unset($columns['Timestamps']['last_email']);
    $remove = array('email', 'email_alt', 'num_emails', 'last_email');
    $export = array_diff($export, $remove);
}

// Panels to Display
$panels = array_fill_keys($export, array());
foreach ($columns as $fields) {
    foreach ($fields as $column => $field) {
        $panels[$column] = $field;
    }
}

// Formatting
$format = function ($column, $value) {
    // Empty Value
    switch ($column) {
        // Assigned Agent
        case 'agent':
            $query = "SELECT CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents` WHERE `id` = '" . mysql_real_escape_string($value) . "';";
            if ($result = mysql_query($query)) {
                $agent = mysql_fetch_assoc($result);
                if (!empty($agent)) {
                    return $agent['name'];
                } else {
                    return false;
                }
            } else {
                Log::error('Query Error: ' . mysql_error());
                return false;
            }
            break;
        // Assigned Groups
        case 'groups':
            if (empty($value)) {
                return '(NONE)';
            }
            return $value;
            break;
        // Numbers
        case 'num_calls':
        case 'num_emails':
        case 'num_visits':
        case 'num_pages':
        case 'num_forms':
        case 'num_favorites':
        case 'num_listings':
        case 'num_searches':
        case 'num_saved':
            if (empty($value)) {
                return '(NONE)';
            }
            return Format::number($value);
            break;
        // Currency
        case 'search_minimum_price':
        case 'search_maximum_price':
            return '$' . Format::number($value);
            break;
        // Comments
        case 'notes':
        case 'remarks':
        case 'comments':
            return Format::stripTags($value);
            break;
        // ENUM ('in','out')
        case 'opt_marketing':
        case 'opt_searches':
        case 'opt_texts':
            return ($value === 'in') ? 'yes' : 'no';
        // ENUM ('true','false')
        case 'bounced':
        case 'fbl':
            return ($value === 'true') ? 'yes' : 'no';
        // ENUM ('yes','no')
        case 'verified':
        case 'notify_favs':
        case 'notify_searches':
            return ($value === 'yes') ? 'yes' : 'no';
        // Stored Event
        case 'last_call':
        case 'last_email':
            $value = json_decode($value, true);
            if (!empty($value) && is_array($value) && is_int($value['timestamp'])) {
                return date('Y-m-d h:i:s A', $value['timestamp']) . ' (' . Format::dateRelative($value['timestamp']) . ')';
            } else {
                return 'N/A';
            }
            break;
        // Timestamps
        case 'timestamp':
        case 'timestamp_active':
        case 'timestamp_assigned':
            $ts = is_numeric($value) ? $value : strtotime($value);
            if (empty($ts) || $value === '0000-00-00 00:00:00') {
                return 'N/A';
            }
            return date('Y-m-d h:i:s A', $ts) . ' (' . Format::dateRelative($ts) . ')';
            break;
        default:
            return $value;
    }
};

// Process Submit
if (isset($_GET['submit'])) {
    // Filename
    $filename = Format::htmlspecialchars(trim($_POST['filename']));

    // Export Data
    $data = $_POST['export'];
    if (!empty($data) && is_array($data)) {
        $data = array_combine($data, $data);
        foreach ($columns as $group) {
            foreach ($group as $column => $field) {
                if (in_array($column, array_keys($data))) {
                    $data[$column] = array_merge($field, array(
                        'title' => str_replace('# of ', '', $field['title'])
                    ));
                }
            }
        }
    }

    // Require Filename
    if (empty($filename)) {
        $errors[] = 'You must supply a valid filename.';
    // Require Valid Filename
    } elseif (!preg_match('/^[A-Z0-9_\-]+$/i', $filename)) {
        $errors[] = ' The file can only contain letters, numbers, underscores and hyphens.';
    }

    // Require Export Fields
    if (empty($data)) {
        $errors[] = 'You must select data to export.';
    }

    // Check Errors
    if (empty($errors)) {
        // Save Settings to Session
        $authuser->data('export-settings', array(
            'filename' => $filename,
            'export' => array_keys($data),
        ));

        // Columns
        $select = array('id');
        $headings = array();
        foreach ($data as $column => $field) {
            if (!in_array($column, array('groups', 'ip'))) {
                $select[] = $column;
            }
            $headings[] = str_replace('# of', '', $field['title']);
        }

        // Export Select Leads
        if (!empty($leads) && is_array($leads)) {
            $query = "SELECT `u`.`" . implode("`, `u`.`", $select) . "`, INET_NTOA(`v`.`ip`) AS 'ip'"
                . ", UNIX_TIMESTAMP(`u`.`timestamp`) AS `timestamp_created`, UNIX_TIMESTAMP(`u`.`timestamp_active`) AS `timestamp_active`"
                . " FROM `users` `u`"
                . " LEFT JOIN `users_sessions` `v` ON `u`.`id` = `v`.`user_id`"
                . " WHERE FIND_IN_SET(`u`.`id`, '" . implode(',', $leads) . "')"
                . (!empty($agent_id) ? " AND `u`.`agent` = '" . $agent_id . "' " : "") // Agent's Leads
                . " GROUP BY u.`id` "
                . (!empty($_SESSION['back_lead_search']['sql_order']) ? $_SESSION['back_lead_search']['sql_order'] : "ORDER BY `u`.`timestamp` DESC")
                . ";";

        // Export Last Search
        } else {
            $query = "SELECT `u`.`" . implode("`, `u`.`", $select) . "`"
                . ", UNIX_TIMESTAMP(`u`.`timestamp`) AS `timestamp_created`, UNIX_TIMESTAMP(`u`.`timestamp_active`) AS `timestamp_active`"
                . " FROM `users` `u`"
                . " LEFT JOIN `" . LM_TABLE_AGENTS . "` `a` ON `u`.`agent` = `a`.`id`" // Assigned Agent
                . " LEFT JOIN `users_sessions` `v` ON `u`.`id` = `v`.`user_id`"
                . " WHERE 1"
                . (!empty($_SESSION['back_lead_search']['sql_where']) ? ' AND ' . $_SESSION['back_lead_search']['sql_where'] : '') // Search SESSION
                . (!empty($agent_id) ? " AND `u`.`agent` = '" . $agent_id . "' " : "") // Agent's Leads
                . " GROUP BY u.`id`"
                . (!empty($_SESSION['back_lead_search']['sql_order']) ? $_SESSION['back_lead_search']['sql_order'] : " ORDER BY `timestamp_created` DESC")
                . ";";
        }

        if ($result = mysql_query($query)) {
            // Force Download
            header('Content-type: text/x-csv');
            header('Content-Disposition: attachment; filename=' . $filename . '.csv');

            // Adding character set to fix encoding
            echo "\xEF\xBB\xBF"; // UTF-8

            // Separator
            $delimiter = ','; // chr(ord("\t"));
            $enclosure = '"';

            // Write Line
            echo $enclosure . implode('"' . $delimiter . '"', $headings) . $enclosure;

            // Export Leads
            while ($row = mysql_fetch_assoc($result)) {
                // Lead groups
                if ($groups = mysql_query("SELECT GROUP_CONCAT(`g`.`name` ORDER BY `g`.`name` SEPARATOR ', ') AS 'groups' FROM `groups` `g`"
                    . " LEFT JOIN `users_groups` `ug` ON `ug`.`group_id` = `g`.`id`"
                    . " WHERE `ug`.`user_id` = '" . mysql_real_escape_string($row['id']) . "'"
                    . (!empty($agent_id) ? " AND (`g`.`agent_id` = '" . $agent_id . "' OR (`g`.`agent_id` IS NULL AND `g`.`associate` IS NULL))" : "")
                . ";")) {
                    $group = mysql_fetch_assoc($groups);
                    if (!empty($group['groups'])) {
                        $row['groups'] = $group['groups'];
                    }
                }

                // Report Data
                $record = array();
                foreach (array_keys($data) as $field) {
                    $value = false;
                    if (isset($row[$field])) {
                        $record[] = $format ($field, $row[$field]);
                    } else {
                        $record[] = null;
                    }
                }

                // Write Line
                echo "\r\n" . $enclosure . implode('"' . $delimiter . '"', $record) . $enclosure;
            }

            // Exit
            exit;

        // Query Error
        } else {
            $errors[] = 'An error has occurred. Please contact Support.';
            Log::error('Query Error: ' . mysql_error());
        }
    }
}
