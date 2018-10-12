<?php

namespace REW\Backend\Page;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;

/**
 * Class TimelineManager
 * @package REW\Backend
 * @subpackage Page
 * @author REW Chelsea Urquhart <urquhart.chelsea@realestatewebmasters.com>
 */
class TimelineFactory
{
    /**
     * @var DBInterface
     */
    private $db;

    /**
     * TimelineManager constructor.
     * @param DBInterface $db
     * @param SettingsInterface $settings
     */
    public function __construct(DBInterface $db, SettingsInterface $settings)
    {
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * Load the Timeline identified by guid
     * @param string $guid
     * @return Timeline|null
     */
    public function load($guid)
    {
        $tablePages = $this->settings['TABLES']['TIMELINE_PAGES'];

        $sql = "SELECT :guid AS `guid`, `url`,"
            . ' ' . sprintf(Timeline::CONVERT_BINARY_TO_GUID, "`last_page_guid`") . " AS `lastPageGuid`"
            . " FROM `" . $tablePages . "` WHERE"
            . " `guid` = " . sprintf(Timeline::CONVERT_GUID_TO_BINARY, ':guid');

        $stmt = $this->db->prepare($sql);

        $stmt->execute(['guid' => $guid]);

        return $stmt->fetchObject(Timeline::class, [$this->db, $this->settings, $this]) ?: null;
    }

    /**
     * Builds a TimelineFactory instance
     * @param string $url
     * @param array $get
     * @return Timeline
     */
    public function build($url, array $get)
    {
        return new Timeline($this->db, $this->settings, $this, $url, $get);
    }
}
