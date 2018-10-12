<?php
namespace REW\Datastore\Agent;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Model\Agent\Search\AgentRequest;
use REW\Model\Agent\Search\AgentResults;
use REW\Model\Agent\Search\AgentResult;
use REW\Factory\Agent\AgentFactory;

/**
 * Class SearchDatastore
 * @package REW\Datastore\Agent
 */
class SearchDatastore
{
    /**
     * Default Fields to Fetch
     * @var array
     */
    const FIELDS = [
        'id',
        'first_name',
        'last_name',
        'email',
        'title',
        'office',
        'office_phone',
        'home_phone',
        'cell_phone',
        'fax',
        'remarks',
        'display',
        'display_feature',
        'image',
        'agent_id'
    ];

    /**
     * @var DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var AgentFactory
     */
    protected $agentFactory;

    /**
     * @param DBFactoryInterface $dbFactory
     * @param SettingsInterface $settings
     * @param AgentFactory $agentFactory
     */
    public function __construct(DBFactoryInterface $dbFactory, SettingsInterface $settings, AgentFactory $agentFactory)
    {
        $this->dbFactory = $dbFactory;
        $this->settings = $settings;
        $this->agentFactory = $agentFactory;
    }

    /**
     * @param AgentRequest $agentRequest
     * @return \REW\Model\Agent\Search\AgentResults
     * @throws \PDOException on database error
     */
    public function getAgents(AgentRequest $agentRequest)
    {
        $database = $this->dbFactory->get();

        list($whereQuery, $whereParams) = $this->getWhereQuery($agentRequest);

        $dataStmt = $database->prepare(sprintf('SELECT %s FROM `%s` `a`%s%s%s;',
            $this->getFieldSql(),
            $this->settings['TABLES']['LM_AGENTS'],
            $whereQuery,
            $this->getOrderQuery($agentRequest),
            $this->getLimitQuery($agentRequest)
        ));
        $dataStmt->execute($whereParams);
        $agentsData = $dataStmt->fetchAll();

        $agentResults = [];
        foreach ($agentsData as $agentData) {
            $agentResults[] = $this->agentFactory->createFromArray($agentData);
        }
        return $agentResults;
    }

    /**
     * @param AgentRequest $agentRequest
     * @return int
     * @throws \PDOException on database error
     */
    public function getAgentCount(AgentRequest $agentRequest)
    {
        $database = $this->dbFactory->get();

        list($whereQuery, $whereParams) = $this->getWhereQuery($agentRequest);

        $countStmt = $database->prepare(sprintf(
            'SELECT COUNT(`a`.`id`) FROM `%s` `a`%s;',
            $this->settings['TABLES']['LM_AGENTS'],
            $whereQuery
        ));
        $countStmt->execute($whereParams);
        $totalCount = $countStmt->fetchColumn();
        return $totalCount;
    }

    /**
     * @return array
     * @throws \PDOException on database error
     */
    public function getAgentLetters()
    {
        $letters = [];
        $database = $this->dbFactory->get();

        // Letter filters
        $letterStmt = $database->prepare(
            'SELECT SUBSTR(`last_name`, 1, 1) AS `letter`
            FROM `agents` WHERE `display` = \'Y\'
            GROUP BY `letter` ORDER BY `last_name` ASC;'
        );
        $letterStmt->execute();
        while ($letter = $letterStmt->fetchColumn()) {
            $letters[] = strtoupper($letter);
        }
        return $letters;
    }

    /**
     * Get CMS Addons for agent subdomain
     * @param $agentId
     * @return array
     * @throws \PDOException
     */
    public function getAddons($agentId)
    {
        $addons = [];
        $database = $this->dbFactory->get();

        // Addons query
        $query = 'SELECT `cms_addons` FROM `agents` WHERE `id` = :agent;';
        $params = ['agent' => $agentId];

        $addonStmt = $database->prepare($query);
        $addonStmt->execute($params);
        $addonResult = $addonStmt->fetch();
        $addons = explode(',', $addonResult['cms_addons']);
        return $addons;
    }

    /**
     * Return a list of fields to query
     * @return array
     */
    protected function getFieldSql()
    {
        return implode(', ', array_map(function($field) {
            return sprintf('`a`.`%s`', $field);
        }, self::FIELDS));
    }

    /**
     * Get Where Query and Parameters from data
     * @param AgentRequest $agentRequest
     * @return [array, array]
     */
    protected function getWhereQuery(AgentRequest $agentRequest)
    {
        $whereParams = $whereParts = [];

        // Query By Agent Id
        if (!empty($agentRequest->getId())) {
            $whereParts[] = '`a`.`id` = ?';
            $whereParams[] = $agentRequest->getId();
        }

        // Query By Agent Slug
        if (!empty($agentRequest->getLink())) {
            $whereParts[] = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(CONCAT(`first_name`, ' ', `last_name`), '.', ''), '/', ''), ')', ''), '(', ''), '-', ' '), '  ', ' '), '\'', ''), ':', '') LIKE CONCAT(\"%\", ?, \"%\")";
            $whereParams[] = str_replace('-', ' ', $agentRequest->getLink());
        }

        // Query By Similar Agent Names
        if (!empty($agentRequest->getName())) {
            $whereParts[] = 'CONCAT(`a`.`first_name`,\' \', `a`.`last_name`) LIKE CONCAT("%", ?, "%")';
            $whereParams[] = $agentRequest->getName();
        }

        // Query By Agent Last Name
        if (!empty($agentRequest->getLetter())) {
            $whereParts[] = '`a`.`last_name` LIKE CONCAT(?, "%")';
            $whereParams[] = $agentRequest->getLetter();
        }

        // Query By Agent Office Id
        if (!empty($agentRequest->getOfficeId())) {
            $whereParts[] = '`a`.`office` = ?';
            $whereParams[] = $agentRequest->getOfficeId();
        }

        // Query By Agent Display
        $display = $agentRequest->getDisplay();
        if (isset($display)) {
            if ($display) {
                $whereParts[] = '`a`.`display` = \'Y\'';
            } else {
                $whereParts[] = '`a`.`display` = \'N\'';
            }
        }

        $where = implode(' AND ', $whereParts);
        $whereQuery = (!empty($where)) ? sprintf(' WHERE %s', $where) : '';
        return [$whereQuery, $whereParams];
    }

    /**
     * Get Order Query from Request
     * @param AgentRequest $agentRequest
     * @return string
     */
    protected function getOrderQuery(AgentRequest $agentRequest)
    {
        if ($order = $agentRequest->getOrder()) {
            $sqlOrdering = [];
            foreach ($order as $sortOrder) {
                list($sort, $dir) = $sortOrder;
                $sqlOrdering[] = sprintf('`a`.`%s` %s', $sort, $dir);
            }
            if (!empty($sqlOrdering)) {
                return sprintf(' ORDER BY %s', implode(', ', $sqlOrdering));
            }
        }
        return '';
    }

    /**
     * Get Limit Query from request
     * @param AgentRequest $agentRequest
     * @return string
     */
    protected function getLimitQuery(AgentRequest $agentRequest)
    {
        // Get Limit and Page
        $limit = (int) $agentRequest->getLimit();
        $page = (int) $agentRequest->getPage();

        $limitSql = '';
        if (!empty($limit)) {
            $limitSql .= sprintf(' LIMIT %d', $limit);
        }
        if (!empty($limit) && (($page-1) > 0)) {
            $offset = $limit * ($page-1);
            $limitSql .= sprintf(' OFFSET %d', $offset);
        }
        return $limitSql;
    }
}
