<?php

namespace REW\Backend\Controller\Cms;

use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\PageNotFoundException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Exceptions\MissingId\MissingAgentException;
use REW\Backend\Exceptions\MissingId\MissingTeamException;
use REW\Backend\Exceptions\MissingId\MissingPageException;
use REW\Backend\Exceptions\SystemErrorException;
use REW\Backend\CMS\Interfaces\SubdomainFactoryInterface;
use REW\Backend\Interfaces\LoaderInterface;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\CMS\Interfaces\SubdomainInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Pagination\Cursor;
use REW\Pagination\Pagination;

/**
 * IndexController
 * @package REW\Backend\Controller\Cms
 */
class IndexController extends AbstractController
{

    /**
     * Filter: All pages
     * @var string
     */
    const FILTER_ALL = 'all';

    /**
     * Filter: Navigation pages
     * @var string
     */
    const FILTER_NAV = 'nav';

    /**
     * Filter: Hidden pages
     * @var string
     */
    const FILTER_HIDDEN = 'hidden';

    /**
     * Default filter
     * @var string
     */
    const DEFAULT_FILTER = self::FILTER_ALL;

    /**
     * @var SubdomainFactoryInterface
     */
    protected $subdomainFactory;

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
     * @var DBInterface
     */
    protected $db;

    /**
     * @var SubdomainInterface
     */
    protected $domain;

    /**
     * @var array SubdomainInterface
     */
    protected $domain_list;

    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * @var string $search
     */
    private $search = "";

    /**
     * @var array $searchParams
     */
    private $searchParams = [];

    /**
     * @param SubdomainFactoryInterface $subdomainFactory
     * @param NoticesCollectionInterface $notices
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param DBInterface $db
     */
    public function __construct(
        SubdomainFactoryInterface $subdomainFactory,
        NoticesCollectionInterface $notices,
        FactoryInterface $view,
        AuthInterface $auth,
        DBInterface $db
    ) {
        $this->subdomainFactory = $subdomainFactory;
        $this->notices = $notices;
        $this->view = $view;
        $this->auth = $auth;
        $this->db = $db;
    }

    /**
     * @throws SystemErrorException If domain not found to manage
     * @throws PageNotFoundException If invalid filter selected
     */
    public function __invoke()
    {

        // Pagination
        $this->pagination = $this->paginate();

        // Get subdomain to manage or throw system error
        $subdomain = $this->getCurrentSubdomain();

        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle request to delete requested page record
            if (isset($_POST['delete']) && !empty($_POST['delete'])) {
                $this->deletePage($subdomain, (int) $_POST['delete']);
                $this->notices->success('Page has successfully been deleted.');

            // Handle request to update order of navigation pages
            } else if (isset($_POST['pages']) && is_array($_POST['pages'])) {
                header('Content-type: application/json');
                $pageIds = array_values($_POST['pages']);
                $this->orderPages($subdomain, $pageIds);
                echo json_encode(['ok' => true]);
                exit;
            }
        }

        // Handle search request
        if (isset($_GET['search'])) {
            $this->searchPages();
        }

        // Get current page filter
        $filter = $this->getFilter();
        $filters = $this->getFilters();

        // Invalid page filter selected
        if (!isset($filters[$filter])) {
            throw new PageNotFoundException;
        }

        // Get Homepage
        $subdomain = $this->getCurrentSubdomain();
        $subdomainAuth = $subdomain->getAuth();
        $homepage = $subdomainAuth->canManageHomepage()
            && $filter === self::FILTER_ALL;

        // Get pages for current subdomain/filter
        $pages = $subdomainAuth->canManagePages() ? $this->getPages($subdomain, $filter) : [];


        // Append Filter
        $searchFilter = $this->getSearchFilter();
        $appendFilter = !empty($filter) ? 'filter=' . $filter : '';
        $appendFilter .= !empty($searchFilter) ? $searchFilter : '';
        $appendFilter .= !empty($subdomain->getPostLink(true)) ? $subdomain->getPostLink(true) : '';

        // Add 'can_delete' key to page array
        $pages = array_map(function ($page) use ($appendFilter) {
            $page['can_delete'] = $this->canDeletePage($page);
            // Delete Form
            $page['delete'] = $page['page_id'];
            if (!empty($_GET['after'])) {
                $page['deleteFormAction'] = sprintf('?after=%s&%s', $_GET['after'], $appendFilter);
            } else if (!empty($_GET['before'])) {
                $page['deleteFormAction'] = sprintf('?before=%s&%s', $_GET['before'], $appendFilter);
            } else {
                $page['deleteFormAction'] = sprintf('?%s', $appendFilter);
            }
            if (isset($pages['subpages']) && is_array($pages['subpages'])) {
                $pages['subpages'] = array_map(function ($subpage) use ($appendFilter) {
                    $subpage['can_delete'] = $this->canDeletePage($subpage);
                    // Delete Form
                    $subpage['delete'] = $subpage['page_id'];
                    if (!empty($_GET['after'])) {
                        $subpage['deleteFormAction'] = sprintf('?after=%s&%s', $_GET['after'], $appendFilter);
                    } else if (!empty($_GET['before'])) {
                        $subpage['deleteFormAction'] = sprintf('?before=%s&%s', $_GET['before'], $appendFilter);
                    } else {
                        $subpage['deleteFormAction'] = sprintf('?%s', $appendFilter);
                    }
                    return $subpage;
                }, $pages['subpages']);
            }
            return $page;
        }, $pages);

        // Pagination link URLs
        $nextLink = $this->pagination->getNextLink();
        $nextLink .= !empty($nextLink) ? '&' . $appendFilter : '';
        $prevLink = $this->pagination->getPrevLink();
        $prevLink .= !empty($prevLink) ? '&' . $appendFilter : '';

        // Show homepage on first page only
        $firstPage = empty($prevLink) ? true : false;

        // Render template file
        echo $this->view->render('::pages/cms/default', [
            'canSort' => $filter === self::FILTER_NAV,
            'homepage' => $homepage,
            'subdomain' => $subdomain,
            'subdomainSelector' => $this->view->render('::partials/subdomain/selector', [
                'subdomain' => $subdomain,
                'subdomains' => $this->getSubdomainList(),
            ]),
            'filters' => $filters,
            'filter' => $filter,
            'subdomainPostLink' => $subdomain->getPostLink(true),
            'pages' => $pages,
            'paginationLinks' => ['nextLink' => $nextLink, 'prevLink' => $prevLink],
            'firstPage' => $firstPage
        ]);
    }

    /**
     * Update order of pages
     * @param SubdomainInterface $subdomain
     * @param int[] $pageIds
     * @return void
     */
    public function orderPages(SubdomainInterface $subdomain, array $pageIds)
    {
        $queryString = "UPDATE `pages` SET
            `category_order` = IF(`is_main_cat` = 't', :order, `category_order`),
            `subcategory_order` = IF(`is_main_cat` = 'f', :order, `subcategory_order`)
            WHERE `page_id` = :page_id AND %s
        ;";
        $updateQuery = $this->db->prepare(sprintf($queryString, $subdomain->getOwnerSql()));
        $order = 0;
        foreach ($pageIds as $pageId) {
            $updateQuery->execute([
                'page_id' => $pageId,
                'order' => $order++
            ]);
        }
    }

    /**
     * Delete content page
     * @param SubdomainInterface $subdomain
     * @param int $pageId
     * @throws MissingPageException If requested page not found
     * @throws SystemErrorException If database error occurred
     * @throws UnauthorizedPageException If not allowed to delete
     * @return void
     */
    public function deletePage(SubdomainInterface $subdomain, $pageId)
    {

        try {
            // Selected request page from database
            $queryString = "SELECT `file_name` FROM `pages` WHERE `page_id` = ? AND %s;";
            $pageQuery = $this->db->prepare(sprintf($queryString, $subdomain->getOwnerSql()));
            $pageQuery->execute([$pageId]);
            if (!$page = $pageQuery->fetch()) {
                throw new MissingPageException;
            }

            // Database error occurred
        } catch (\PDOException $e) {
            throw new SystemErrorException(__('An error occurred while deleting the requested page.'));
        }

        // Check if allowed to delete page
        if (!$this->canDeletePage($page)) {
            throw new UnauthorizedPageException(
                __('You do not have the proper permissions to perform this action.')
            );
        }

        try {
            // Perform row deletion in transaction
            $this->db->query("START TRANSACTION;");

            // Delete page from database
            $queryString = "DELETE FROM `pages` WHERE `page_id` = ? AND %s LIMIT 1;";
            $deleteQuery = $this->db->prepare(sprintf($queryString, $subdomain->getOwnerSql()));
            $deleteQuery->execute([$pageId]);

            // Re-assign children pages as top main pages
            $queryString = "UPDATE `pages` SET `category` = `file_name`, `is_main_cat` = 't', `hide` = 't' WHERE `category` = ? AND `is_main_cat` = 'f' AND %s;";
            $updateQuery = $this->db->prepare(sprintf($queryString, $subdomain->getOwnerSql()));
            $updateQuery->execute([$page['file_name']]);

            // Commit performed queries
            $this->db->query("COMMIT;");

            // Database error occurred
        } catch (\PDOException $e) {
            // Rollback performed queries
            $this->db->query("ROLLBACK;");

            // Throw human-friendly error exception
            throw new SystemErrorException(__('An error occurred while deleting the requested page.'));
        }
    }

    /**
     * Get current page filter
     * @return string
     */
    public function getFilter()
    {
        return $_GET['filter'] ?: self::DEFAULT_FILTER;
    }

    /**
     * Get available page filters
     * @return array
     */
    public function getFilters()
    {
        return [
            self::FILTER_ALL => __('All Pages'),
            self::FILTER_NAV => __('Navigation'),
            self::FILTER_HIDDEN => __('Hidden Pages')
        ];
    }

    /**
     * Get current search filter
     * @return string
     */
    private function getSearchFilter()
    {
        $filter = '';
        $filter .= !empty($_GET['page_title']) ? '&page_title=' . urlencode($_GET['page_title']) : '';
        $filter .= !empty($_GET['file_name']) ? '&file_name=' . urlencode($_GET['file_name']) : '';
        $filter .= !empty($_GET['link_name']) ? '&link_name=' . urlencode($_GET['link_name']) : '';
        $filter .= !empty($_GET['page_title']) || !empty($_GET['file_name']) || !empty($_GET['link_name']) ? '&search=' : '';
        return $filter;
    }

    /**
     * Get pages to manage for provided subdomain &filter
     * @param SubdomainInterface $subdomain
     * @param string $filter
     * @return array
     */
    public function getPages(SubdomainInterface $subdomain, $filter)
    {
        if ($filter === self::FILTER_ALL) {
            return $this->getAllPages($subdomain);
        } elseif ($filter === self::FILTER_NAV) {
            return $this->getNavPages($subdomain);
        } elseif ($filter === self::FILTER_HIDDEN) {
            return $this->getHiddenPages($subdomain);
        }
        return [];
    }

    /**
     * Get all pages
     * @param SubdomainInterface $subdomain
     * @return array
     */
    public function getAllPages(SubdomainInterface $subdomain)
    {
        $where = implode(' AND ', array_filter([$subdomain->getOwnerSql(), $this->search]));
        return $this->fetchPages($where, "", true);
    }

    /**
     * Get navigation pages
     * @param SubdomainInterface $subdomain
     * @return array
     */
    public function getNavPages(SubdomainInterface $subdomain)
    {
        $where = implode(' AND ', [$subdomain->getOwnerSql(), "`hide` = 'f'", "`is_main_cat` = 't'"]);
        $navPages = $this->fetchPages($where, "`is_main_cat` ASC, `category_order` ASC");
        foreach ($navPages as $k => $navPage) {
            if ($navPage['is_link'] !== 't') {
                $navPages[$k]['subpages'] = $this->fetchPages(implode(' AND ', [
                    $subdomain->getOwnerSql(),
                    "`hide` = 'f'", "`is_main_cat` = 'f'",
                    sprintf("`category` = '%s'", $navPage['file_name'])
                ]), "`is_main_cat` ASC, (`category_order` * 100) + `subcategory_order` ASC");
            }
        }
        return $navPages;
    }

    /**
     * Get hidden pages
     * @param SubdomainInterface $subdomain
     * @return array
     */
    public function getHiddenPages(SubdomainInterface $subdomain)
    {
        $where = implode(' AND ', array_filter([$subdomain->getOwnerSql(), "`hide` = 't'", $this->search]));
        return $this->fetchPages($where, "", true);
    }

    /**
     * Fetch content pages as requested
     * @param string $where WHERE sql
     * @param string $orderBy ORDER BY sql
     * @return array
     */
    public function fetchPages($where, $orderBy, $usePagination = false)
    {
        $limitQuery = "";
        $params = [];
        if ($usePagination) {
            $limit = $this->pagination->getLimit();
            $limitQuery = $limit ? " LIMIT " . $limit : "";
            $order = $this->pagination->getOrder();
            $orderQuery = "";
            foreach ($order as $sort => $dir) {
                $orderQuery .= "`" . $sort . "` " . $dir . ", ";
            };
            $orderQuery = rtrim($orderQuery, ", ");
            $orderBy = $orderQuery;
            $paginationWhere = $this->pagination->getWhere();
            $params = $this->pagination->getParams();
            if (!empty($paginationWhere)) {
                $where = !empty($where) ? $where . " AND " . $paginationWhere : $paginationWhere;
            }
        }
        $query = sprintf(
            "SELECT `page_id`, `file_name`, `link_name`, `is_link` FROM `pages` WHERE %s ORDER BY %s",
            $where,
            $orderBy
        ) . $limitQuery . ";";
        $fetchPagesQuery = $this->db->prepare($query);
        $fetchPagesQuery->execute(array_merge($this->searchParams, $params));
        $results = $fetchPagesQuery->fetchAll();
        if ($usePagination) {
            $this->pagination->processResults($results);
        }
        return $results;
    }

    /**
     * Search pages as requested
     * @return void
     */
    private function searchPages()
    {
        if (
            !empty($_GET['page_title']) ||
            !empty($_GET['file_name']) ||
            !empty($_GET['link_name'])
        ) {
            $searchString = '';
            if (!empty($_GET['page_title'])) {
                $searchString .= "`page_title` LIKE ? OR ";
                $this->searchParams[] = '%' . $_GET['page_title'] . '%';
            }
            if (!empty($_GET['file_name'])) {
                $searchString .= "`file_name` LIKE ? OR ";
                $this->searchParams[] = '%' . $_GET['file_name'] . '%';
            }
            if (!empty($_GET['link_name'])) {
                $searchString .= "`link_name` LIKE ?";
                $this->searchParams[] = '%' . $_GET['link_name'] . '%';
            }
            $this->search = "(" . rtrim($searchString, " OR ") . ")";
        }
    }

    /**
     * @param array $page
     * @return bool
     */
    public function canDeletePage(array $page)
    {
        return $this->getCurrentSubdomain()->getAuth()
                ->canDeletePages($this->auth) && !$this->isRequiredPage($page['file_name']);
    }

    /**
     * @param string $file_name
     * @return bool
     */
    public function isRequiredPage($file_name)
    {
        return in_array($file_name, $this->getRequiredPages());
    }

    /**
     * @return array
     */
    public function getRequiredPages()
    {
        return ['404', 'error', 'unsubscribe'];
    }

    /**
     * Get current subdomain to manage
     * @throws MissingAgentException If invalid agent is selected
     * @throws MissingTeamException If invalid team is selected
     * @throws UnauthorizedPageException If not allowed to manage pages
     */
    public function getCurrentSubdomain()
    {

        $filter = $this->getFilter();
        if ($filter !== self::FILTER_NAV) {
            $rule = 'canViewPages';
        } else {
            $rule = 'canManageNav';
        }

        if (empty($this->domain)) {
            $domain= $this->subdomainFactory->buildSubdomainFromRequest($rule);
            if (!$domain) {
                throw new \REW\Backend\Exceptions\UnauthorizedPageException(
                    __('You do not have permission to edit CMS snippets.')
                );
            }
            $domain->validateSettings();
            $this->domain = $domain;
        }

        return $this->domain;
    }

    /**
     * Get list of available subdomains
     * @return SubdomainInterface[]
     */
    public function getSubdomainList()
    {

        if (empty($this->domain_list)) {
            $this->domain_list = $this->subdomainFactory->getSubdomainList('canViewPages');
        }

        return $this->domain_list;
    }

    /**
     * Pagination
     * @return object
     */
    private function paginate()
    {

        // Cursor details
        $beforeCursor = $_GET['before'];
        $afterCursor = $_GET['after'];
        $primaryKey = 'page_id';
        $searchLimit = 10;
        $orderBy = 'link_name';
        $sortDir = 'ASC';

        // Next
        if (!empty($afterCursor)) {
            $cursor = Cursor\After::decode($afterCursor);

        // Prev
        } else if (!empty($beforeCursor)) {
            $cursor = Cursor\Before::decode($beforeCursor);

        // First
        } else {
            $cursor = new Cursor($primaryKey, $searchLimit, $orderBy, $sortDir);

        }

        // Create pagination instance
        return new Pagination($cursor);
    }
}
