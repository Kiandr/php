<?php

namespace REW\Backend\Page;

use REW\Backend\Interfaces\Page\TimelineInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;

/**
 * Class Page
 *
 * @category Timeline
 * @package  REW\Backend\History
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class Timeline implements TimelineInterface
{
    /**
     * @const string The mysql function to use to convert to binary
     */
    const CONVERT_GUID_TO_BINARY = 'GuidToBinary(%s)';

    /**
     * @const string The mysql function to use to convert to guid
     */
    const CONVERT_BINARY_TO_GUID = 'ToGuid(%s)';

    /**
     * Timeline Page URL
     * @var string $url
     */
    private $url;

    /**
     * Timeline Page GET
     * @var array|null $get
     */
    private $get;

    /**
     * Last Page Visited
     * @var TimelineInterface $last
     */
    private $lastPage;

    /**
     * The id of the last page
     * @var string
     */
    private $lastPageGuid;

    /**
     * The guid of this record
     * @var string
     */
    private $guid;

    /**
     * @var DBInterface
     */
    private $db;

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @var TimelineFactory
     */
    private $factory;

    /**
     * Create Timeline Page
     * @param DBInterface $db
     * @param SettingsInterface $settings
     * @param TimelineFactory $factory
     * @param string $url
     * @param array|null $get
     */
    public function __construct(
        DBInterface $db,
        SettingsInterface $settings,
        TimelineFactory $factory,
        $url = '',
        array $get = null
    ) {
        $this->db = $db;
        $this->settings = $settings;
        $this->factory = $factory;
        if (!isset($this->url)) {
            // Set Timeline Page URL if it isn't set already (PDO will set it)
            $this->url = $url;
        }
        $this->lastPage = null;

        //Convert Post Data to Get Data
        if (isset($get)) {
            $this->get = [];
            foreach (self::SAVED_VARS as $saved_var) {
                if (isset($get[$saved_var])) {
                    $this->get[$saved_var] = $get[$saved_var];
                }
            }
        }
    }

    /**
     * Get JSON string representing page
     * @param string $mode
     * @return string
     */
    public function getLink($mode = 'back')
    {
        $params = array_merge($this->getGet(), [self::MODE => $mode]);
        $httpQuery = http_build_query($params);
        return $this->url . (!empty($httpQuery) ? ('?' . $httpQuery) : '');
    }

    /**
     * Get JSON string representing page
     * @return string
     */
    public function encode()
    {
        $lastPage = $this->getLast();

        return json_encode([
            'url'  => $this->url,
            'get'  => $this->getGet(),
            'lastPage' => isset($lastPage) ? $lastPage->getGUID() : null
        ]);
    }

    /**
     * Get last page visited. Loads it if it hasn't been loaded yet
     * @return TimelineInterface
     */
    public function getLast()
    {
        if ($this->lastPage === null && $this->lastPageGuid) {
            $this->lastPage = $this->factory->load($this->lastPageGuid);
        }

        return $this->lastPage;
    }

    /**
     * Set last page visited
     * @param TimelineInterface $lastPage
     * @return void
     */
    public function setLast(TimelineInterface $lastPage)
    {
        $this->lastPage = $lastPage;
    }

    /**
     * Clear Timeline History
     */
    public function clearPast()
    {
        $lastPage = $this->getLast();

        if (isset($lastPage)) {
            $root = false;
            if (!$this->db->inTransaction()) {
                // Wrap all this recursion in a transaction so that we can't loose elements
                $root = true;
                $this->db->beginTransaction();
            }

            $this->lastPage->clearPast();
            $tablePages = $this->settings['TABLES']['TIMELINE_PAGES'];
            $sql = "DELETE FROM `" . $tablePages . "`"
                . " WHERE `guid` = " . sprintf(static::CONVERT_GUID_TO_BINARY, ':guid');
            $stmt = $this->db->prepare($sql);
            try {
                $stmt->execute(['guid' => $this->lastPage->getGUID()]);
            } catch (\PDOException $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }

                throw $e;
            }

            if ($root) {
                $this->db->commit();
            }

            $this->lastPage = $this->lastPageGuid = null;
        }
    }

    /**
     * Compare with timeline page
     * @param TimelineInterface $page
     * @return bool
     */
    public function compare(TimelineInterface $page)
    {

        // Compare URL
        if (rtrim($this->getUrl(), '/') != rtrim($page->getUrl(), '/')) {
            return false;
        }

        //Compare GET
        return md5(json_encode($this->getGet())) == md5(json_encode($page->getGet()));
    }

    /**
     * Get the GUID for this page.
     * @return string|null
     */
    public function getGUID()
    {
        return $this->guid;
    }

    /**
     * Set the GUID for this page. Do NOT use this if you plan to save a new page because this will mark a page as
     * already existing and saving will fail (unless of course the guid you pass is valid.)
     * @param string
     */
    public function setGUID($guid)
    {
        $this->guid = $guid;
    }

    /**
     * Get the url for this page.
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the GET vars for this page.
     * @return array
     */
    public function getGet()
    {
        if ($this->get === null && $this->guid) {
            // not loaded yet but it is for an existing page
            $tablePageVariables = $this->settings['TABLES']['TIMELINE_PAGE_VARIABLES'];

            $sql = "SELECT `value` FROM `" . $tablePageVariables . "`"
                . " WHERE `page_guid` = " . sprintf(static::CONVERT_GUID_TO_BINARY, ':guid') . " AND"
                . " `key` = :key";
            $params = [];
            $params['key'] = 'get';
            $params['guid'] = $this->guid;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            $encodedGet = $stmt->fetchColumn(0);
            if ($encodedGet) {
                $this->get = json_decode($encodedGet, true);
            }
        }

        return $this->get ?: [];
    }

    /**
     * Saves this page
     */
    public function save()
    {
        $create = false;
        if (!$this->guid) {
            $this->guid = $this->generateRandomGUID();
            $create = true;
        }

        $tablePages = $this->settings['TABLES']['TIMELINE_PAGES'];
        $tablePageVariables = $this->settings['TABLES']['TIMELINE_PAGE_VARIABLES'];

        $lastPage = $this->getLast();
        $params = [];
        $params['url'] = $this->url;
        $params['guid'] = $this->guid;
        $params['last_page_guid'] = isset($lastPage) ? $lastPage->getGUID() : null;

        if ($create) {
            $sql = "INSERT INTO ";
        } else {
            $sql = "UPDATE ";
        }

        $sql .= "`" . $tablePages . "` SET ";
        foreach (array_keys($params) as $param) {
            if ($create || $param != 'guid') {
                $sql .= "`" . $param . "` = ";

                if (in_array($param, ['guid', 'last_page_guid'])) {
                    $sql .= sprintf(static::CONVERT_GUID_TO_BINARY, ':'. $param);
                } else {
                    $sql .= ":" . $param;
                }
                $sql .= ', ';
            }
        }

        $sql .= " `timestamp_updated` = NOW()";

        if (!$create) {
            $sql .= " WHERE `guid` = " . sprintf(static::CONVERT_GUID_TO_BINARY, ':guid');
        }

        $stmt = $this->db->prepare($sql);

        $this->db->beginTransaction();

        // Insert/update page record
        try {
            $stmt->execute($params);
        } catch (\PDOException $e) {
            $this->db->rollBack();

            throw $e;
        }

        // Insert/update variables
        $params = ['guid' => $params['guid']];

        if (!$this->get) {
            $sql = "DELETE FROM `" . $tablePageVariables . "` WHERE"
                . " `page_guid` = " . sprintf(static::CONVERT_GUID_TO_BINARY, ':guid')
                . " AND `key` = 'get'";
        } else {
            $sql = "INSERT INTO `" . $tablePageVariables . "`"
                . " SET `page_guid` = " . sprintf(static::CONVERT_GUID_TO_BINARY, ':guid') . ","
                . " `key` = :key, `value` = :value"
                . " ON DUPLICATE KEY UPDATE `timestamp_updated` = NOW(), `value` = VALUES(`value`)";
            $params['key'] = 'get';
            $params['value'] = json_encode($this->get);
        }

        $stmt = $this->db->prepare($sql);
        try {
            $stmt->execute($params);
        } catch (\PDOException $e) {
            $this->db->rollBack();

            throw $e;
        }

        $this->db->commit();
    }

    /**
     * Build a guid and return it
     * @return string
     */
    public function generateRandomGUID()
    {
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);
        return substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
    }
}
