<?php

// Get Database
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Lead ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Super Admin Permissions can not be changed
if (($_GET['id'] == 1)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You can not change the permissions of the administrative account.')
    );
}

// Query Lead
$agent = Backend_Agent::load($_GET['id']);

// Throw Missing Agent Exception
if (empty($agent)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
}

// Get Agent Authorization
$agentAuth = new REW\Backend\Auth\Agents\AgentAuth($settings, $authuser, $agent);

// Not authorized to view agent history
if (!$agentAuth->canManagePermissions()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to edit agent permissions.'
    );
}

// Sharktank Setting
$sharktank = Container::getInstance()->get(\REW\Backend\Controller\Leads\SharktankController::class);
$isSharktankEnabled = $sharktank->isSharktankEnabled();

// Process Submit
if (isset($_GET['submit'])) {
    // User Permissions
    if (is_array($_POST['permissions']['user'])) {
        $permissions_user = array_sum($_POST['permissions']['user']);
    } else {
        $permissions_user = 0;
    }

    // Admin Permissions
    if (is_array($_POST['permissions']['admin'])) {
        $permissions_admin = array_sum($_POST['permissions']['admin']);
    } else {
        $permissions_admin = 0;
    }

    // Build UPDATE Query
    try {
        $db->prepare("UPDATE `agents` SET "
           . "`permissions_user`  = :permissions_user,"
           . "`permissions_admin` = :permissions_admin"
           . (empty($permissions_admin) ? ", `mode` = 'agent'" : "")
           . " WHERE "
           . "`id` = :id;")
        ->execute([
            'permissions_user' => $permissions_user,
            'permissions_admin' => $permissions_admin,
            'id' => $agent['id']
        ]);

        // Success
        $success[] = 'Account Permissions have successfully been updated.';

        // Save Notices
        $authuser->setNotices($success, $errors);

        // Redirect Back to Form
        header('Location: ?id=' . $agent['id'] . '&success');
        exit;

    // Query Error
    } catch (PDOException $e) {
        $errors[] = 'Error Occurred, Account Permissions could not be updated.';
    }
}

// Permissions
$permissions = array();

// CMS
$permissions[] = array(
    'title' => 'Content',
    'permissions' => array(
        array('value' => Auth::PERM_CMS_HOMEPAGE,        'title' => 'Edit Homepage',        'description' => 'Allow agent to edit home page content.', 'type' => 'admin'),
        array('value' => Auth::PERM_CMS_PAGES,           'title' => 'Add & Edit Pages',     'description' => 'Allow agent to add and edit frontend pages.', 'type' => 'admin', 'subpermissions' => [
            array('value' => Auth::PERM_CMS_PAGES_DELETE,    'title' => 'Delete Pages',         'description' => 'Allow agent to delete frontend pages.', 'type' => 'admin'),
            array('value' => Auth::PERM_CMS_NAV,             'title' => 'Manage Navigation',    'description' => 'Allow agent to edit frontend navigation menus.', 'type' => 'admin'),
        ]),
        array('value' => Auth::PERM_CMS_SNIPPETS,        'title' => 'Add & Edit Snippets',  'description' => 'Allow agent to add and edit snippets.', 'type' => 'admin', 'subpermissions' => [
            array('value' => Auth::PERM_CMS_SNIPPETS_DELETE, 'title' => 'Delete Snippets',      'description' => 'Allow agent to delete snippets', 'type' => 'admin')
        ]),
        array('value' => Auth::PERM_CMS_FEATURED_COMMUNITIES, 'title' => 'Add & Edit Featured Communities', 'description' => 'Allow agent to add and edit featured communities', 'type' => 'admin', 'subpermissions' => [
            array('value' => Auth::PERM_CMS_FEATURED_COMMUNITIES_DELETE, 'title' => 'Delete Featured Communities', 'description' => 'Allow agent to delete featured communities', 'type' => 'admin'),
        ]),
        array('value' => Auth::PERM_CMS_BACKUP,          'title' => 'Perform CMS Backup',   'description' => 'Allow agent to download local backups of site content.', 'type' => 'admin'),
        (!empty(Settings::getInstance()->MODULES['REW_REWRITE_MANAGER']) ?
            array('value' => Auth::PERM_CMS_REDIRECT,        'title' => 'Redirect Rules',       'description' => 'Allow agent to set up redirects to other pages.', 'type' => 'admin')
            : ''
        ),
        (!empty(Settings::getInstance()->MODULES['REW_SLIDESHOW_MANAGER']) ?
            array('value' => Auth::PERM_CMS_SLIDESHOW,       'title' => 'Slideshow Manager',    'description' => 'Allow agent to create slideshows.', 'type' => 'admin')
            : ''
        ),
        (!empty(Settings::getInstance()->MODULES['REW_TESTIMONIALS']) ?
            array('value' => Auth::PERM_CMS_TESTIMONIALS,    'title' => 'Testimonials Manager', 'description' => 'Allow agent to create and edit client testimonials.', 'type' => 'admin')
            : ''
        )
    )
);

// Listings Manager
$permissions[] = array(
    'title' => 'Listings',
    'permissions' => array(
        array('value' => Auth::PERM_LISTINGS_AGENT,    'title' => 'Add & Edit Personal Listings', 'description' => 'Allow agent to add their own listings to the listing manager.', 'type' => 'user'),
        array('value' => Auth::PERM_LISTINGS_MANAGE,   'title' => 'Add & Edit All Listings', 'description' => 'Allow agent to manage every backend listing.', 'type' => 'admin', 'subpermissions' => [
            array('value' => Auth::PERM_LISTINGS_DELETE,   'title' => 'Delete Listings',     'description' => 'Allow agent to delete listings they have access to.', 'type' => 'admin')
        ]),
        (!empty(Settings::getInstance()->MODULES['REW_FEATURED_LISTINGS']) ?
            array('value' => Auth::PERM_LISTINGS_FEATURED, 'title' => 'Manage Featured Listings',  'description' => 'Allow agent to designate featured listings.', 'type' => 'admin')
            : ''
        ),
    )
);

// Agent Management
$permissions[] = array(
    'title' => 'Agent',
    'permissions' => array(
        array('value' => Auth::PERM_AGENTS_VIEW,  'title' => 'View Agents',  'description' => 'Allow agent to view the basic summary of other agents in the system.', 'type' => 'user', 'subpermissions' => [
            array('value' => Auth::PERM_AGENTS_MANAGE,  'title' => 'Add & Edit Agents', 'description' => 'Allow agent to edit every agent profile in the system.', 'type' => 'admin'),
            array('value' => Auth::PERM_AGENTS_DELETE,  'title' => 'Delete Agents',     'description' => 'Allow agent to delete other agents in the system.', 'type' => 'admin'),
            array('value' => Auth::PERM_AGENTS_EMAIL, 'title' => 'Email Agents', 'description' => 'Show agent email addresses to this agent and enable the ability to email them.', 'type' => 'user')
        ])
    )
);

// Teams Management
$permissions[] = array(
    'title' => 'Team',
    'permissions' => array(
        array('value' => Auth::PERM_TEAMS_VIEW,         'title' => 'View Personal Teams',          'description' => 'Allow agent to view the teams they belong to.', 'type' => 'user', 'subpermissions' => [
            array('value' => Auth::PERM_TEAMS_MANAGE,       'title' => 'Create & Edit Personal Teams', 'description' => 'Allow agent to create, manage, and delete their own teams.',     'type' => 'admin'),
            array('value' => Auth::PERM_TEAMS_MANAGE_ALL,   'title' => 'Create & Edit All Teams',      'description' => 'Allow agent to create, manage, and delete teams for any agent.', 'type' => 'admin', 'full' => true)
        ]),
    )
);

// Lead Management
$permissions[] = array(
    'title' => 'Leads',
    'permissions' => array(
        (!empty($isSharktankEnabled) ?
            array('value' => Auth::PERM_SHARK_TANK,   'title' => 'Access Shark Tank Leads', 'description' => 'Allow agents to access leads who are in the Shark Tank, as well as opt in/out of Shark Tank lead notifications.', 'type' => 'user')
            : ''
        ),
        array('value' => Auth::PERM_LEADS_ALL,        'title' => 'Access to All Leads',       'description' => 'Allow agent to manage every lead in the system.', 'type' => 'admin', 'subpermissions' => [
            array('value' => Auth::PERM_LEADS_ASSIGN, 'title' => 'Assign Leads to Agents',    'description' => 'Allow agent to assign leads to other agents.', 'type' => 'admin'),
            array('value' => Auth::PERM_LEADS_DELETE, 'title' => 'Allow to Delete all Leads',   'description' => 'Allow agent to delete leads from the system.', 'type' => 'admin'),
            array('value' => Auth::PERM_LEADS_EMAILS, 'title' => 'Allow to Email all Leads', 'description' => 'Allow agent to view lead email addresses and email them through the backend.', 'type' => 'user'),
            array('value' => Auth::PERM_LEADS_ACTION_PLAN_ASSIGNMENTS, 'title' => 'Allow to Assign Action plan to all Leads', 'description' => 'Allow agent to assign action plan to all leads through the backend.', 'type' => 'user'),
        ]),
        array('value' => Auth::PERM_LEADS_AUTO_RESPONDERS, 'title' => 'Manage Auto-Responders',  'description' => 'Allow agent to manage auto-responders sent to new leads.', 'type' => 'admin'),
        array('value' => Auth::PERM_ACTION_PLAN_ASSIGNMENTS, 'title' => 'Action Plan Assignments', 'description' => 'Allow agent to assign and/or un-assign action plans for their assigned leads.', 'type' => 'user'),
        array('value' => Auth::PERM_LEADS_TEXT,              'title' => 'Send Text Messages',    'description' => 'Allow agent to send text messages to leads they have access to.', 'type' => 'user'),
        array('value' => Auth::PERM_LEADS_CAMPAIGNS,         'title' => 'Allow Email Campaigns', 'description' => 'Allow agent access to setup automated drip email campaigns.', 'type' => 'user'),
        array('value' => Auth::PERM_LEADS_BACKUP,            'title' => 'Export Own Leads',          'description' => 'Allow agent to export a comma separated file of their assigned leads.', 'type' => 'user'),
        array('value' => Auth::PERM_LEADS_ALL_BACKUP,        'title' => 'Export All Leads',             'description' => 'Allow agent to export a comma separated file of every lead.', 'type' => 'admin'),
        array('value' => Auth::PERM_LEAD_FILES,              'title' => 'Access Own File Manager',      'description' => 'Allow agent access to their own file manager.', 'type' => 'user'),
        array('value' => Auth::PERM_CMS_FILES,               'title' => 'Access All File Managers',      'description' => 'Allow agent access to every agents\' files.', 'type' => 'admin'),
        array('value' => Auth::PERM_CUSTOM_FIELDS,           'title' => 'Manage Custom Fields',   'description' => 'Allow Agent to add/edit lead custom fields.', 'type' => 'admin'),
    )
);

// Reports
$permissions[] = array(
    'title' => 'Reports',
    'permissions' => array(
        array('value' => Auth::PERM_REPORTS_AGENT,  'title' => 'View Personal Agent Response Report',       'description' => 'Give agent access to see their own response report.', 'type' => 'user'),
        array('value' => Auth::PERM_REPORTS_AGENT_ALL,          'title' => 'View Agent Response Reports',   'description' => 'Give admin access to see all agent response reports.', 'type' => 'admin'),
        array('value' => Auth::PERM_REPORTS_LISTINGS,           'title' => 'View Listing Reports',           'description' => '', 'type' => 'admin'),
        array('value' => Auth::PERM_REPORTS_GOOGLE_ANALYTICS,   'title' => 'View Google Analytics Reports',  'description' => '', 'type' => 'admin'),
        (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO']) ?
            array('value' => Auth::PERM_REPORTS_ESPRESSO,  'title' => 'View REW Dialer Report', 'description' => 'Give agent access to see their own dialer report.', 'type' => 'user')
            : ''
        ),
    )
);

// Calendar
$permissions[] = array(
    'title' => 'Calendar',
    'permissions' => array(
        array('value' => Auth::PERM_CALENDAR_AGENT,  'title' => 'Enable Personal Calendar',   'description' => 'Allow agent access to their own calendar to manage events.', 'type' => 'user'),
        array('value' => Auth::PERM_CALENDAR_MANAGE, 'title' => 'Add & Edit All Events',      'description' => 'Allow agent to manage the calendar and events or every agent', 'type' => 'admin'),
        array('value' => Auth::PERM_CALENDAR_DELETE, 'title' => 'Delete Events',              'description' => 'Allow agent to delete events from the calendar', 'type' => 'admin'),
        (!empty(Settings::getInstance()->MODULES['REW_GOOGLE_CALENDAR']) ?
            array('value' => Auth::CALENDAR_GOOGLE_PUSH, 'title' => 'Google Calendar Push',   'description' => 'Allows agents to enable/disable Google Calendar push in their agent settings.', 'type' => 'user')
            : ''
        ),
        (!empty(Settings::getInstance()->MODULES['REW_OUTLOOK_CALENDAR']) ?
            array('value' => Auth::CALENDAR_OUTLOOK_PUSH, 'title' => 'Outlook Calendar Push', 'description' => 'Allows agents to enable/disable Outlook Calendar push in their agent settings.', 'type' => 'user')
            : ''
        ),
    )
);

// Directory
if (!empty(Settings::getInstance()->MODULES['REW_DIRECTORY'])) {
    $permissions[] = array(
        'title' => 'Directory',
        'permissions' => array(
            array('value' => Auth::PERM_DIRECTORY_AGENT,             'title' => 'Manage Personal Directory', 'description' => 'Allow agent to submit their own directory listings.', 'type' => 'user'),
            array('value' => Auth::PERM_DIRECTORY_LISTINGS_MANAGE,   'title' => 'Add & Edit All Listings',   'description' => 'Allow agent to add and edit directory listings for any agent.', 'type' => 'admin'),
            array('value' => Auth::PERM_DIRECTORY_LISTINGS_DELETE,   'title' => 'Delete Listings',           'description' => 'Allow agent to delete directory listings.', 'type' => 'admin'),
            array('value' => Auth::PERM_DIRECTORY_CATEGORIES_MANAGE, 'title' => 'Add & Edit All Categories', 'description' => 'Allow agent to add and edit directory categories.', 'type' => 'admin'),
            array('value' => Auth::PERM_DIRECTORY_CATEGORIES_DELETE, 'title' => 'Delete Categories',         'description' => 'Allow agent to delete directory categories.', 'type' => 'admin'),
            array('value' => Auth::PERM_DIRECTORY_SETTINGS,          'title' => 'Directory Settings',        'description' => 'Allow agent to update directory settings.', 'type' => 'admin'),
        )
    );
}

// Blog
if (!empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'])) {
    $permissions[] = array(
        'title' => 'Blog',
        'permissions' => array(
            array('value' => Auth::PERM_BLOG_AGENT,      'title' => 'Manage Personal Blog',       'description' => 'Allow agent to write and publish their own blog articles.', 'type' => 'user'),
            array('value' => Auth::PERM_BLOG_ENTRIES,    'title' => 'Manage All Blog Entries',    'description' => 'Allow agent to write and edit articles for other agents.', 'type' => 'admin'),
            array('value' => Auth::PERM_BLOG_CATEGORIES, 'title' => 'Manage All Blog Categories', 'description' => 'Allow agent to add and edit blog categories.', 'type' => 'admin'),
            array('value' => Auth::PERM_BLOG_LINKS,      'title' => 'Manage All Blog Links',      'description' => 'Allow agent to add, edit, and delete blog links.', 'type' => 'admin'),
            array('value' => Auth::PERM_BLOG_COMMENTS,   'title' => 'Manage All Blog Comments',   'description' => 'Allow agent to add or delete user blog comments.', 'type' => 'admin'),
            array('value' => Auth::PERM_BLOG_SETTINGS,   'title' => 'Manage Blog Settings',       'description' => 'Allow agent to modify blog settings and visablility.', 'type' => 'admin'),

        )
    );
}

// Permission items for partners
$items = array();

// Happy Grasshopper
if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_GRASSHOPPER'])) {
    $items[] = array('value' => Auth::PERM_PARTNERS_GRASSHOPPER_AGENT, 'title' => 'Enable Happy Grasshopper', 'description' => 'Allow agent to set up Happy Grasshopper integration with their account.', 'type' => 'user');
}

// BombBomb
if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_BOMBBOMB'])) {
    $items[] = array('value' => Auth::PERM_PARTNERS_BOMBBOMB_AGENT, 'title' => 'Enable BombBomb', 'description' => 'Allow agent to set up BombBomb integration with their account.', 'type' => 'user');
}

// Espresso (REW Dialer)
if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO'])) {
    $items[] = array('value' => Auth::PERM_PARTNERS_ESPRESSO, 'title' => 'Enable REW Dialer', 'description' => 'Allow agent to access the REW dialer with their account.', 'type' => 'user');
}

// WiseAgent
if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_WISEAGENT'])) {
    $items[] = array('value' => Auth::PERM_PARTNERS_WISEAGENT_AGENT, 'title' => 'Enable WiseAgent', 'description' => 'Allow agent to set up WiseAgent integration with their account.', 'type' => 'user');
}

// Zillow
if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_ZILLOW'])) {
    $items[] = array('value' => Auth::PERM_PARTNERS_ZILLOW, 'title' => 'Enable Zillow', 'description' => 'Allow agent to set up Zillow integration with their account.', 'type' => 'user');
}

// DotLoop
if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_DOTLOOP'])) {
    $items[] = array('value' => Auth::PERM_PARTNERS_DOTLOOP, 'title' => 'Enable DotLoop', 'description' => 'Allow agent to set up DotLoop integration with their account.', 'type' => 'user');
}

// First Call Agent (ISA)
if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_FIRSTCALLAGENT'])) {
    $items[] = array('value' => Auth::PERM_PARTNERS_FIRSTCALLAGENT, 'title' => 'Enable FCA', 'description' => 'Allow agent to set up FCA integration with their account.', 'type' => 'user');
}

if (!empty($items)) {
    $permissions[] = array(
        'title' => 'Partners',
        'permissions' => $items,
    );
}

// Admin Permissions
$agent['permissions_admin'] = isset($_POST['permissions']['admin']) ? array_sum($_POST['permissions']['admin']) : $agent['permissions_admin'];

// User Permissions
$agent['permissions_user']  = isset($_POST['permissions']['user'])  ? array_sum($_POST['permissions']['user'])  : $agent['permissions_user'];
