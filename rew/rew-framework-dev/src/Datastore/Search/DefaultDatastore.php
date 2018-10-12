<?php
namespace REW\Datastore\Search;

use REW\Factory\Idx\Search\DefaultOptionsFactory;
use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;

class DefaultDatastore
{
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
     * @var DefaultOptionsFactory
     */
    protected $defaultOptionsFactory;

    /**
     * SearchDefault constructor.
     * @param DBFactoryInterface $dbFactory
     * @param SettingsInterface $settings
     * @param DefaultOptionsFactory $defaultOptionsFactory
     */
    public function __construct(
        DBFactoryInterface $dbFactory,
        SettingsInterface $settings,
        DefaultOptionsFactory $defaultOptionsFactory
    ) {
        $this->dbFactory = $dbFactory;
        $this->settings = $settings;
        $this->defaultOptionsFactory = $defaultOptionsFactory;
    }

    /**
     * @return array
     * @throws \Exception on database error
     */
    public function getDefaultOptions()
    {
        $database = $this->dbFactory->get();
        $query = sprintf(
            "SELECT * FROM `%s`;",
            TABLE_IDX_DEFAULTS
        );
        $optionsQuery = $database->prepare($query);
        $optionsQuery->execute();
        $defaults = $optionsQuery->fetchAll();
        if (empty($defaults)) {
            $optionsQuery = $database->prepare($query);
            $optionsQuery->execute();
            $defaults = $optionsQuery->fetch();
        }
        $results = [];
        foreach ($defaults as $default) {
            $idx = $default['idx'] ? $default['idx'] : $this->settings->getInstance()->IDX_FEED;
            $results[$idx] = $this->defaultOptionsFactory->createFromArray($default);
        }
        $feeds = $this->settings->getInstance()->IDX_FEEDS;
        foreach ($feeds as $idx =>$title)
        {
            if(!array_key_exists($idx, $results))
            {
                $results[$idx] = $results[$this->settings->getInstance()->IDX_FEED];
            }
        }

        return $results;
    }

}
