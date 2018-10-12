<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\ContainerInterface;

/**
 * BREW Framework Skin
 * @package REW
 * @subpackage Skin
 */
class Skin_BREW extends Skin
{

    /**
     * List of features that skin uses (used to define whether feature or feature configuration should display)
     * @var array of strings
     */
    protected static $features = array();

    /**
     * Directory
     * @var string
     */
    public static $directory = 'brew';

    /**
     * Name
     * @var string
     */
    protected $name = 'BREW';

    /**
     * @const string[]
     */
     const JAVASCRIPT_FILES = [
         '//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
         'js/rew.core.js',
         'js/rew.idx.js',
         'js/rew.oauth.js',
         'js/rew.legacy.js',
         'js/utils/saveSearch.js',
         'js/utils/editSearch.js',
     ];

    /**
     * Create Skin
     * @param string $directory
     * @param string $scheme
     * @param ContainerInterface $container
     * @uses Resource::__construct
     */
    public function __construct($directory = null, $scheme = 'default', ContainerInterface $container = null)
    {
        if ($container === null) {
            $container = Container::getInstance();
        }

        // Skin Directory
        if (is_null($directory)) {
            $directory = self::$directory;
        }

        // Create Skin
        $container->callConstructor($this, parent::class, ['directory' => $directory, 'scheme' => $scheme]);

        // Include core JS resources
        $this->addCoreJavaScriptFiles();
    }

    /**
     * Include core javascript files
     * @return void
     */
    protected function addCoreJavaScriptFiles()
    {
        $jsFiles = static::JAVASCRIPT_FILES;
        if (!empty($jsFiles) && is_array($jsFiles)) {
            foreach ($jsFiles as $jsFile) {
                $this->addJavascript($jsFile);
                ;
            }
        }
        if (Settings::getInstance()->SKIN !== 'REW\Theme\Enterprise\Theme') {
            $this->addJavascript('js/rew.core.gallery.js', 'static');
            $this->addJavascript('js/rew.core.carousel.js', 'static');
        }
        $this->addJavascript('js/rew.plugins.js', 'dynamic');
        $this->addJavascript('js/brew/Window.js', 'dynamic');
    }

    /**
     * @inheritDoc
     */
    public function bodyClass()
    {

        // Page Instance
        $page = $this->getPage();

        // User Session
        $user = User_Session::get();

        // Page Classes
        $classes = array();

        // Page Information
        $classes[] = $page->info('app');
        $classes[] = ($page->info('app') === 'cms' ? 'pg-' : '') . $page->info('name');
        $classes[] = Settings::getInstance()->SKIN;
        $classes[] = Settings::getInstance()->SKIN_SCHEME;
        $classes[] = Settings::getInstance()->IDX_FEED;

        // Is Logged In
        if ($user->isValid()) {
            $classes[] = 'logged-in';
        }

        // Popup Window
        if (isset($_GET['popup'])) {
            $classes[] = 'popup';
        }

        // Agent Sub-Domain
        if (Settings::getInstance()->SETTINGS['agent'] != 1) {
            $classes[] = 'agent-site';
        }

        // Listing Detail Pages
        if (in_array($page->info('name'), array('details', 'map', 'birdseye', 'streetview', 'local'))) {
            $classes[] = 'details';
        }

        // Multi-IDX
        if (!empty(Settings::getInstance()->IDX_FEEDS)) {
            $classes[] = 'multi-idx';
        }

        // Current Classes
        $classes[] = $page->info('class');

        // Page Template
        $template = $page->getTemplate();
        if ($template instanceof Page_Template) {
            $classes[] = 'tpl-' . $template->getName();
        }

        // Compliance Body Classes
        global $_COMPLIANCE;
        if (!empty($_COMPLIANCE['body_class'])) {
            $classes[] = $_COMPLIANCE['body_class'];
        }

        // Set Body Class
        $page->info('class', implode(' ', array_filter($classes)));
    }

    /**
     * Load Extra Skin Modules
     * @see Skin::loadModules()
     */
    public function loadModules(PageInterface $page)
    {

        // Multi-IDX
        if (!isset($_GET['popup']) && in_array($page->info('app'), array('idx', 'idx-map'))) {
            // Show IDX Feed Switcher on search pages
            if (in_array($page->info('name'), array('search', 'search_map'))) {
                if (!empty(Settings::getInstance()->IDX_FEEDS)) {
                    // Switcher container
                    $container = 'sidebar';

                    // Do not add to the barbara skin
                    if (Settings::getInstance()->SKIN === 'bcse') {
                        return;
                    }

                    // Do not add to the LEC-2015 skin
                    if (Settings::getInstance()->SKIN === 'lec-2015') {
                        return;
                    }

                    // Add feed switcher
                    if (!$page->container($container)->contains('idx-feeds')) {
                        $page->container($container)->addModule('idx-feeds', array(
                            'prepend' => true,
                            'wrap_module' => true
                        ));
                    }
                }
            }
        }
    }

    /**
     * Load Extra Sources for Page
     * @return void
     */
    protected function extraSources()
    {

        // Generate Body Class
        $this->bodyClass();

        // Root Directory
        $dir_root = $_SERVER['DOCUMENT_ROOT'];

        // Google Maps API
        if (!empty($_REQUEST['snippet']) // IDX Snippets
            || (in_array($this->page->info('app'), array('idx', 'idx-map')) && in_array($_GET['load_page'], array('details', 'map', 'streetview', 'birdseye', 'local', 'search', 'search_form', 'search_map'))) // IDX
            || ($this->page->info('app') == 'directory' && in_array($_GET['page'], array('details', 'edit'))) // Directory
        ) {
            $this->loadMapApi();
        }

        // User Session
        $user = User_Session::get();

        // Not in Popup, User is Logged In...
        if (!isset($_GET['popup']) && $user->isValid() && $_GET['load_page'] != 'login') {
            // New Un-Read Message
            $db = DB::get();
            $unread = $db->prepare(
                "SELECT `category` ".
                "FROM `users_messages` ".
                "WHERE `user_id` = :user_id ".
                    "AND `sent_from` = 'agent' ".
                    "AND `user_read` = 'N' ".
                    "AND `user_alert` = 'N' ".
                "ORDER BY `timestamp` ASC ".
                "LIMIT 1"
            );
            $unread->execute(array('user_id' => $user->info('id')));
            $unread = $unread->fetch();
            if (!empty($unread)) {
                // Mark as Alerted
                $update_messages = $db->prepare(
                    "UPDATE `users_messages` ".
                    "SET `user_alert` = 'Y' ".
                    "WHERE `category` = :category"
                );
                $update_messages->execute(array('category' => $unread['category']));

                $this->openMessage($unread['category']);

            // Open Dashboard
            } elseif (!empty($_SESSION['dashboard']) || isset($_GET['dashboard'])) {
                unset($_SESSION['dashboard']);
                $this->openDashboard();

            // Open Newsletter notice
            } elseif (isset($_GET['newsletter'])) {
                $this->openNewsletter($user, true);
            }

        // Open Dashboard
        } else if (!isset($_GET['popup']) && isset($_GET['dashboard'])) {
            $this->openDashboard();

        // Open Newsletter notice
        } elseif (isset($_GET['newsletter'])) {
            $this->openNewsletter($user, false);
        }

        parent::extraSources();
    }

    /**
     * Open popup window
     */
    protected function openPopup($params)
    {
        $window = "$.Window(%s)";
        if(is_integer($params["delay"])) {
            $window = sprintf("setTimeout(function(){%s}, %s);", $window, $params["delay"]);
            unset($params["delay"]);
        }
        $this->page->writeJS(sprintf($window, json_encode($params)));
    }

    /**
     * @inheritDoc
     */
    protected function addForcedVerification()
    {
        $this->openPopup([
            "iframe" => sprintf(Settings::getInstance()->SETTINGS['URL_IDX_VERIFY'], ''),
            "noClose" => true
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function addForcedRegistration(array $listing = null, $registration_required)
    {
        $this->openPopup([
            "iframe" => (!empty($property) ? $property['url_register'] : $listing['url_register']),
            "noClose" => $registration_required,
            "delay" => 0
        ]);
    }

    /**
     * Open popup window for user dashboard
     */
    public function openDashboard()
    {
        $this->openPopup([
            "iframe" => "/idx/dashboard.html"
        ]);
    }

    /**
     * Open popup window for Terms of service
     */
    public function openTos()
    {
        $this->openPopup([
            "iframe" => "/idx/tos.html",
            "noClose" => true
        ]);
    }

    /**
     * Open popup window to view user message
     * @param string $category
     */
    public function openMessage($category)
    {
        $this->openPopup([
            "iframe" => sprintf("/idx/dashboard.html?view=messages&thread=%s", $category)]
        );
    }

    /**
     * Open popup window to view user message
     * @param string $category
     */
    public function openNewsletter($user, $logged_in)
    {
        $email = trim($_POST['mi0moecs']) ?: $user->info('email');
        $user->saveInfo('email', $email);

        // Authenticate User and Build Login Token
        if($user->exists($email)) {
            // Load lead instance from ID
            $user_id = $user->getUserId();
            $lead = Backend_Lead::load($user_id);

            // Opt-in setting before signup
            $optIn = $lead->info('opt_marketing') === 'out';
            $user->saveInfo('newOptIn', $optIn);
            $user->setBackUrl($_SERVER['REQUEST_URI']);

            // Require password
            if (!$user->isValid()) {
                if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) {
                    header('Location: ' . Settings::getInstance()->SETTINGS['URL_IDX_LOGIN'] . "?opt_marketing=in");
                } else {
                    $user->authenticate($email);
                    header('Location: ' . $user->getBackUrl());
                }
            }

        } else {
            header('Location: ' . Settings::getInstance()->SETTINGS['URL_IDX_REGISTER'] . "?opt_marketing=in");
        }

        $this->openPopup([
            "iframe" => "/idx/newsletter.html",
            "closeOnClick" => true
        ]);
    }

    /**
     * Get namespace to use for object oriented modules. This is necessary to make inheritance without renaming
     * possible.
     * @return string
     */
    public function getModuleNamespace()
    {
        return parent::getModuleNamespace() . 'BREW\\';
    }
}
