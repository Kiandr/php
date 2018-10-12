<?php

namespace REW\Backend\Controller\Leads;

use REW\Backend\Auth\LeadsAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\View\Interfaces\FactoryInterface;
use Backend_Lead;

class IndexController extends AbstractController
{
    /**
     * Twig Template File
     */
    const TEMPLATE_FILE = __DIR__ . '/../../../assets/views/pages/leads/index.html.twig';

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var LeadsAuth
     */
    protected $leadsAuth;

    /**
     * @var FactoryInterface
     */
    protected $renderer;

    /**
     * @param AuthInterface $auth
     * @param FactoryInterface $renderer
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        FactoryInterface $renderer,
        SettingsInterface $settings
    ) {
        // Setup Dependencies
        $this->auth = $auth;
        $this->leadsAuth = new LeadsAuth($settings);
        $this->renderer = $renderer;
    }

    /**
     * Invoke - Check permissions and render template
     */
    public function __invoke()
    {
        $this->checkPagePermissions();

        // Render template file
        echo $this->renderer->render(self::TEMPLATE_FILE);
    }

    /**
     * Check the current authuser's page access permissions
     *
     * @throws UnauthorizedPageException
     */
    protected function checkPagePermissions()
    {
        if (!$this->leadsAuth->canManageLeads($this->auth)) {
            if ($_GET['view'] == 'all-leads') {
                throw new UnauthorizedPageException('You do not have permission to view all leads');
            }
            if (!$this->leadsAuth->canViewOwn($this->auth)) {
                throw new UnauthorizedPageException('You do not have permission to view leads');
            }
        }
    }
}
