<?php
namespace REW\Datastore\Listing;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Factory\Idx\SavedSearch\Result\ResultInterface as SavedSearchResultFactory;
use REW\Model\Idx\SavedSearch\Request\RequestInterface as SavedSearchRequestModel;
use REW\Model\Idx\SavedSearch\Result\ResultInterface as SavedSearchResultModel;

class SavedSearchDatastore implements SavedSearchDatastoreInterface
{
    const SELECT_SEARCH_COLUMNS = [
        's.`id`', 's.`user_id`', 's.`agent_id`', 's.`title`', 's.`criteria`', 's.`table`', 's.`idx`', 's.`suggested`', 's.`frequency`'
    ];

    /**
     * @var DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var IDXInterface
     */
    protected $idx;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var SavedSearchResultFactory
     */
    protected $savedSearchResultFactory;

    /**
     * SavedSearch constructor.
     * @param DBFactoryInterface $dbFactory
     * @param IDXInterface $idx
     * @param SavedSearchResultFactory $savedSearchResultFactory
     */
    public function __construct(
        DBFactoryInterface $dbFactory,
        IDXInterface $idx,
        SettingsInterface $settings,
        SavedSearchResultFactory $savedSearchResultFactory
    ) {
        $this->dbFactory = $dbFactory;
        $this->idx = $idx;
        $this->settings = $settings;
        $this->savedSearchResultFactory = $savedSearchResultFactory;
    }

    /**
     * @param SavedSearchRequestModel $searchRequest
     * @return SavedSearchResultModel
     * @throws \PDOException
     * @throws \Exception
     */
    public function saveSearch(SavedSearchRequestModel $searchRequest)
    {
        $db = $this->dbFactory->get();
        $sql = sprintf('INSERT INTO `%s` SET
            `user_id` = :userId,
            `agent_id` = :agentId,
            `title` = :title,
            `criteria` = :criteria,
            `table` = :table,
            `idx` = :idx,
            `suggested` = :suggested,
            `frequency` = :frequency,
            `timestamp_created` = NOW()',
            $this->settings['TABLES']['LM_USER_SEARCHES']
        );

        $stmt = $db->prepare($sql);

            $stmt->execute([
                'userId' => $searchRequest->getUserId(),
                'agentId' => $searchRequest->getAgentId(),
                'title' => $searchRequest->getTitle(),
                'criteria' => $searchRequest->getSerializedSearchCriteria(),
                'table' => $this->idx->getTable(),
                'idx' => $searchRequest->getFeed(),
                'suggested' => 'false',
                'frequency' => $searchRequest->getFrequency()
            ]);

        $lastInsertId = $db->lastInsertId();
        return $this->getSearchById($lastInsertId);
    }


    /**
     * @param SavedSearchRequestModel $searchRequest
     * @return SavedSearchResultModel
     * @throws \PDOException
     * @throws \Exception
     */
    public function updateSearch(SavedSearchRequestModel $searchRequest)
    {
        $db = $this->dbFactory->get();
        $sql = sprintf('UPDATE `%s` SET
            `title` = :title,
            `frequency` = :frequency,
            `criteria` = :criteria
            WHERE `id` = :id AND `user_id` = :user_id;',
            $this->settings['TABLES']['LM_USER_SEARCHES']
        );

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'title' => $searchRequest->getTitle(),
            'frequency' => $searchRequest->getFrequency(),
            'criteria' => $searchRequest->getSerializedSearchCriteria(),
            'id' => $searchRequest->getId(),
            'user_id' => $searchRequest->getUserId()
        ]);
        return $this->getSearchById($searchRequest->getId());
    }

    /**
     * @param SavedSearchRequestModel $searchRequest
     * @return SavedSearchResultModel[]
     */
    public function getSearches(SavedSearchRequestModel $searchRequest)
    {
        $db = $this->dbFactory->get();
        $sql = sprintf(
            'SELECT %s FROM `%s` s',
            self::SELECT_SEARCH_COLUMNS,
            $this->settings['TABLES']['LM_USER_SEARCHES']
        );

        $params = [];
        $whereSql = $this->buildWhereSql($searchRequest, $params);

        if ($whereSql !== '') {
            $sql = sprintf('%s WHERE %s', $sql, $whereSql);
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $sqlResults = $stmt->fetchAll();
        $results = [];

        foreach ($sqlResults as $result) {
            $results[] = $this->savedSearchResultFactory->createFromArray($result);
        }

        return $results;
    }

    /**
     * @param SavedSearchRequestModel $searchRequest
     * @param array $params
     * @return string
     */
    protected function buildWhereSql(SavedSearchRequestModel $searchRequest, &$params)
    {
        $whereParts = [];

        if (null !== $searchRequest->getUserId()) {
            $whereParts[] = 's.`user_id` = ?';
            $params[] = $searchRequest->getUserId();
        }

        if (null !== $searchRequest->getTitle()) {
            $whereParts[] = 's.`title` = ?';
            $params[] = $searchRequest->getTitle();
        }

        if (null !== $searchRequest->getFrequency()) {
            $whereParts[] = 's.`frequency` = ?';
            $params[] = $searchRequest->getFrequency();
        }

        if (null !== $searchRequest->getFeed()) {
            $whereParts[] = 's.`idx` = ?';
            $params[] = $searchRequest->getFeed();
        }

        if (null !== $searchRequest->getCriteria()) {
            $whereParts[] = 's.`criteria` = ?';
            $params[] = $searchRequest->getCriteria();
        }

        return implode(' AND ', $whereParts);
    }

    /**
     * @param int $id
     * @return SavedSearchResultModel
     * @throws \PDOException
     * @throws \Exception
     */
    public function getSearchById($id)
    {
        $db = $this->dbFactory->get();

        $sql = sprintf(
            'SELECT %s FROM `%s` s WHERE s.`id` = :searchId LIMIT 1',
            implode(',',self::SELECT_SEARCH_COLUMNS),
            $this->settings['TABLES']['LM_USER_SEARCHES']
        );

        $stmt = $db->prepare($sql);
        $stmt->execute(['searchId' => $id]);

        $result = $stmt->fetch();
        return $this->savedSearchResultFactory->createFromArray($result);
    }
}
