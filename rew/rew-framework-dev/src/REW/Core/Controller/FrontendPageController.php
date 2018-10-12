<?php

namespace REW\Core\Controller;

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;

/**
 * FrontendPageController
 * @package REW\Core\Controller
 */
class FrontendPageController extends AbstractController
{

    /**
     * The legacy "controller" file included to handle routing
     * @deprecated This is not intended to be used or shared
     * @var string
     */
    const PAGE_QUERY_FILE = 'page_query.php';

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @param SettingsInterface $settings,
     * @param PageInterface $page
     * @param DBInterface $db
     */
    public function __construct(SettingsInterface $settings, PageInterface $page, DBInterface $db)
    {
        $this->settings = $settings;
        $this->page = $page;
        $this->db = $db;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke()
    {
        // Extract expected global variables
        extract($this->getGlobalVars(), EXTR_OVERWRITE | EXTR_REFS);

        // Load the legacy "page_query" file
        $rootPath = $this->settings->DIRS['ROOT'];
        include $rootPath . static::PAGE_QUERY_FILE;

        // Output Content
        echo $row['category_html'];
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
            'page' => $this->page,
            'db' => $this->db,
            'row' => []
        ];
    }
}
