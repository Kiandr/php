<?php

// Get Email Mode
const MODES = ['leads','agents','associates','lenders'];

// Multi-select to add more recipients
const SELECTABLE_MODES = ['agents'];

$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : $_GET['redirect'];
$_GET['type'] = isset($_POST['type']) ? $_POST['type'] : $_GET['type'];
$type = (isset($_GET['type']) && in_array($_GET['type'], MODES)) ? $_GET['type'] : 'leads' ;
$sql_page_size = 500;
$time_limit_per_batch = 15;
$recipient_ids = null;
$rew_mail_max_recipients = $sql_page_size * 2;
$delay_threshold = 100;

$settings = Settings::getInstance();

// Get DB
$db = DB::get();
$params = [];
$auth_params = [];

// Email Single Recipient
$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
$emailSearch = isset($_POST['email_search']) ? $_POST['email_search'] : $_GET['email_search'];
if (!empty($id)) {
    $recipientsExist = true;

    $sql_set_where = "`id` = :id";
    $params = ['id' => $id];
    $recipient_ids = [$id];
} else if (isset($emailSearch) && isset($_SESSION['back_lead_search'])) {
    $multiple = true;
    $recipientsExist = true;
} else {
    $multiple = true;

    // Email All Provided Ids
    $recipient_ids = isset($_POST[$type]) ? $_POST[$type] : $_GET[$type];
    $recipient_ids = !empty($recipient_ids) && !is_array($recipient_ids) ? explode(',', $recipient_ids) : $recipient_ids;
    if (!empty($recipient_ids)) {
        $recipientsExist = true;
        $sql_set_where = "`id` IN (" . implode(', ', array_map([$db, 'quote'], $recipient_ids)) . ")";
    }
}

// Leads Mode
if ($type == 'leads') {
    // Set Recipient Type
    $recipientsType = REW\Backend\Email\Email::TYPE_LEADS;

    // Recipient Query Params
    $sql_table = LM_TABLE_LEADS;
    $sql_table_alias = 'u';

    if ($multiple) {
        // Get Lead Authorization
        $leadsAuth = new REW\Backend\Auth\LeadsAuth($settings, $authuser);
        $teamsAuth = new REW\Backend\Auth\TeamsAuth($settings);

        // Not authorized to view all leads
        if (!$leadsAuth->canEmailLeads($authuser)) {
            // Not authorized to view any leads
            if (!$leadsAuth->canViewOwn($authuser)) {
                throw new \REW\Backend\Exceptions\UnauthorizedPageException(
                    __('You do not have permission to email leads')
                );
            }

            // Is Agent: Only Show Assigned Leads
            if ($authuser->isAgent()) {
                if ($teamsAuth->canViewTeamLeads()) {
                    // Get Leads from Teams
                    $teamManager = new REW\Backend\Teams\Manager($authuser);
                    $teamAgents =$teamManager->getAgentsSharingLeads();

                    // Double check for personal leads
                    if (!in_array($authuser->info('id'), $teamAgents)) {
                        $teamAgents[] = $authuser->info('id');
                    }

                    // Get Agent Check
                    $sql_auth[] = "`agent` IN (" . implode(', ', array_map([$db, 'quote'], $teamAgents)) . ")"
                        . " AND (`agent` = :ownerId OR `u`.`share_lead` = '1')";
                } else {
                    // Get Personal Leads
                    $sql_auth[] = "`agent` = :ownerId";
                }
                // Is Lender: Only Show Assigned Leads
            } else if ($authuser->isLender()) {
                $sql_auth[] = "`lender` = :ownerId";
            }

            $auth_params = ['ownerId' => $authuser->info('id')];
        }

        // Check for opted in leads which have not bounced or been flagged as spam
        $sql_opt_select .= ", `u`.`opt_marketing`, `u`.`bounced`, `u`.`fbl`, `u`.`verified`";
        $sql_opt_where = " `u`.`opt_marketing` = 'in' AND `u`.`bounced` = 'false' AND `u`.`fbl` = 'false'";

        // Email All Searched Leads
        if (isset($emailSearch) && isset($_SESSION['back_lead_search'])) {
            if (isset($_SESSION['back_lead_search']['sql_join'])) {
                $sql_search_join = $_SESSION['back_lead_search']['sql_join'];
            }
            if (!empty($_SESSION['back_lead_search']['sql_where'])) {
                $sql_search_where = $_SESSION['back_lead_search']['sql_where'];
            }
        }

        // Filter Leads, Only including those verified
        $recipients_filter = function ($recipient) {

            // Check if e-mail host is blocked
            if (Validate::verifyWhitelisted($recipient['email'])) {
                return false;
            }

            // Check if e-mail host requires verification
            if ((Validate::verifyRequired($recipient['email'])
                || !empty(Settings::getInstance()->SETTINGS['registration_verify']))
                && $recipient['verified'] != 'yes') {
                return false;
            }

            return true;
        };

    // Single Lead
    } else {
        // Create Lead Instance
        $lead = Backend_Lead::load($id);
        if (empty($lead->getRow())) {
            throw new \REW\Backend\Exceptions\MissingId\MissingLeadException();
        }

        // Get Lead Authorization
        $leadAuth = new REW\Backend\Auth\Leads\LeadAuth($settings, $authuser, $lead);
        if (!$leadAuth->canEmailLead()) {
            throw new \REW\Backend\Exceptions\UnauthorizedPageException(
                __('You do not have permission to email this lead')
            );
        }

        // Check for lead warnings
        if ($lead->info('opt_marketing') =='out') {
            $leadWarning = __('This lead has unsubscribed from marketing mail.');
        } else if ($lead->info('bounced') == 'true') {
            $leadWarning = __('This lead has previously bounced an email.');
        } else if ($lead->info('fbl') == 'true') {
            $leadWarning = __('This lead has marked a previous message as spam.');
        }
    }

// Agents Mode
} else if ($type == 'agents') {
    // Set Recipient Type
    $recipientsType = REW\Backend\Email\Email::TYPE_AGENTS;

    // Load Agent
    if (!$multiple) {
        // Create Lead Instance
        $agent = Backend_Agent::load($id);
        if (empty($agent)) {
            throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
        }

        // Get Lead Authorization
        $agentAuth = new REW\Backend\Auth\Agents\AgentAuth($settings, $authuser, $agent);
        if (!$agentAuth->canEmailAgent()) {
            throw new \REW\Backend\Exceptions\UnauthorizedPageException(
                __('You do not have permission to email this agent')
            );
        }
    }

    // Recipient Query Params
    $sql_table = LM_TABLE_AGENTS;
    $sql_table_alias = 'a';

// Associates Mode
} else if ($type == 'associates' && !empty($id)) {
    // Set Recipient Type
    $recipientsType = REW\Backend\Email\Email::TYPE_ASSOCIATES;

    // Create Associate Instance
    $associate = Backend_Associate::load($id);
    if (empty($associate)) {
        throw new \REW\Backend\Exceptions\MissingId\MissingAssociateException();
    }

    // Get Associate Authorization
    $associateAuth = new REW\Backend\Auth\Associates\AssociateAuth($settings, $authuser, $associate);
    if (!$associateAuth->canEmailAssociate()) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to email this associate')
        );
    }

    // Recipient Query Params
    $sql_table = LM_TABLE_ASSOCIATES;
    $sql_table_alias = 'a';

// Lenders Mode
} else if ($type == 'lenders' && !empty($id)) {
    // Set Recipient Type
    $recipientsType = REW\Backend\Email\Email::TYPE_LENDERS;

    // Recipient Query Params
    $sql_table = LM_TABLE_LENDERS;
    $sql_table_alias = 'l';
}

// Build Where
$sql_where = [];
if (!empty($sql_set_where)) {
    $sql_where []= $sql_set_where;
}
if (!empty($sql_search_where)) {
    $sql_where []= $sql_search_where;
}
$sql_where = !empty($sql_where) ? '(' . implode(' OR ', $sql_where) . ')' : '';
if (!empty($sql_opt_where)) {
    if (!empty($sql_where)) {
        $sql_where .= ' AND ';
    }
    $sql_where .= $sql_opt_where;
}

// Build Auth Where
$sql_auth = !empty($sql_auth) ? implode(' AND ', $sql_auth) : '';

// Query Recipients
$recipientQuery = $db->prepare("SELECT `" . $sql_table_alias . "`.`id`,  `" . $sql_table_alias . "`.`first_name`,"
    . " `" . $sql_table_alias . "`.`last_name`,  `" . $sql_table_alias . "`.`email`"  . ($type === 'leads' ? ", `" . $sql_table_alias . "`.`guid`" : "") . $sql_opt_select
    . " FROM `" . $sql_table . "` " . $sql_table_alias
    . $sql_search_join
    . ' WHERE `' . $sql_table_alias . '`.`id` > :marker'
    . (!empty($sql_where) ? ' AND ' : '') . $sql_where
    . (!empty($sql_auth) ? ' AND ' : '') . $sql_auth
    . " ORDER BY `" . $sql_table_alias . "`.`id`"
    . " LIMIT " . ((int) $sql_page_size) . ";");
if (in_array($type, SELECTABLE_MODES)) {
    // Query Possible Recipients
    $query = $db->prepare("SELECT `" . $sql_table_alias . "`.`id`, `" . $sql_table_alias . "`.`first_name`,"
        . " `" . $sql_table_alias . "`.`last_name`, `" . $sql_table_alias . "`.`email`" . ($type === 'leads' ? ", `" . $sql_table_alias . "`.`guid`" : "")
        . " FROM `" . $sql_table . "` " . $sql_table_alias
        . (!empty($sql_auth) ? ' WHERE ' . implode(' AND ', $sql_auth) : '' ) . ";");
    $query->execute($auth_params);
    $persons = $query->fetchAll();
}

// Success
$success = array();

// Errors
$errors = array();

// Allowed File Types for Email Attachments
$allow = Backend_Mailer::$allowAttachments;

$container = Container::getInstance();
$coreConfig = $container->get(\REW\Core\Interfaces\EnvironmentInterface::class)
    ->loadMailCRMSettings()->getCoreConfig();
$settings = $container->get(\REW\Core\Interfaces\SettingsInterface::class);
$thirdPartyMail = !empty($coreConfig['external_mail_provider']);

// Process Submit
$marker = '';
$recipientCount = 0;
$sentEmailCount = 0;
$emailer = null;
$recipient = null;
$restrictRecipients = false;
$urlSearch = $type == 'leads' ? $settings['URLS']['URL_BACKEND'] . 'leads/' : null;

$db = DB::get('users');

$db->disableLogging(true);

if (isset($_GET['submit'])) {
    //BREW48-2461 - switch to delayed emails if there are more than $delay_threshold recipients
    $email_params = [];
    if ($_POST['recipientCount'] > $delay_threshold) {
        $email_params['delay'] = 'Y';
        list($email_params['send_date'], $email_params['send_time']) = explode(' ',date('Y-m-d H:i',strtotime("+10 second")));
    }
    if (!empty($_POST)) {
        $email_params = array_merge($email_params, $_POST);
    }
    try {
        $emailer = new REW\Backend\Email\Email($authuser, $email_params, $_FILES['attachments'] ?: []);
    } catch (\InvalidArgumentException $e) {
        $errors[] = $e->getMessage();
    }
}

// Require Recipients to exist to be queried
if ($recipientsExist) {
    do {
        set_time_limit($time_limit_per_batch);

        // Paginate Recipients
        $page_params = ['marker' => $marker];

        $recipientQuery->execute(array_merge($params, $auth_params, $page_params));
        $recipients = $recipientQuery->fetchAll() ?: [];
        $recipientsExist = !empty($recipients);
        $foundRecipients = (bool) $recipients;
        $lastRecipient = end($recipients);
        $marker = $lastRecipient['id'];
        if (!$multiple) {
            $recipient = $lastRecipient;
            $foundRecipients = false;
        }

        // Filter Out Recipients
        if (isset($recipients_filter) && is_callable($recipients_filter)) {
            $recipients = array_filter($recipients, $recipients_filter);
        }
        $recipientCount += count($recipients);

        if (isset($_GET['submit']) && $emailer) {
            try {
                // If no valid recipients
                if ($recipientsExist && empty($recipients)) {
                    throw new \REW\Backend\Exceptions\Email\AllReportedEmailException();
                }

                // Start transaction
                $db->beginTransaction();

                // Send Email
                if ($emailer->send($recipients, $recipientsType, $errors)) {
                    $sentEmailCount += count($recipients);
                }

                $db->commit();
            } catch (\Exception $e) {
                $errors[] = 'Error: ' . $e->getMessage();
                $db->rollBack();
                $sentEmailCount = 0;
                $foundRecipients = false;
            }
        }

        // Approximate # of recipients.
        if (!$thirdPartyMail && $recipientCount >= $rew_mail_max_recipients) {
            /** @var \REW\Backend\Application $this */
            $this->notices->warning(
                __('Too many recipients found. Only the first %s will be emailed.', $rew_mail_max_recipients)
            );
            $restrictRecipients = $recipientCount;
            $foundRecipients = false;
        }
    } while ($foundRecipients);

    $db->enableLogging();

    if (isset($_GET['submit']) && $emailer && $sentEmailCount) {
        $success[] = $emailer->buildSuccessMessage(
            ($emailer->isDelayed() ? strtotime(date('Y-m-d', strtotime($_POST['send_date'])) . ' ' . date('H:i:s', strtotime($_POST['send_time']))) : null),
            $multiple,
            $recipientsType,
            $sentEmailCount,
            $recipient
        );

        // Save Notices
        $authuser->setNotices($success, $errors);

        // Redirect
        if (!empty($redirect)) {
            header('Location: ' . $redirect);
            exit;
        } else if (!empty($_POST['timeline_id'])) {
            $timelineFactory = \Container::getInstance()->get(\REW\Backend\Page\TimelineFactory::class);
            $lastPage = $timelineFactory->load($_POST['timeline_id']);
            header('Location: ' . $lastPage->getLink('back'));
            exit;
        }

        // Unset $_POST
        unset($_POST['email_subject'], $_POST['email_message'], $_POST['agents'], $_POST['leads']);
    }
} else {
    $recipients = [];
}

// Agents Only: Documents & Templates
if ($authuser->isAgent()) {
    // Templates
    $templates = array();
    $query = $db->prepare("SELECT `id`, `name` FROM `" . LM_TABLE_DOC_TEMPLATES . "` WHERE (`agent_id` = :agent_id OR `share` = 'true') ORDER BY LENGTH(`name`) ASC, `name` ASC;");
    if ($query->execute(array('agent_id' => $authuser->info('id')))) {
        $templates = $query->fetchAll();
    } else {
        $errors[] = 'Error Occurred while loading Templates.';
    }

    // Documents
    $docs = array();
    $query = $db->prepare("SELECT `c`.`id` AS `cat_id`, `c`.`name` AS `cat_name`, `d`.`id` AS `doc_id`, `d`.`name` AS `doc_name`"
       . " FROM `" . LM_TABLE_DOC_CATEGORIES . "` `c` LEFT JOIN `" . LM_TABLE_DOCS . "` `d` ON `c`.`id` = `d`.`cat_id`"
       . (($authuser->info('mode') == 'agent') ? " WHERE (`d`.`share` = 'true' OR `c`.`agent_id` = :agent_id)" : '')
       . " ORDER BY LENGTH(`cat_name`) ASC, `cat_name` ASC, LENGTH(`doc_name`) ASC, `doc_name` ASC;");
    if ($query->execute(array('agent_id' => $authuser->info('id')))) {
        while ($row = $query->fetch()) {
            $docs[$row['cat_id']]['name'] = $row['cat_name'];
            $docs[$row['cat_id']]['docs'][$row['doc_id']] = $row['doc_name'];
        }
    } else {
        $errors[] = __('Error Occurred while loading Documents.');
    }
}

// Current Date & Time
$_POST['send_date'] = isset($_POST['send_date']) ? $_POST['send_date'] : date('Y-m-d');
$_POST['send_time'] = isset($_POST['send_time']) ? $_POST['send_time'] : date('h:i A');
