<?php

$authuser = Auth::get();

if ($this->config('ajax') == true) {
    if (isset($_GET['q'])) {
        // Total
        $total = 0;

        // Errors
        $errors = array();

        // Search Results
        $results = array();

        // Mode
        $mode = 'basic';

        if (strpos($_GET['q'], 'content:') === 0) {
            $mode = 'content';
            $_GET['q'] = substr($_GET['q'], 8);
        }

        // Remove Excess Whitespace
        $_GET['q'] = trim($_GET['q']);

        // Require More than 3 Characters
        if (strlen($_GET['q']) < 3) {
            return;
        }

        // Search Backend Sections
        $results['sections'] = array();

        $contentNavigation = Container::getInstance()->get(\REW\Backend\Navigation\ContentNavigation::class);
        $crmNavigation = Container::getInstance()->get(\REW\Backend\Navigation\CrmNavigation::class);
        $listingNavigation = Container::getInstance()->get(\REW\Backend\Navigation\ListingsNavigation::class);
        $peopleNavigation = Container::getInstance()->get(\REW\Backend\Navigation\PeopleNavigation::class);
        $settingsNavigation = Container::getInstance()->get(\REW\Backend\Navigation\SettingsNavigation::class);

        $places = array_merge(
            $contentNavigation->getNavLinks(),
            $crmNavigation->getNavLinks(),
            $listingNavigation->getNavLinks(),
            $peopleNavigation->getNavLinks(),
            $settingsNavigation->getNavLinks()
        );

        foreach ($places as $place) {
            if (stristr($place['title'], $_GET['q'])) {
                $results['sections'][] = array('title' => $place['title'], 'href' => $place['link']);
                $total++;
            }
        }

        // Get Leads Setting
        $settings = Settings::getInstance();
        $agentsAuth = new REW\Backend\Auth\AgentsAuth($settings);
        $blogAuth = new REW\Backend\Auth\BlogsAuth($settings);
        $leadsAuth = new REW\Backend\Auth\LeadsAuth($settings);

        // Search Files
        $results['files'] = array();
        if ($leadsAuth->canViewFiles($authuser) || $leadsAuth->canManageOwnFiles($authuser)) {
            $query = "SELECT SQL_CALC_FOUND_ROWS `id`, `name` FROM `cms_files` WHERE "
                . "`name` LIKE '%" . mysql_real_escape_string($_GET['q']) . "%'"
                .  (!$leadsAuth->canViewFiles($authuser) ? " AND (`agent` = '" . $authuser->info('id') . "' OR `share` = 'true')" : "")
                . " ORDER BY `name` ASC LIMIT 11;";
            if ($result = mysql_query($query)) {
                while ($row = mysql_fetch_assoc($result)) {
                    $results['files'][] = $row;
                    $total++;
                }
                list($results['files_cnt']) = mysql_fetch_row(mysql_query("SELECT FOUND_ROWS();"));
            } else {
                $errors[] = __('Error Searching File Manager');
            }
        }

        // Get Current Auth
        $subdomainFactory = Container::getInstance()->get(\REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class);
        $subdomain = $subdomainFactory->buildSubdomainFromRequest();
        $subdomainAuth = $subdomain->getAuth();

        // Agent Subdomain Content Check
        if (!$subdomainAuth->canManagePages()) {
            $agentSubdomain = $subdomainFactory->buildSubdomainFromRequest('canManageOwnSubdomain');
            if (($agentSubdomain instanceof REW\Backend\CMS\Interfaces\SubdomainInterface)) {
                $subdomain = $agentSubdomain;
                $subdomainAuth = $subdomain->getAuth();
            }
        }

        $show_content = $subdomainAuth->canManagePages();

        // Search CMS Pages
        if ($show_content) {
            // CMS Pages
            switch ($mode) {
                case 'basic':
                    $query = "SELECT SQL_CALC_FOUND_ROWS `page_id`, `link_name`, `file_name` FROM `" . TABLE_PAGES . "` WHERE "
                        . $subdomain->getOwnerSql() . " AND ("
                        . "`link_name` LIKE '%" . mysql_real_escape_string($_GET['q']) . "%' OR "
                        . "`file_name` LIKE '%" . mysql_real_escape_string(str_replace('.php', '', $_GET['q'])) . "%' OR "
                        . " `page_title` LIKE '%" . mysql_real_escape_string($_GET['q']) . "%'"
                        . ") ORDER BY `link_name` ASC LIMIT 11;";
                    break;
                case 'content':
                    $query = "SELECT SQL_CALC_FOUND_ROWS `page_id`, `link_name`, `file_name` FROM `" . TABLE_PAGES . "` WHERE "
                        . $subdomain->getOwnerSql() . " AND "
                        . "`category_html` LIKE '%" . mysql_real_escape_string($_GET['q']) . "%' "
                        . "ORDER BY `link_name` ASC LIMIT 11;";
                    break;
                default:
                    $query = false;
                    break;
            }

            $results['pages'] = array();
            if (!empty($query)) {
                if ($result = mysql_query($query)) {
                    while ($row = mysql_fetch_assoc($result)) {
                        $results['pages'][] = $row;
                        $total++;
                    }
                    list($results['pages_cnt']) = mysql_fetch_row(mysql_query("SELECT FOUND_ROWS();"));
                } else {
                    // Query Error
                    $errors[] = __('Error Searching CMS Pages');
                }
            }
        }

        $subdomain = $subdomainFactory->buildSubdomainFromRequest();
        $subdomainAuth = $subdomain->getAuth();

        // Search IDX Snippets
        if ($subdomainAuth->canManageIDXSnippets()) {
            // CMS Snippets
            switch ($mode) {
                case 'basic':
                    $query = "SELECT SQL_CALC_FOUND_ROWS `name` FROM `" . TABLE_SNIPPETS . "` WHERE "
                        . $subdomain->getOwnerSql() . " AND "
                        . "`name` LIKE '%" . mysql_real_escape_string(trim($_GET['q'], '#')) . "%' AND "
                        . "`type` IN ('cms', 'idx'"
                        . ($subdomainAuth->canManagePages() ? ", 'form'" : '')
                        . ") "
                        . "ORDER BY `name` ASC LIMIT 11;";
                    break;
                default:
                    $query = false;
                    break;
            }

            $results['snippets'] = array();
            if (!empty($query)) {
                if ($result = mysql_query($query)) {
                    while ($row = mysql_fetch_assoc($result)) {
                        $results['snippets'][] = $row;
                        $total++;
                    }
                    list($results['snippets_cnt']) = mysql_fetch_row(mysql_query("SELECT FOUND_ROWS();"));
                } else {
                    // Query Error
                    $errors[] = __('Error Searching IDX Snippets');
                }
            }
        }

        // Search Blog Entries
        if ($blogAuth->canManageEntries($authuser) || ($blogAuth->canManageSelf($authuser))) {

            // Only Manage self
            if (!$blogAuth->canManageEntries($authuser)) {
                $sql_self = " AND `agent` = " . $authuser->info('id') . ' ';
            }

            // Blog Results
            switch ($mode) {
                case 'basic':
                    $query = "SELECT SQL_CALC_FOUND_ROWS `id`, `link`, `title` FROM `" . TABLE_BLOG_ENTRIES . "` WHERE "
                        . "(`link` LIKE '%" . mysql_real_escape_string($_GET['q']) . "%' OR "
                        . "`title` LIKE '%" . mysql_real_escape_string($_GET['q']) . "%')"
                        . $sql_self
                        . " ORDER BY `timestamp_created` DESC LIMIT 11";
                    break;
                case 'content':
                    $query = "SELECT SQL_CALC_FOUND_ROWS `id`, `link`, `title` FROM `" . TABLE_BLOG_ENTRIES . "` WHERE "
                        . "`body` LIKE '%" . mysql_real_escape_string($_GET['q']) . "%' "
                        . $sql_self
                        . " ORDER BY `timestamp_created` DESC LIMIT 11";
                    break;
                default:
                    $query = false;
                    break;
            }

            $results['blog'] = array();
            if (!empty($query)) {
                if ($result = mysql_query($query)) {
                    while ($row = mysql_fetch_assoc($result)) {
                        $results['blog'][] = $row;
                        $total++;
                    }
                    list($results['blog_cnt']) = mysql_fetch_row(mysql_query("SELECT FOUND_ROWS();"));
                } else {
                    // Query Error
                    $errors[] = __('Error Searching Blog Entries');
                }
            }
        }

        // Search Agents
        if ($agentsAuth->canViewAgents($authuser)) {
            // Generate Phone Match
            $like = array();
            $digits = str_split(str_replace(array('+', '.', '-', ' ', '(', ')'), '', $_GET['q']));
            foreach ($digits as $i => $digit) {
                if (is_numeric($digit)) {
                    $like[] = $digit . (($i != count($digits) - 1) ? '[^[:digit:]]*' : '');
                } else {
                    // query is for something other than a phone number
                    $like = array();
                    break;
                }
            }
            $like = !empty($like) ? implode('', $like) : mysql_real_escape_string($_GET['q']);

            // Agent Results
            switch ($mode) {
                case 'basic':
                    $query = "SELECT SQL_CALC_FOUND_ROWS `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE "
                        . "`id` != 1 AND "
                        . "(CONCAT(`first_name`, ' ', `last_name`) LIKE '%" . mysql_real_escape_string($_GET['q']) . "%' OR "
                        . "`email` LIKE '%" . mysql_real_escape_string($_GET['q']) . "%') "
                        . (!empty($like) ?
                            " OR (`cell_phone` RLIKE '" . $like . "'"
                            . " OR `office_phone` RLIKE '" . $like . "'"
                            . " OR `home_phone` RLIKE '" . $like . "'"
                            . " OR `fax` RLIKE '" . $like . "')" : "")
                        . " ORDER BY `last_name` ASC, `first_name` ASC LIMIT 11;";
                    break;
                default:
                    $query = false;
                    break;
            }

            $results['agents'] = array();
            if (!empty($query)) {
                if ($result = mysql_query($query)) {
                    while ($row = mysql_fetch_assoc($result)) {
                        $results['agents'][] = $row;
                        $total++;
                    }
                    list($results['agents_cnt']) = mysql_fetch_row(mysql_query("SELECT FOUND_ROWS();"));
                } else {
                    // Query Error
                    $errors[] = __('Error Searching Agents');
                    $errors[] = mysql_error();
                }
            }
        }

        // Search Leads
        if ($leadsAuth->canManageLeads($authuser) || $leadsAuth->canViewOwn($authuser)) {
            // Agent Mode
            $sql_agent = !$leadsAuth->canManageLeads($authuser) ? '`%s`.`agent` = ' . $authuser->info('id') : '';

            // Generate Phone Match
            $like = array();
            $digits = str_split(str_replace(array('+', '.', '-', ' ', '(', ')'), '', $_GET['q']));
            foreach ($digits as $i => $digit) {
                if (is_numeric($digit)) {
                    $like[] = $digit . (($i != count($digits) - 1) ? '[^[:digit:]]*' : '');
                } else {
                    // query is for something other than a phone number
                    $like = array();
                    break;
                }
            }
            $like = !empty($like) ? implode('', $like) : mysql_real_escape_string($_GET['q']);

            // Search Query
            switch ($mode) {
                case 'basic':
                    $query = "SELECT SQL_CALC_FOUND_ROWS `id`, `first_name`, `last_name`, `email` FROM `" . LM_TABLE_LEADS . "` WHERE ("
                        . "(CONCAT(`first_name`, ' ', `last_name`) LIKE '%" . mysql_real_escape_string($_GET['q']) . "%' OR "
                        . "`email` LIKE '%" . mysql_real_escape_string($_GET['q']) . "%')"
                        . (!empty($like) ?
                            " OR (`phone` RLIKE '" . $like . "'"
                            . " OR `phone_work` RLIKE '" . $like . "'"
                            . " OR `phone_cell` RLIKE '" . $like . "'"
                            . " OR `phone_fax` RLIKE '" . $like . "')" : "")
                        . ")" . (!empty($sql_agent) ? ' AND ' . sprintf($sql_agent, LM_TABLE_LEADS) : '')
                        . " ORDER BY (`first_name` IS NULL && `last_name` IS NULL) ASC, `last_name` ASC, `first_name` ASC LIMIT 11;";
                    break;
                default:
                    $query = false;
                    break;
            }

            $results['leads'] = array();
            if (!empty($query)) {
                if ($result = mysql_query($query)) {
                    while ($row = mysql_fetch_assoc($result)) {
                        $results['leads'][] = $row;
                        $total++;
                    }
                    list($results['leads_cnt']) = mysql_fetch_row(mysql_query("SELECT FOUND_ROWS();"));
                } else {
                    // Query Error
                    $errors[] = __('Error Searching Leads');
                }
            }
        }
    }
}
