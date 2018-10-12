<?php

use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\Http\HostInterface;
use REW\Core\Interfaces\IDX\ComplianceInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use \PHPMailer\RewMailer as PHPMailer;

/**
 * IDX_Compliance is a class used to handle MLS Compliance Rules & Regulations
 *
 */
class IDX_Compliance implements ComplianceInterface
{
    /**
     * Feeds for which Compliance has been loaded
     * @var array $feeds
     */
    private $feeds = array();

    /**
     * Current feed for which Compliance settings have been loaded
     * @var string
     */
    private $feed;

    /**
     * Account ID for ListTrac web application
     * @var string
     */
    const ListTracID = 'REWS_100054';

    /**
     * To Email Address For Agent Subdomain Notifications
     * @var string
     */
    const IDX_SUBDOMAIN_EMAIL = 'subdomains@realestatewebmasters.com';

    /**
     * Store Whether A Feed Requires Users To Accept The Terms Of Service.
     * This Is Specifically Useful For Comingled Feeds.
     * @var boolean
     */
    private $terms_required = false;

    /**
     * Terms Of Service Page Array That Is Useful For Comingled Feeds.
     * @var array
     */
    private $tos_page = array();

    /**
     * @var IDXFactoryInterface
     */
    private $idxFactory;

    /**
     * @var PageInterface
     */
    private $page;

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @var HostInterface
     */
    private $httpHost;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var HooksInterface
     */
    private $hooks;

    /**
     * @var AuthInterface
     */
    private $authUser;

    /**
     * IDX_Compliance constructor.
     * @param IDXFactoryInterface $idxFactory
     * @param PageInterface $page
     * @param SettingsInterface $settings
     * @param HostInterface $httpHost
     * @param ContainerInterface $container
     * @param HooksInterface $hooks
     * @param AuthInterface $authUser
     */
    public function __construct(
        IDXFactoryInterface $idxFactory,
        PageInterface $page,
        SettingsInterface $settings,
        HostInterface $httpHost,
        ContainerInterface $container,
        HooksInterface $hooks,
        AuthInterface $authUser
    ) {
        $this->idxFactory = $idxFactory;
        $this->page = $page;
        $this->settings = $settings;
        $this->httpHost = $httpHost;
        $this->container = $container;
        $this->hooks = $hooks;
        $this->authUser = $authUser;
    }

    /**
     * Get Last Updated Time
     *
     * @param DatabaseInterface $db_idx
     * @param IDXInterface $idx
     * @param boolean $get_unixtime
     * @return string|int MySQL Timestamp or UNIX timestamp
     */
    public function lastUpdated(DatabaseInterface $db_idx = null, IDXInterface $idx = null, $get_unixtime = false)
    {
        $db_idx = is_null($db_idx) ? $this->idxFactory->getDatabase() : $db_idx;
        $idx = is_null($idx) ? $this->idxFactory->getIdx() : $idx;

        // The key value we're searching for
        $lu_key = $idx->getLastUpdatedKey();

        // The field we're searching in
        $lu_field = $idx->getLastUpdatedKeyField();

        // Return SQL time or UNIX time?
        $select = $get_unixtime ? "UNIX_TIMESTAMP(`LastUpdated`)" : "`LastUpdated`";

        // Base query used when querying for 0-1 ListingFeed values
        $query = "SELECT " . $select . " AS `LastUpdated` FROM `" . REWIDX_TABLE_INFO . "`";

        if (!empty($lu_key)) {
            if (is_array($lu_key)) {
                $lu_key = array_map(array($db_idx, 'cleanInput'), $lu_key);

                // Looking for multiple LastUpdated values
                $query = "SELECT MAX(" . $select . ") AS `LastUpdated`"
                    . " FROM `" . REWIDX_TABLE_INFO . "` WHERE `" . $lu_field . "` IN"
                    . " ('" . implode("', '", $lu_key) . "')";
            } else {
                $lu_key = $db_idx->cleanInput($lu_key);

                // Looking for a single ListingFeed
                $query .= " WHERE `" . $lu_field . "` = '" . $lu_key . "'";
            }
        }

        $query .= " LIMIT 1;";

        $last_updated = $db_idx->fetchQuery($query);

        // If the feed was mis-configured and the required label is not set,
        // revert to old functionality. This is to prevent potential compliance
        // breakage in this case.
        if (!empty($lu_key) && empty($last_updated['LastUpdated'])) {
            $query = "SELECT " . $select . " AS `LastUpdated` FROM `" . REWIDX_TABLE_INFO . "`"
                . " LIMIT 1;";

            $last_updated = $db_idx->fetchQuery($query);
        }

        return $last_updated['LastUpdated'];
    }

    /**
     * Reset loaded IDX feeds
     */
    public function resetFeeds()
    {
        $this->feeds = array();
        $this->load();
    }

    /**
     * Remove MLS Disclaimer for IDX
     * WARNING: Don't break the rules - that's on you!
     */
    public function hideDisclaimer()
    {
        global $_COMPLIANCE;
        unset($_COMPLIANCE['disclaimer']);
        unset($_COMPLIANCE['update_time']);
    }

    /**
     * Display MLS Disclaimer for IDX
     *
     * @param bool $force Force display
     * @param string $feed specify a feed disclaimer to display
     * @return void
     */
    public function showDisclaimer($force = false, $feed = "")
    {
        // Global Resources
        global $_COMPLIANCE, $listing;

        // Force the disclaimer
        $force = (isset($_COMPLIANCE['force_disclaimer']) ? $_COMPLIANCE['force_disclaimer']  : $force);

        // Details Page, No Listing Found
        if ($_GET['load_page'] == 'details' && empty($listing)) {
            return;
        }

        // Only show disclaimer where needed
        $showDisclaimer = ($force || (preg_match('/^idx/', $this->page->info('app'))) || (in_array($this->page->info('app'), ['cms', 'blog']) && !empty($_REQUEST['snippet'])));
        if (empty($showDisclaimer)) {
            return;
        }

        // If feed is specified, then use that.
        $feeds = (!empty($feed) ? array($feed) : $this->feeds);

        // If commingled feed, but listings not present in page scope load other feeds to get Compliance
        if (empty($listing) && count($this->feeds) == 1 && $_COMPLIANCE['commingled_feed_name'] == $this->feeds[0]) {
            foreach ($_COMPLIANCE['feeds'] as $compliance_feed) {
                if (!in_array($compliance_feed, $this->feeds)) {
                    $this->feeds[] = $compliance_feed;
                    $feeds[] = $compliance_feed;
                }
            }
        }

        // Exclude disclaimer on these pages...
        $excluded_pages = array('connect', 'register', 'login', 'remind', 'verify', 'newsletter');
        if (in_array($_GET['load_page'], $excluded_pages)) {
            return;
        }

        foreach ($feeds as $feed) {
            // Load settings
            $db_idx = $this->idxFactory->getDatabase($feed);
            $idx = $this->idxFactory->getIdx();

            $this->load($feed, $force);

            // IDX Last Update Time
            // Only show update time where needed
            $excluded_pages = array('connect', 'register', 'login', 'remind', 'verify', 'inquire', 'friend');
            $showUpdateTime = (!empty($_COMPLIANCE['update_time']) && !in_array($_GET['load_page'], $excluded_pages));
            if ($_COMPLIANCE['commingled_feed_name'] == $feed) {
                if (empty($listing)) {
                    // If using a commingled db, always set lastupdated to commingled time
                    $commingled_last_updated = $this->lastUpdated($db_idx, $idx);
                    // Don't display separate disclaimer for commingled db.
                    continue;
                } else if (isset($listing['ListingFeed'])) {
                    // If on details page, set last updated to commingled db
                    // Then load Compliance for feed specified in listing.
                    $last_updated = $this->lastUpdated($db_idx, $idx);
                    $this->load($listing['ListingFeed'], $force);
                }
            } else if (!isset($commingled_last_updated)) {
                // If not using a commingled db, set lastUpdated regularly
                $last_updated = $this->lastUpdated($db_idx, $idx);
            } else {
                // If using a commingled db, always set lastupdated to commingled time
                $last_updated = $commingled_last_updated;
            }

            // Start Bugger
            ob_start();
            // MLS Disclaimer
            if (is_callable($_COMPLIANCE['disclaimer'])) {
                $_COMPLIANCE['disclaimer'] = $_COMPLIANCE['disclaimer']($listing);
            }
            if (!empty($_COMPLIANCE['disclaimer'])) {
                $disclaimer = is_array($_COMPLIANCE['disclaimer']) ? implode($_COMPLIANCE['disclaimer']) : $_COMPLIANCE['disclaimer'];
                eval(' ?>' . $disclaimer . '<?php ');
                if (!$force) {
                    unset($_COMPLIANCE['disclaimer']);
                }
            }
            // Last Updated Time
            if (!empty($showUpdateTime)) {
                echo '<p class="disclaimer">Listing information last updated on ' . date('F jS, Y \a\t g:ia T', strtotime($last_updated)) . '.</p>';
                unset($_COMPLIANCE['update_time']);
            }
            // Display MLS Disclaimer
            $disclaimer = ob_get_clean();
            if (!empty($disclaimer)) {
                echo '<div class="mls-disclaimer">' . $disclaimer . '</div>';
            }
        }
    }

    /**
     * Display MLS Disclaimer for IDX
     * Call in footer for display on all pages
     *
     * @return void
     */
    public function showDisclaimerFooter()
    {
        // Global Resource
        global $_COMPLIANCE;

        // Acquire List Of IDX Feeds
        $feeds = !empty($this->settings['IDX_FEEDS']) ? array_keys($this->settings['IDX_FEEDS']) : array($this->settings['IDX_FEED']);

        while ($feed = current($feeds)) {
            // Load Feed
            $this->load($feed);

            // If commingled feed, load other feeds to get Compliance
            if ($_COMPLIANCE['commingled_feed_name'] == $feed) {
                foreach ($_COMPLIANCE['feeds'] as $compliance_feed) {
                    if (!in_array($compliance_feed, $feeds)) {
                        $feeds[] = $compliance_feed;
                    }
                }
                // Can skip the commingled feed as the disclaimer will be in the specific feed
                next($feeds);
                continue;
            }

            // MLS Disclaimer for Footer
            if (!empty($_COMPLIANCE['disclaimer_footer'])) {
                $disclaimer = is_array($_COMPLIANCE['disclaimer_footer']) ?
                    implode($_COMPLIANCE['disclaimer_footer']) :
                    $_COMPLIANCE['disclaimer_footer'];
                unset($_COMPLIANCE['disclaimer_footer']);
            }
            // Display MLS Disclaimer
            if (!empty($disclaimer)) {
                echo '<div align="center">' . $disclaimer . '</div>';
                unset($disclaimer);
            }
            next($feeds);
        }
    }

    /**
     * Display MLS Provider (Agent / Office)
     *
     * @return void
     */
    public function showProvider($listing = array())
    {
        // MLS Office / Agent
        global $_COMPLIANCE;

        // Use Compliance settings for specified feed if commingled db.
        if (isset($listing['ListingFeed'])) {
            $this->load($listing['ListingFeed']);
        }

        if (!empty($_COMPLIANCE['details']['show_extras'])) {
            foreach ($_COMPLIANCE['details']['show_extras'] as $extras) {
                if (!empty($listing[$extras])) {
                    echo ' <p class="extras">' . $listing[$extras] . '</p> ';
                }
            }
        }
        if (!empty($_COMPLIANCE['details']['show_agent']) || !empty($_COMPLIANCE['details']['show_office'])) {
            $provider_prefix = !empty($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : '';
            $providers = array();
            if (!empty($_COMPLIANCE['details']['show_status'])) {
                echo ' <span class="status">Status: ' . $listing['ListingStatus'] . '</span>';
            }
            if (!empty($_COMPLIANCE['details']['show_agent']) && !empty($listing['ListingAgent'])) {
                $providers[] = $listing['ListingAgent'];
            }
            if (!empty($_COMPLIANCE['details']['show_office']) && !empty($listing['ListingOffice'])) {
                $providers[] = $listing['ListingOffice'];
            }
            if (!empty($_COMPLIANCE['details']['show_office_phone']) && !empty($listing['ListingOfficePhoneNumber'])) {
                $providers[] = $listing['ListingOfficePhoneNumber'];
            }
            if (!empty($providers)) {
                echo '<p class="mls-provider">' . $provider_prefix . ' ' . implode(', ', $providers)  . '.</p>';
            }
        }
        if (!empty($_COMPLIANCE['details']['show_icon'])) {
            echo '<span class="icon">' . $_COMPLIANCE['details']['show_icon'] . '</span>';
        }
    }

    /**
     * Display MLS Provider (Agent / Office) for Results
     * Outputs html for provider compliance
     *
     * @param array $listing - The listing information to get provider info
     * @return void
     */
    public function showProviderResult($listing = array())
    {
        // MLS Office / Agent
        global $_COMPLIANCE;

        // Use Compliance settings for specified feed if commingled db.
        if (isset($listing['ListingFeed'])) {
            $this->load($listing['ListingFeed']);
        }

        if (!empty($_COMPLIANCE['results']['show_extras'])) {
            foreach ($_COMPLIANCE['results']['show_extras'] as $extras) {
                if (!empty($listing[$extras])) {
                    $extrasList[] = $listing[$extras];
                }
            }
            echo implode(', ', $extrasList)."<br/>";
        }

        $show_agent = $_COMPLIANCE['results']['show_agent'];
        if (is_callable($show_agent)) {
            $show_agent = $show_agent($listing);
        }

        $show_office = $_COMPLIANCE['results']['show_office'];
        if (is_callable($show_office)) {
            $show_office = $show_office($listing);
        }

        if ($show_agent || $show_office) {
            if (!empty($_COMPLIANCE['results']['lang']['provider'])) {
                echo $_COMPLIANCE['results']['lang']['provider'];
            }
            if ($show_agent) {
                echo ' <span class="agent">' . str_replace(',', ', ', $listing['ListingAgent']) . '</span>';
            }
            if ($show_office) {
                echo ' <span class="office">' . $listing['ListingOffice'] . '</span>';
            }
            if (!empty($_COMPLIANCE['results']['show_office_phone']) && !empty($listing['ListingOfficePhoneNumber'])) {
                echo ' <span class="office_phone">' . $listing['ListingOfficePhoneNumber'] . '</span>';
            }
        }
        if (!empty($_COMPLIANCE['results']['show_status'])) {
            echo ' <span class="status">Status: ' . $listing['ListingStatus'] . '</span>';
        }
        if (!empty($_COMPLIANCE['results']['show_icon'])) {
            echo '<span class="icon">' . $_COMPLIANCE['results']['show_icon'] . '</span>';
        }
    }

    /**
     * Load IDX Compliance Settings for feed
     *
     * @param string $feed
     * @return void
     */
    public function load($feed = null, $force = false)
    {
        // Default Feed
        $feed = !empty($feed) ? $feed : $this->settings['IDX_FEED'];

        // Don't reload
        if ($this->feed === $feed && !$force) {
            return;
        }

        // Find IDX Feed
        $path = realpath(__DIR__ . '/../../../idx/settings/' . $feed);
        if (empty($path)) {
            $feed = str_replace('_', '-', $feed);
            $path = realpath(__DIR__ . '/../../../idx/settings/' . $feed);
        }
        if (empty($path)) {
            $feed = str_replace('-', '', $feed);
            $path = realpath(__DIR__ . '/../../../idx/settings/' . $feed);
        }
        if (!empty($path)) {
            // Load Compliance Settings
            global $_COMPLIANCE;
            $settings = $path . '/Compliance.settings.php';
            if (file_exists($settings)) {
                require $settings;
            }

            // Set compliance feed
            $this->feed = $feed;

            // Set Terms Required If Necessary.  This Section Is Used Particularly For Comingled Feeds.
            if (empty($this->terms_required) && !is_callable($_COMPLIANCE['terms_required']) && !empty($_COMPLIANCE['terms_required'])) {
                $this->terms_required = $_COMPLIANCE['terms_required'];
                $this->tos_page = $_COMPLIANCE['terms_required']['pages']['tos'];
            // If Terms Are Already Loaded, Carry The Compliance Terms Over
            } else if (!empty($this->terms_required)) {
                $_COMPLIANCE['terms_required'] = $this->terms_required;
                $_COMPLIANCE['pages']['tos'] = $this->tos_page;
            }

            // Add to collection
            if (!in_array($feed, $this->feeds)) {
                $this->feeds[] = $feed;
            }
        }
    }

    /**
     * Send notification to IDX team that a brokerage has created an agent subdomain.
     *
     * @param string $first_name
     * @param string $last_name
     * @param string $mls
     * @param string $subdomain
     * @return boolean status of email true upon success; false otherwise.
     */
    public function sendAgentSubdomainRequest($first_name = null, $last_name = null, $mls_info = null, $subdomain = null)
    {
        list ($first_name, $last_name, $subdomain) = filter_var_array(array($first_name, $last_name, $subdomain), FILTER_SANITIZE_STRING);

        if ((!empty($first_name) && !empty($last_name)) && !empty($mls_info) && is_array($mls_info)) {
            $mailer = $this->container->make(PHPMailer::class);
            $mailer->CharSet = 'UTF-8';

            $mailer->From = $this->settings['SETTINGS']['EMAIL_NOREPLY'];
            $mailer->AddAddress(self::IDX_SUBDOMAIN_EMAIL);

            $mailer->Subject = "Agent Subdomain Enabled For " . $first_name .  " " . $last_name . " On Website: " . $this->httpHost->getDomain();
            $body = "Agent - " . $first_name . " " . $last_name . PHP_EOL;
            if (!empty($subdomain)) {
                $body .= "Website - " . sprintf(URL_AGENT_SITE, $subdomain) . PHP_EOL;
            }

            $body .= "Submitted MLS Board Request Information: " . PHP_EOL;
            foreach ($mls_info as $name => $mls) {
                $body .= "Feed: " . $mls['long_name'] . " (" . strtoupper($name) . ")" . PHP_EOL
                       . "Agent ID: " . $mls['agent_id'] . PHP_EOL . PHP_EOL;
            }

            $mailer->Body = $body;

            return $mailer->Send();
        } else {
            return false;
        }
    }

    /**
     * Send notification to IDX team that a brokerage has created a team subdomain.
     * @param string $team_name
     * @param string $mls
     * @return boolean status of email true upon success; false otherwise.
     */
    public function sendTeamSubdomainRequest($team_name, $mls_info = null)
    {
        $team_name = filter_var($team_name, FILTER_SANITIZE_STRING);

        if (!empty($team_name) && !empty($mls_info) && is_array($mls_info)) {
            $mailer = $this->container->make(PHPMailer::class);
            $mailer->CharSet = 'UTF-8';

            $mailer->From = $this->settings['SETTINGS']['EMAIL_NOREPLY'];
            $mailer->AddAddress(self::IDX_SUBDOMAIN_EMAIL);

            $mailer->Subject = "Agent Subdomain Enabled For " . $team_name . " On Website: " . $this->httpHost->getDomain();
            $body = "Team - " . $team_name . PHP_EOL;
            $body .= "Website - " . $this->httpHost->getDomain() . PHP_EOL;

            $body .= "Submitted MLS Board Request Information: " . PHP_EOL;
            foreach ($mls_info as $name => $mls) {
                $body .= "Feed: " . $mls['long_name'] . " (" . strtoupper($name) . ")" . PHP_EOL
                . "Agent ID: " . $mls['agent_id'] . PHP_EOL . PHP_EOL;
            }

            $mailer->Body = $body;

            return $mailer->Send();
        } else {
            return false;
        }
    }


    /**
     * Send notification to IDX team that an agent subdomain has been cancelled.
     *
     * @param Backend_Agent $agent
     * @return boolean status of email true upon success; false otherwise.
     */
    public function sendAgentSubdomainCancellationNotice(Backend_Agent $agent)
    {
        $first_name = filter_var($agent['first_name'], FILTER_SANITIZE_STRING);
        $last_name  = filter_var($agent['last_name'], FILTER_SANITIZE_STRING);

        $subdomain = filter_var($agent['cms_link'], FILTER_SANITIZE_STRING);

        $mlss = filter_var($agent['cms_idxs'], FILTER_SANITIZE_STRING);

        if (!empty($first_name) && !empty($last_name) && !empty($subdomain)) {
            $mailer = $this->container->make(PHPMailer::class);
            $mailer->CharSet = 'UTF-8';

            $mailer->From = $this->settings['SETTINGS']['EMAIL_NOREPLY'];
            $mailer->FromName = 'No Reply';
            $mailer->AddAddress(self::IDX_SUBDOMAIN_EMAIL);

            $mailer->Subject = "Agent Subdomain Cancellation Notice For " . $first_name .  " " . $last_name . " On Website: " . $this->httpHost->getDomain();
            $body            = "Agent - " . $first_name . " " . $last_name . PHP_EOL;
            $body           .= "Website - " . sprintf(URL_AGENT_SITE, $subdomain) . PHP_EOL;
            $body           .= "Approved MLSs: " . (!empty($mlss) ? str_replace(',', ', ', $mlss) : ' There were no MLS\' enabled at the time of cancellation.') . PHP_EOL;

            $mailer->Body = $body;

            return $mailer->Send();
        } else {
            return false;
        }
    }

    /**
     * Send notification to IDX team that an team subdomain has been cancelled.
     *
     * @param array $team
     * @return boolean status of email true upon success; false otherwise.
     */
    public function sendTeamSubdomainCancellationNotice($team)
    {
        $team_name = filter_var($team['name'], FILTER_SANITIZE_STRING);

        $subdomain = filter_var($team['subdomain_link'], FILTER_SANITIZE_STRING);

        $mlss = filter_var($team['subdomain_idxs'], FILTER_SANITIZE_STRING);

        if (!empty($team_name) && !empty($subdomain)) {
            $mailer = $this->container->make(PHPMailer::class);
            $mailer->CharSet = 'UTF-8';

            $mailer->From = $this->settings['SETTINGS']['EMAIL_NOREPLY'];
            $mailer->FromName = 'No Reply';
            $mailer->AddAddress(self::IDX_SUBDOMAIN_EMAIL);

            $mailer->Subject = "Team Subdomain Cancellation Notice For " . $team_name . " On Website: " . $this->httpHost->getDomain();
            $body            = "Team - " . $team_name . PHP_EOL;
            $body           .= "Website - " . sprintf(URL_AGENT_SITE, $subdomain) . PHP_EOL;
            $body           .= "Approved MLSs: " . (!empty($mlss) ? str_replace(',', ', ', $mlss) : ' There were no MLS\' enabled at the time of cancellation.') . PHP_EOL;

            $mailer->Body = $body;

            return $mailer->Send();
        } else {
            return false;
        }
    }

    /**
     * Checks to see if the main site or agent subdomain has access to an IDX feed.  Can be used to determine whether an IDX page should display.
     * @return boolean
     */
    public function hasFrontendIDXAccess()
    {
        $timer = Profile::timer()->stopwatch('<code>' . __METHOD__ . '</code>')->start();

        // Acquire List Of IDX Feeds
        $feeds = !empty($this->settings['IDX_FEEDS']) ? array_keys($this->settings['IDX_FEEDS']) : array($this->settings['IDX_FEED']);

        // If backend user is in team or agent mode and they have a subdomain
        if ($this->settings['SETTINGS']['agent'] !== 1) {
            $feeds = $this->idxFactory->parseFeeds($feeds);
        }

        $timer->stop();

        // Returns True If The Team or Agent CMS Has Access To Any IDX Feed The Main Site Does Or If Main Site, Return True If The Site Has Any Feeds At All
        return (($this->settings['SETTINGS']['agent'] !== 1 && (
                (!$this->settings['SETTINGS']['team'] && !empty($this->settings['SETTINGS']['agent_idxs']) && !empty($feeds) && array_intersect($this->settings['SETTINGS']['agent_idxs'], $feeds) != array()) ||
                ($this->settings['SETTINGS']['team'] && !empty($this->settings['SETTINGS']['team_idxs']) && !empty($feeds) && array_intersect($this->settings['SETTINGS']['team_idxs'], $feeds) != array())
        )) ||
                ($this->settings['SETTINGS']['agent'] === 1 && !empty($feeds)));
    }

    /**
     * Run in the back end to determine whether the certain features should be allowed.  A Team can be provided to check features allowed in the context of that team.
     * @param $team
     * @return boolean
     */
    public function hasBackendIDXAccess($team = null)
    {
        $timer = Profile::timer()->stopwatch('<code>' . __METHOD__ . '</code>')->start();

        // Acquire List Of IDX Feeds
        $feeds = !empty($this->settings['IDX_FEEDS']) ? array_keys($this->settings['IDX_FEEDS']) : array($this->settings['IDX_FEED']);

        // If backend user is in agent mode and they have a subdomain, or the user is editing a team
        if ($team || ($this->authUser->info('mode') == 'agent' && $this->authUser->info('cms') == 'true')) {
            $feeds = $this->idxFactory->parseFeeds($feeds);
        }

        // If It's An Agent or Team Cms Load Up The List Of Feeds The Agent Cms Has Access To
        if ($this->authUser->info('mode') == 'agent' && $this->authUser->info('cms') == 'true' && empty($this->settings['SETTINGS']['agent_idxs'])) {
            $agent = Backend_Agent::load($this->authUser->info('id'));
            $this->settings['SETTINGS']['agent_idxs'] = !empty($agent['cms_idxs']) ? explode(",", $agent['cms_idxs']) : array();
        }

        $timer->stop();

        // Returns True If The Agent CMS Has Access To Any IDX Feed The Main Site Does Or If An Admin With CMS Permissions, Return True If The Site Has Any Feeds At All
        return (($team && !empty($this->settings['SETTINGS']['team_idxs']) && array_intersect($this->settings['SETTINGS']['team_idxs'], $feeds) != array()) ||
            (!isset($team) && ($this->authUser->info('mode') == 'agent' && $this->authUser->info('cms') == 'true' && !empty($this->settings['SETTINGS']['agent_idxs']) && array_intersect($this->settings['SETTINGS']['agent_idxs'], $feeds) != array()) ||
            ((($this->authUser->info('mode') == 'admin' && $this->authUser->isSuperAdmin()) || ($this->authUser->info('mode') == 'admin' && $authuser->adminPermission(Auth::PERM_CMS_SNIPPETS))) && !empty($feeds))));
    }

    /**
     * Used for manipulating the search query of a commingled feed for agent subdomains. If the site is the
     * main site, or if the feed is not commingled, an empty string will be returned.
     * The result of the function will be appended to $search_where.
     * NOTE: This function requires that a search_where_callback setting is defined in a feed's IDX.settings.php
     * file.  Simply call this function from there to have the filtering put in place.
     * @param string $alias
     * @param IDXInterface $idx
     * @return string
     */
    public function agentSubdomainFilterComingled($alias, $idx)
    {
        $search_where = '';

        if ($this->settings['SETTINGS']['agent'] !== 1 && $idx->isCommingled()) {
            $alias = !empty($alias) ? "`" . $alias . "`." : '';

            $db_idx = $this->idxFactory->getDatabase($idx->getName());

            //Check Agent Teams
            if ($this->settings['SETTINGS']['agent']) {
                $subdomain_feeds = is_array($this->settings['SETTINGS']['team_idxs']) ? $this->settings['SETTINGS']['team_idxs'] : array($this->settings['SETTINGS']['team_idxs']);
            } else {
                $subdomain_feeds = is_array($this->settings['SETTINGS']['agent_idxs']) ? $this->settings['SETTINGS']['agent_idxs'] : array($this->settings['SETTINGS']['agent_idxs']);
            }

            $commingled_feeds = $idx->getFeeds();

            $feeds = array_intersect($commingled_feeds, $subdomain_feeds);

            foreach ($feeds as $feed) {
                $search_where .= $alias . "`" . $idx->field('ListingFeed') . "` = '" . $db_idx->cleanInput($feed) . "' OR ";
            }

            $search_where = rtrim($search_where, ' OR ');

            return $search_where;
        }

        return $search_where;
    }


    /**
     * Adds a listing formatted to the list tracker's specification to the Page object
     * @param Page $page
     * @param array $listing
     * @return void
     */
    public static function trackPageLoad(Page &$page, array $listing)
    {
        $trackable_listing_info = self::FormatListingTrackable($listing);

        foreach ($trackable_listing_info as $service => $data) {
            $page->info('trackable_listing' . $service, $data);
        }
    }

    /**
     * Returns an array of the listing formatted to the list tracker's specification
     * @param array $listing
     * @return array
     */
    public static function FormatListingTrackable(array $listing)
    {
        global $_COMPLIANCE;

        $tracking = array();

        if (!empty($_COMPLIANCE['tracking']) && is_array($_COMPLIANCE['tracking'])) {
            foreach ($_COMPLIANCE['tracking'] as $service => $data) {
                list ($account, $required_fields) = $data;

                $trackable_listing_info = array();
                foreach ($listing as $field => $value) {
                    if (in_array($field, $required_fields)) {
                        $trackable_listing_info[$field] = $value;
                    }
                }

                $tracking[$service] = $trackable_listing_info;
            }
        }

        return $tracking;
    }

    /**
     * Tracks a registration event with any registered trackers
     * @param int $backend_agent_id
     * @param bool $force
     */
    public function trackRegisterEvent($backend_agent_id, $force = false)
    {
        if (!$force && Skin::hasFeature(Skin::INLINE_POPUPS)) {
            $_SESSION['force_register_agent'] = $backend_agent_id;
        }

        global $_COMPLIANCE;
        if (!empty($page) && !empty($_COMPLIANCE['tracking']) && is_array($_COMPLIANCE['tracking'])) {
            // Find the listing agent. If auto-assign is turned off and this
            // is not an agent subdomain, this will be null.
            if (!empty($backend_agent_id) && ($backend_agent = Backend_Agent::load($backend_agent_id)) !== null) {
                $agent_ids = json_decode($backend_agent->info('agent_id'), true);
                if (!empty($agent_ids[$this->settings['IDX_FEED']])) {
                    $agent_id = $agent_ids[$this->settings['IDX_FEED']];
                } else {
                    $agent_id = null;
                }
                unset($backend_agent, $agent_ids);
            } else {
                $agent_id = null;
            }

            foreach (array_keys($_COMPLIANCE['tracking']) as $service) {
                // Call the Register function on the tracking script if possible.
                // If there is not an available parent, it means that we're on a
                // page without listing data in which case we have nothing to track.
                $this->page->addSource(
                    Source_Type::JAVASCRIPT,
                    "var ttTarget = parent || window; if (typeof(ttTarget) != 'undefined' && typeof(ttTarget.tracking_" . $service . ") != 'undefined') {"
                    . "  ttTarget.tracking_" . $service . ".Register(" . json_encode($agent_id) . ");"
                    . "}",
                    'dynamic',
                    false
                );
            }
        }
    }

    /**
     * Check if compliance setting exists
     * @param string $offset
     * @return mixed
     */
    public function offsetExists($offset)
    {
        global $_COMPLIANCE;
        return isset($_COMPLIANCE[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function &offsetGet($offset)
    {
        global $_COMPLIANCE;
        return $_COMPLIANCE[$offset];
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        global $_COMPLIANCE;
        $_COMPLIANCE[$offset] = $value;
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        global $_COMPLIANCE;
        unset($_COMPLIANCE[$offset]);
    }
}
