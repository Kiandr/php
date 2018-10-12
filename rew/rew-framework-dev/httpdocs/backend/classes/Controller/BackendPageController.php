<?php

namespace REW\Backend\Controller;

use REW\Backend\Interfaces\RouterInterface;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\Exceptions\PageNotFoundException;
use REW\Backend\Asset\Interfaces\LoaderInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\Page\BackendInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;

/**
 * BackendPageController
 * @package REW\Backend\Controller
 */
class BackendPageController extends AbstractController
{

    /**
     * @var NoticesCollectionInterface
     */
    protected $notices;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var BackendInterface
     */
    protected $page;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @param NoticesCollectionInterface $notices
     * @param LoaderInterface $loader
     * @param RouterInterface $router
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param BackendInterface $page
     * @param DBInterface $db
     */
    public function __construct(NoticesCollectionInterface $notices, LoaderInterface $loader, RouterInterface $router, FactoryInterface $view, AuthInterface $auth, BackendInterface $page, DBInterface $db)
    {
        $this->notices = $notices;
        $this->loader = $loader;
        $this->router = $router;
        $this->view = $view;
        $this->auth = $auth;
        $this->page = $page;
        $this->db = $db;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke()
    {

        // Get current route's path
        $routePath = $this->router->getRoute()->getPath();

        // Load old-style controller/template files
        $controller = $this->loader->getControllerFile($routePath);
        $template = $this->loader->getTemplateFile($routePath);

        // Define notice collections
        $errors = [];
        $warnings = [];
        $success = [];

        // No route files found - throw 404 exception
        if (is_null($controller) && is_null($template)) {
            throw new PageNotFoundException;
        }

        // Extract expected global variables
        extract($this->getGlobalVars(), EXTR_OVERWRITE | EXTR_REFS);

        // Include controller & template
        $files = [$controller, $template];
        foreach ($files as $file) {
            if (!is_null($file)) {
                require_once $file;
            }
        }

        // Set application notification messages
        $this->setNotices($success, $errors, $warnings);
    }

    /**
     * Set notification messages
     * @param array $success
     * @param array $errors
     * @param array $warnings
     */
    protected function setNotices($success, $errors, $warnings)
    {
        // Set success messages
        if (is_array($success)) {
            foreach ($success as $msg) {
                $this->notices->success($msg);
            }
        }
        // Set error messages
        if (is_array($errors)) {
            foreach ($errors as $error) {
                $this->notices->error($error);
            }
        }
        // Set warning messages
        if (is_array($warnings)) {
            foreach ($warnings as $warning) {
                $this->notices->warning($warning);
            }
        }
    }

    /**
     * Globals used in the PHP controller and TPL view files.
     * This is temporary, these global variables are to be removed.
     * @deprecated Use DIC properly
     * @return array
     */
    protected function getGlobalVars()
    {
        return [
            'authuser' => $this->auth,
            'page' => $this->page,
            'db' => $this->db
        ];
    }
}
