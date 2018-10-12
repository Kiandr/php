<?php

namespace REW\Backend\Controller\Settings\Idx;

use REW\Backend\Auth\SettingsAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\MissingIdxMetaException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\DBInterface;
use Format;
use Lang;

/**
 * MetaController
 * @package REW\Backend\Controller\Cms
 */
class MetaController extends AbstractController
{

    /**
     * @var SettingsAuth
     */
    protected $settingsAuth;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var NoticesCollectionInterface
     */
    protected $notices;

    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var SkinInterface
     */
    protected $skin;

    /**
     * @var IDXInterface
     */
    protected $idx;

    /**
     * @var DatabaseInterface
     */
    protected $dbIdx;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @param SettingsAuth $settingsAuth
     * @param NoticesCollectionInterface $notices
     * @param SettingsInterface $settings
     * @param DatabaseInterface $dbIdx
     * @param FactoryInterface $view
     * @param SkinInterface $skin
     * @param AuthInterface $auth
     * @param IDXInterface $idx
     * @param DBInterface $db
     */
    public function __construct(
        SettingsAuth $settingsAuth,
        NoticesCollectionInterface $notices,
        SettingsInterface $settings,
        DatabaseInterface $dbIdx,
        FactoryInterface $view,
        SkinInterface $skin,
        AuthInterface $auth,
        IDXInterface $idx,
        DBInterface $db
    ) {
        $this->settingsAuth = $settingsAuth;
        $this->settings = $settings;
        $this->notices = $notices;
        $this->dbIdx = $dbIdx;
        $this->view = $view;
        $this->skin = $skin;
        $this->auth = $auth;
        $this->idx = $idx;
        $this->db = $db;
    }

    /**
     * @throws UnauthorizedPageException If not authorized to view page
     * @throws MissingIdxMetaException If IDX settings failed to load
     */
    public function __invoke()
    {

        // Authorized to manage IDX meta information
        if (!$this->settingsAuth->canManageIdxMeta($this->auth)) {
            throw new UnauthorizedPageException(__('You do not have permission to view idx settings'));
        }

        // Require IDX system settings
        if (!$system = $this->getIdxSettings()) {
            throw new MissingIdxMetaException;
        }

        // IDX language settings
        $lang = $_POST['lang'] ?: [];
        $language = $this->getIdxLanguage();
        $listingTags = $language['IDX_LISTING_TAGS'] ?: [];

        // Save IDX system settings on POST submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Remove unknown array keys
                foreach ($lang as $k => $v) {
                    if (!isset($language[$k])) {
                        unset($lang[$k]);
                    }
                }

                // Format listing URL
                if ($link = Format::slugify($lang['IDX_LISTING_URL'], '/[^a-zA-Z0-9_\-\{\}]/')) {
                    foreach ($listingTags as $listingTag) {
                        if (!in_array($listingTag, ['ListingMLS', 'ListingRemarks', 'ListingDOM'])) {
                            $link = str_ireplace(
                                sprintf('{%s}', $listingTag),
                                sprintf('[%s]', $listingTag),
                                $link
                            );
                        }
                    }
                }
                $link = str_replace(['{', '}'], '', $link);
                $lang['IDX_LISTING_URL'] = str_replace(['[', ']'], ['{', '}'], $link);

                // Generate query string to save IDX settings
                $queryString = sprintf("INSERT INTO `%s` SET
                    `idx`                  = :feed,
                    `language`             = :language,
                    `timestamp_created`    = NOW()
                    ON DUPLICATE KEY UPDATE
                    `language`             = :language,
                    `timestamp_updated`    = NOW()
                ;", TABLE_IDX_SYSTEM);

                // Query parameters
                $queryParams = [
                    'feed'     => $_POST['feed'],
                    'language' => json_encode($lang)
                ];

                // Execute database query
                $query = $this->db->prepare($queryString);
                $query->execute($queryParams);

                // Display success notification
                $this->notices->success(__('IDX Meta Information has successfully been saved.'));

                // Redirect back to settings form
                $feed = isset($_POST['feed']) ? sprintf('&feed=%s', urlencode($_POST['feed'])) : '';
                header(sprintf('Location: ?success%s', $feed));
                exit;

            // Database error occurred
            } catch (\PDOException $e) {
                $this->notices->error(__('IDX Meta Information could not be saved, please try again.'));
            }
        }

        // Use $_POST data for language
        foreach ($language as $k => $v) {
            if (isset($lang[$k])) {
                $language[$k] = $lang[$k];
            }
        }

        // Listing meta information
        $metaInfo = $this->getIdxMetaInformation();
        $metaData = json_decode($system['language'], true);
        $metaData = is_array($metaData) ? $metaData : $language;
        foreach ($metaInfo as $metaFields) {
            foreach ($metaFields as $metaField) {
                if (!isset($metaData[$metaField])) {
                    $metaData[$metaField] = $language[$metaField];
                }
                $metaData[$metaField] = html_entity_decode($metaData[$metaField], ENT_NOQUOTES, 'UTF-8');
            }
        }

        // Listing link URL
        $placeholderUrl = $language['IDX_LISTING_URL'];
        $listingLink = Format::slugify($placeholderUrl, '/[^a-zA-Z0-9_\-\{\}]/', false);
        $listingUrl = sprintf('%slisting/%s%%s', $this->settings->SETTINGS['URL'], $language['IDX_LISTING_URL_PREFIX']);

        // Check if responsive saved search email template exists
        $skin_directory = $this->skin->getDirectory();
        $indexTemplate = $this->skin->getSavedSearchEmailPath() . "index.php";

        // Render template file
        echo $this->view->render('::pages/settings/idx/meta', [
            'metaData' => $metaData,
            'metaInfo' => $metaInfo,
            'idxFeed' => $this->settings->IDX_FEED,
            'listingUrl' => $listingUrl,
            'listingLink' => $listingLink,
            'listingTags' => $listingTags,
            'placeholderUrl' => $placeholderUrl,
            'listingUrlTags' => array_filter($listingTags, function ($tag) {
                return !in_array($tag, ['ListingMLS', 'ListingRemarks', 'ListingDOM']);
            }),
            'saved_search_email_responsive_template_exists' => $this->view->exists($indexTemplate)
        ]);
    }

    /**
     * @return array
     */
    public function getIdxSettings()
    {
        $queryString = "SELECT * FROM `%s` WHERE `idx` = ? LIMIT 1";
        $query = $this->db->prepare(sprintf($queryString, TABLE_IDX_SYSTEM));
        $query->execute([$this->settings->IDX_FEED]);
        if ($settings = $query->fetch()) {
            return $settings;
        }
        $query->execute(['']);
        return $query->fetch();
    }

    /**
     * @return array
     */
    public function getIdxLanguage()
    {
        return Lang::$lang;
    }

    /**
     * @return array
     */
    public function getIdxMetaInformation()
    {
        $modules = $this->settings->MODULES;
        return array_filter([
            'Listing Details' => [
                'Page Title'        => 'IDX_DETAILS_PAGE_TITLE',
                'Meta Description'  => 'IDX_DETAILS_META_DESCRIPTION'
            ],
            'Google Map' . ($modules['REW_IDX_DIRECTIONS'] ? ' & Directions' : '') => (
                $modules['REW_IDX_MAPPING'] ? [
                    'Page Title'        => 'IDX_DETAILS_MAP_PAGE_TITLE',
                    'Meta Description'  => 'IDX_DETAILS_MAP_META_DESCRIPTION'
                ] : null
            ),
            'Bird\'s Eye View' => $modules['REW_IDX_BIRDSEYE'] ? [
                'Page Title'        => 'IDX_DETAILS_BIRDSEYE_PAGE_TITLE',
                'Meta Description'  => 'IDX_DETAILS_BIRDSEYE_META_DESCRIPTION'
            ] : null,
            'Streetview' => $modules['REW_IDX_STREETVIEW'] ? [
                'Page Title'        => 'IDX_DETAILS_STREETVIEW_PAGE_TITLE',
                'Meta Description'  => 'IDX_DETAILS_STREETVIEW_META_DESCRIPTION'
            ] : null,
            'Get Local' => $modules['REW_IDX_ONBOARD'] ? [
                'Page Title'        => 'IDX_DETAILS_ONBOARD_PAGE_TITLE',
                'Meta Description'  => 'IDX_DETAILS_ONBOARD_META_DESCRIPTION'
            ] : null,
            'Inquire Form' => [
                'Page Title'        => 'IDX_DETAILS_INQUIRE_PAGE_TITLE',
                'Meta Description'  => 'IDX_DETAILS_INQUIRE_META_DESCRIPTION'
            ],
            'Request Showing' => [
                'Page Title'        => 'IDX_DETAILS_SHOWING_PAGE_TITLE',
                'Meta Description'  => 'IDX_DETAILS_SHOWING_META_DESCRIPTION'
            ],
            'Send to Friend' => [
                'Page Title'        => 'IDX_DETAILS_SHARE_PAGE_TITLE',
                'Meta Description'  => 'IDX_DETAILS_SHARE_META_DESCRIPTION'
            ],
            'Send to Mobile Device' => $modules['REW_PARTNERS_TWILIO'] ? [
                'Page Title'        => 'IDX_DETAILS_PHONE_PAGE_TITLE',
                'Meta Description'  => 'IDX_DETAILS_PHONE_META_DESCRIPTION'
            ] : null,
            'Listing Not Found (404 Error)' => [
                'Page Title'        => 'IDX_DETAILS_PAGE_TITLE_MISSING',
                'Meta Description'  => 'IDX_DETAILS_META_DESCRIPTION_MISSING'
            ],
            'Registration Form' => [
                'Page Title'        => 'IDX_REGISTER_PAGE_TITLE',
                'Meta Description'  => 'IDX_REGISTER_META_DESCRIPTION'
            ],
            'Login Form' => [
                'Page Title'        => 'IDX_LOGIN_PAGE_TITLE',
                'Meta Description'  => 'IDX_LOGIN_META_DESCRIPTION'
            ],
            'Social Connect' => [
                'Page Title'        => 'IDX_CONNECT_PAGE_TITLE',
                'Meta Description'  => 'IDX_CONNECT_META_DESCRIPTION'
            ]
        ]);
    }
}
