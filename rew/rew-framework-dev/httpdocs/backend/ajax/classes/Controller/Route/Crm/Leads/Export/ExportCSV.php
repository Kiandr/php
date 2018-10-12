<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Export;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Api\Internal\Leads\Search;
use REW\Api\Internal\Store\Agents;
use REW\Api\Internal\Store\Lenders;
use REW\Api\Internal\Store\Groups;
use REW\Api\Internal\Store\ActionPlans;
use REW\Backend\Auth\LeadsAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Pagination\Pagination;
use REW\Pagination\Cursor;
use \Slim\Http\Response;
use \Slim\Http\Request;
use \History_Event;

/**
 * Leads Export CSV Controller
 * @package REW\Api\Internal\Controller\ExportCSV
 */
class ExportCSV implements ControllerInterface
{
    /**
     * @var string
     */
    const ALL_LEADS_VIEW = 'all-leads';

    /**
     * @var string
     */
    const PRIMARY_KEY = 'id';

    /**
     * @var string
     */
    const DEFAULT_ORDER = 'timestamp_active';

    /**
     * @var string
     */
    const DEFAULT_SORT = 'DESC';


    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var DB
     */
    protected $db;

    /**
     * @var array
     */
    protected $get;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var string
     */
    protected $order;

    /**
     * @var string
     */
    protected $sort;


    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param ContainerInterface $container
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        ContainerInterface $container,
        SettingsInterface $settings
    )
    {
        $this->auth = $auth;
        $this->container = $container;
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $response->headers->set('Content-Disposition', 'attachment; filename=export.csv');
        $response->headers->set('Content-Type', 'text/csv');

        $cursor = null;

        $this->get = $request->get();
        $this->order = !empty($this->get['order']) ? htmlspecialchars($this->get['order']) : self::DEFAULT_ORDER;
        $this->sort = !empty($this->get['sort']) ? htmlspecialchars($this->get['sort']) : self::DEFAULT_SORT;
        $this->order = explode(',', $this->order);
        $this->sort = array_fill(0, count($this->order), $this->sort);

        // Check Request VS Permissions
        $this->checkPermissions();
        $body = $this->getResponse(new Pagination(new Cursor()));
        $response->setBody($this->generateCsv($body));
    }

    protected function multi_implode($array, $glue) {
        $ret = '';

        foreach ($array as $item) {
            if (is_array($item)) {
                $ret .= multi_implode($item, $glue) . $glue;
            } else {
                $ret .= $item . $glue;
            }
        }

        $ret = substr($ret, 0, 0-strlen($glue));

        return $ret;
    }

    /**
     * Generates a csv file for export
     * @return array
     */
    protected function generateCsv($data, $delimiter = ',', $enclosure = '"')
    {
        $contents = '';
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, array_keys($data[0]));

        foreach ($data as $key => $value) {
            foreach ($data[$key] as $columnKey => $columnValue) {
                if (is_array($data[$key][$columnKey])) {
                    $data[$key][$columnKey] = serialize($columnValue);
                }
            }
            fputcsv($handle, $data[$key], $delimiter, $enclosure);
        }
        rewind($handle);

        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }

        fclose($handle);
        return $contents;
    }

    /**
     * Check permissions against this request
     * @throws InsufficientPermissionsException
     */
    protected function checkPermissions()
    {
        $leadsAuth = new LeadsAuth($this->settings);
        if (!$leadsAuth->canManageLeads($this->auth)) {
            if ($this->get['view'] == self::ALL_LEADS_VIEW || !$leadsAuth->canViewOwn($this->auth)) {
                throw new InsufficientPermissionsException('You do not have the proper CRM permissions to perform this request.');
            }
        }
    }

    /**
     * @throws PDOException
     * @return array
     */
    protected function getResponse(Pagination $pagination)
    {

        $leadSearch = $this->container->get(Search::class);

        // Build the SQL Query based on current filters
        $leadSearch->updateLeadsQuery($this->limit, $pagination);

        // Fetch Results
        $leadResults = [];

        // Fetch count before fetching results
        $leads = $leadSearch->fetchLeads($pagination);
        if (!empty($leads)) {

            // Handle pagination of lead results
            $pagination->processResults($leads);

            // Fetch Agents
            $agentsStore = new Agents($this->db, $this->settings);
            $agents = $agentsStore->getAgents(array_filter(array_column($leads, 'agent')));

            // Fetch Lenders
            $lendersStore = new Lenders($this->db, $this->settings);
            $lenders = $lendersStore->getLenders(array_filter(array_column($leads, 'lender')));

            // Fetch Last Action
            $last_actions = $this->getLastAction(array_column($leads, 'last_action'));

            // Fetch Groups
            $groupsStore = new Groups($this->db, $this->settings);
            $groups = $groupsStore->getAssignedGroups(array_column($leads, 'id'));

            // Fetch Action Plans
            $actionPlansStore = new ActionPlans($this->db, $this->settings);
            $action_plans = $actionPlansStore->getAssignedActionPlans(array_column($leads, 'id'));

            foreach ($leads as $lead) {
                if (!empty($lead['id'])) {

                    $lead['last_action'] = !empty($last_actions[$lead['last_action']]) ? $last_actions[$lead['last_action']] : null;

                    if (!empty($lead['timestamp_created']) != '0000-00-00 00:00:00') {
                        $timestamp_created = $lead['timestamp_created'];
                        $timestamp_created = date("Y-m-d\TH:i:s.000\Z", strtotime($timestamp_created));
                        $lead['timestamp_created'] = $timestamp_created;
                    } elseif (!empty($lead['timestamp_created']) && $lead['timestamp_created'] == '0000-00-00 00:00:00') {
                        $lead['timestamp_created'] = null;
                    }

                    if (!empty($lead['timestamp_active']) != '0000-00-00 00:00:00') {
                        $timestamp_created = $lead['timestamp_active'];
                        $timestamp_created = date("Y-m-d\TH:i:s.000\Z", strtotime($timestamp_created));
                        $lead['timestamp_active'] = $timestamp_created;
                    } elseif (!empty($lead['timestamp_active']) && $lead['timestamp_active'] == '0000-00-00 00:00:00') {
                        $lead['timestamp_active'] = null;
                    }

                    if (!empty($lead['last_touched'])) {
                        $last_touched = json_decode($lead['last_touched'], TRUE);
                        if ($last_touched['timestamp'] != '0000-00-00 00:00:00') {
                            $last_touched['timestamp'] = date("Y-m-d H:i:s", $last_touched['timestamp']);
                        } else {
                            $last_touched['timestamp'] = null;
                        }
                        $last_touched['method'] = $lead['last_touched_method'];
                        $lead['last_touched'] = $last_touched;
                    } else {
                        $lead['last_touched'] = null;
                    }
                    unset($lead['last_touched_method']);

                    // Grab Agent
                    if (!empty($lead['agent'])) {
                        $lead['agent'] = $agents[$lead['agent']];
                    }

                    //Grab Lender Data
                    if (!empty($lead['lender'])) {
                        $lead['lender'] = $lenders[$lead['lender']];
                    }

                    $lead['groups'] = !empty($groups[$lead['id']]) ? $groups[$lead['id']] : null;

                    $lead['action_plans'] = !empty($action_plans[$lead['id']]) ? $action_plans[$lead['id']] : null;
                }
                $leadResults[] = $lead;
            }
        }

        $response = $leadResults;

        return $response;
    }

    /**
     * Grab Last Action Data
     * @param Array $ids
     * @return array
     */
    private function getLastAction($ids = [])
    {

        $result = [];

        // Action Labels
        $action_labels = [
            'Login' => 'Login',
            'Logout' => 'Logout',
            'Unsubscribe' => 'Unsubscribed',
            'FormSubmission' => 'Inquired',
            'ViewedListing' => 'Viewed Listing',
            'DismissListing' => 'Dismiss Listing',
            'SavedListing' => 'Saved Listing',
            'SavedSearch' => 'Saved Search',
            'Connected' => 'Connected',
        ];

        if (!empty($ids)) {
            $actions = History_Event::load(implode(',', array_filter(array_values($ids))));
            $actions = is_array($actions) ? $actions : [$actions];
            foreach ($actions as $action) {
                if (!empty($action)) {
                    $result[$action->getID()] = [
                        'title' => $action_labels[$action->getSubType()],
                        'timestamp' => date("Y-m-d\TH:i:s.000\Z", $action->getTimestamp()),
                        'url' => (method_exists($action, 'getListingData') ? $action->getListingData()['url_details'] : null)
                    ];
                }
            }
        }

        return $result;
    }

}

