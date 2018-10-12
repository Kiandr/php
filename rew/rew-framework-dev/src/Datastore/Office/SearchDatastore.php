<?php
namespace REW\Datastore\Office;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Model\Office\Search\OfficeRequest;
use REW\Factory\Office\OfficeFactory;

/**
 * Class SearchDatastore
 * @package REW\Datastore\Office
 */
class SearchDatastore
{
    /**
     * Default Fields to Fetch
     * @var array
     */
    const FIELDS = [
        'id',
        'title',
        'description',
        'email',
        'phone',
        'fax',
        'address',
        'city',
        'state',
        'zip',
        'display',
        'image',
        'sort'
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
     * @var OfficeFactory
     */
    protected $officeFactory;

    /**
     * @param DBFactoryInterface $dbFactory
     * @param SettingsInterface $settings
     * @param OfficeFactory $officeFactory
     */
    public function __construct(DBFactoryInterface $dbFactory, SettingsInterface $settings, OfficeFactory $officeFactory)
    {
        $this->dbFactory = $dbFactory;
        $this->settings = $settings;
        $this->officeFactory = $officeFactory;
    }

    /**
     * @param int $officeId
     * @return \REW\Model\Office\Search\OfficeResult|null
     * @throws \PDOException on database error
     */
    public function getOffice($officeId)
    {
        $database = $this->dbFactory->get();
        $dataStmt = $database->prepare(sprintf(
            'SELECT %s FROM `%s` `o` WHERE `o`.`id` = :id LIMIT 1;',
            $this->getFieldSql(),
            $this->settings['TABLES']['LM_OFFICES']
        ));
        $dataStmt->execute(['id' => $officeId]);
        $officeData = $dataStmt->fetch();

        if (!$officeData) {
            return null;
        }

        return $this->officeFactory->createFromArray($officeData);
    }

    /**
     * @param OfficeRequest $officeRequest
     * @return \REW\Model\Office\Search\OfficeResult[]
     * @throws \PDOException on database error
     */
    public function getOffices(OfficeRequest $officeRequest)
    {
        $database = $this->dbFactory->get();

        list($whereQuery, $whereParams) = $this->getWhereQuery($officeRequest);

        $dataStmt = $database->prepare(sprintf('SELECT %s FROM `%s` `o`%s%s%s;',
            $this->getFieldSql(),
            $this->settings['TABLES']['LM_OFFICES'],
            $whereQuery,
            $this->getOrderQuery($officeRequest),
            $this->getLimitQuery($officeRequest)
        ));

        $dataStmt->execute($whereParams);
        $officesData = $dataStmt->fetchAll();

        $officeResults = [];
        foreach ($officesData as $officeData) {
            $officeResults[] = $this->officeFactory->createFromArray($officeData);
        }
        return $officeResults;
    }

    /**
     * @param OfficeRequest $officeRequest
     * @return int
     * @throws \PDOException on database error
     */
    public function getOfficeCount(OfficeRequest $officeRequest)
    {
        $database = $this->dbFactory->get();

        list($whereQuery, $whereParams) = $this->getWhereQuery($officeRequest);

        $countStmt = $database->prepare(sprintf(
            'SELECT COUNT(`id`) FROM `%s` `o`%s;',
            $this->settings['TABLES']['LM_OFFICES'],
            $whereQuery
        ));
        $countStmt->execute($whereParams);
        $totalCount = $countStmt->fetchColumn();
        return $totalCount;
    }

    /**
     * Return a list of fields to query
     * @return array
     */
    protected function getFieldSql()
    {
        return implode(', ', array_map(function($field) {
            return sprintf('`o`.`%s`', $field);
        }, self::FIELDS));
    }

    /**
     * Get Where Query and Parameters from data and pagination
     * @param OfficeRequest $officeRequest
     * @return [array, array]
     */
    protected function getWhereQuery(OfficeRequest $officeRequest)
    {
        $whereParams = $whereParts = [];

        if (!empty($officeRequest->getId())) {
            $whereParts[] = '`o`.`id` = ?';
            $whereParams[] = $officeRequest->getId();
        }

        if (!empty($officeRequest->getLink())) {
            $whereParts[] = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`title`, '.', ''), '/', ''), ')', ''), '(', ''), '-', ' '), '  ', ' '), '\'', ''), ':', '') LIKE CONCAT(\"%\", ?, \"%\")";
            $whereParams[] = str_replace('-', ' ', $officeRequest->getLink());
        }

        if (!empty($officeRequest->getName())) {
            $whereParts[] = '`o`.`title` LIKE CONCAT("%", ?, "%")';
            $whereParams[] = $officeRequest->getName();
        }

        $display = $officeRequest->getDisplay();
        if (isset($display)) {
            if ($display) {
                $whereParts[] = '`o`.`display` = \'Y\'';
            } else {
                $whereParts[] = '`o`.`display` = \'N\'';
            }
        }

        $where = implode(' AND ', $whereParts);
        $whereQuery = (!empty($where)) ? sprintf(' WHERE %s', $where) : '';
        return [$whereQuery, $whereParams];
    }

    /**
     * Get Order Query  from pagination
     * @param OfficeRequest $officeRequest
     * @return string
     */
    protected function getOrderQuery(OfficeRequest $officeRequest)
    {
        if ($order = $officeRequest->getOrder()) {
            $sqlOrdering = [];
            foreach ($order as $sortOrder) {
                list($sort, $dir) = $sortOrder;
                $sqlOrdering[] = sprintf('`o`.`%s` %s', $sort, $dir);
            }
            if (!empty($sqlOrdering)) {
                return sprintf(' ORDER BY %s', implode(', ', $sqlOrdering));
            }
        }
        return '';
    }

    /**
     * Get Limit Query from pagination
     * @param OfficeRequest $officeRequest
     * @return string
     */
    protected function getLimitQuery(OfficeRequest $officeRequest)
    {
        // Get Limit and Page
        $limit = (int) $officeRequest->getLimit();
        $page = (int) $officeRequest->getPage();

        $limitSql = '';
        if (!empty($limit)) {
            $limitSql .= sprintf(' LIMIT %d', $limit);
        }
        if (!empty($limit) && ($page -1 > 0)) {
            $offset = $limit * ($page - 1);
            $limitSql .= sprintf(' OFFSET %d', $offset);
        }
        return $limitSql;
    }
}
