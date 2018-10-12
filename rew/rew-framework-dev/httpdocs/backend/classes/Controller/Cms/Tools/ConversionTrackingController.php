<?php

namespace REW\Backend\Controller\Cms\Tools;

use Psr\Http\Message\ServerRequestInterface;
use REW\Backend\Controller\AbstractController;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\CMS\Interfaces\SubdomainFactoryInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Backend\Interfaces\NoticesCollectionInterface;

/**
 * IndexController
 * @package REW\Backend\Controller\Cms
 */
class ConversionTrackingController extends AbstractController
{
    /**
     * @var ServerRequestInterface
     */
    private $serverRequest;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var SubdomainFactoryInterface
     */
    protected $subdomainFactory;


    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var NoticesCollectionInterface
     */
    protected $notices;

    /**
     * @var Subdomain
     */

    protected $subdomain;

    /**
     * @var array
     */

    protected $subdomains;

    /**
     * @param ServerRequestInterface $serverRequest
     * @param SettingsInterface $settings
     * @param SubdomainFactoryInterface $subdomainFactory
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param NoticesCollectionInterface $notices
     */
    public function __construct(
        ServerRequestInterface $serverRequest,
        SettingsInterface $settings,
        SubdomainFactoryInterface $subdomainFactory,
        FactoryInterface $view,
        AuthInterface $auth,
        DBInterface $db,
        NoticesCollectionInterface $notices
    ) {
        $this->serverRequest = $serverRequest;
        $this->settings = $settings;
        $this->subdomainFactory = $subdomainFactory;
        $this->view = $view;
        $this->auth = $auth;
        $this->db = $db;
        $this->notices = $notices;
    }

    /**
     * @throws SystemErrorException If domain not found to manage
     * @throws PageNotFoundException If invalid filter selected
     */
    public function __invoke()
    {
        $body = $this->serverRequest->getParsedBody();
        $query = $this->serverRequest->getQueryParams();

        // Create Auth Classes
        $settings = $this->settings;

        // Get Authorization Managers
        $this->subdomain = $this->subdomainFactory->buildSubdomainFromRequest('canManageConversionTracking');
        if (!$this->subdomain) {
            throw new \REW\Backend\Exceptions\UnauthorizedPageException(
                'You do not have permission to manage Conversion Tracking.'
            );
        }
        $this->subdomain->validateSettings();
        $this->subdomains = $this->subdomainFactory->getSubdomainList('canManageConversionTracking');

        // Load Settings
        try {
            $settings = $this->db->fetch(sprintf("SELECT `settings` FROM `%s` WHERE %s;", TABLE_SETTINGS, $this->subdomain->getOwnerSql()));
            /* Throw Missing Settings Exception */
            if (empty($settings)) {
                throw new \REW\Backend\Exceptions\MissingSettingsException();
            } else {

                // Load Settings
                $settings = unserialize($settings['settings']);

                // Require Array
                $settings = !empty($settings) && is_array($settings) ? $settings : array(
                    // PPC Settings
                    'ppc' => array(
                        'enabled' => 'false',
                        'idx-register' => null,
                        'idx-inquire' => null,
                        'idx-showing' => null,
                        'idx-phone' => null,
                        'form-contact' => null,
                        'form-buyers' => null,
                        'form-seller' => null,
                        'form-approve' => null,
                        'form-cma' => null,
                        'rt-register' => null,
                    )
                );

                // Process Submit
                if (isset($query['submit'])) {
                    // Combine Settings
                    $settings = array_merge($settings, array(
                        'ppc' =>  $body['ppc']
                    ));

                   $this->save($settings);
                }
            }
        } catch (\PDOException $e) {}

        // Render template file
        echo $this->view->render('::pages/cms/tools/conversion-tracking', [
            'settings' => $settings,
            'postLink' => $this->subdomain->getPostLink(),
            'subdomainSelector' => $this->renderSubdomainSelector(),
            'view' => $this->view,
            'displayIdxPhone' => !empty($this->settings->MODULES['REW_PARTNERS_TWILIO']),
            'displayCMATool' => $this->settings->MODULES['REW_PROPERTY_VALUATION'],
            'displayRTReg' => $this->settings->MODULES['REW_RT']
        ]);
    }

    /**
     * @param Subdomain $subdomain
     * @param array $settings
     * @return array
     */

    public function save($settings, $subdomain = null)
    {
        if (empty($subdomain)) {
            $subdomain = $this->subdomain;
        }

        // Serialize Settings
        $data = serialize($settings);

        // Build UPDATE Query
        try {
            $this->db->prepare(sprintf("UPDATE `%s` SET `settings` = :settings  WHERE %s;", TABLE_SETTINGS, $subdomain->getOwnerSql()))
                ->execute([
                    "settings" => $data
                ]);

            // Success
            $this->notices->success('Your changes have successfully been saved.');

            // Query Error
        } catch (\PDOException $e) {
            $this->notices->error('Your changes could not be saved.');
        }
    }

    /**
     * @return string
     */

    private function renderSubdomainSelector()
    {
        return $this->view->render('::partials/subdomain/selector', [
            'subdomain' => $this->subdomain,
            'subdomains' => $this->subdomains,
        ]);
    }
}
