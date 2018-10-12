<?php

/**
 * Module_FeaturedDevelopments
 */
class Module_FeaturedDevelopments
{

    /**
     * @var DB
     */
    protected $db;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var int[]
     */
    protected $exclude = [];

    /**
     * @param DB $db
     * @param Settings $settings
     */
    public function __construct(DB $db, Settings $settings)
    {
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * Get featured development
     * @return array|NULL
     */
    public function getResult()
    {
        $results = $this->getResults(1);
        return array_shift($results);
    }

    /**
     * Get featured developments
     * @param int|NULL $limit
     * @return array
     */
    public function getResults($limit = null)
    {
        $results = [];
        $query = $this->getDevelopmentQuery($limit);
        foreach ($query->fetchAll() as $featured) {
            $this->exclude[] = (int) $featured['id'];
            $featured['image'] = $this->getDevelopmentPhoto($featured['id']);
            $featured['tags'] = $this->getDevelopmentTags($featured['id']);
            $featured['url'] = $this->getDevelopmentUrl($featured['link']);
            $results[] = $featured;
        }
        return $results;
    }

    /**
     * @param int|NULL $limit
     * @return PDOStatement
     */
    protected function getDevelopmentQuery($limit = null)
    {
        $queryString = sprintf(
            "SELECT %s FROM `developments` WHERE `is_enabled` = 'Y' AND `is_featured` = 'Y'%s ORDER BY RAND() LIMIT %d;",
            implode(', ', $this->getDevelopmentFields()),
            $this->exclude ? sprintf(' AND `id` NOT IN (%s)', implode(', ', array_fill(0, count($this->exclude), '?'))) : '',
            is_numeric($limit) ? $limit : 1
        );
        $query = $this->db->prepare($queryString);
        $query->execute($this->exclude);
        return $query;
    }

    /**
     * Get development fields
     * @return string[]
     */
    protected function getDevelopmentFields()
    {
        return ['id', 'link', 'title', 'subtitle', 'city', 'unit_min_price', 'unit_max_price'];
    }

    /**
     * Get development's photo
     * @param int $id
     * @return string|NULL
     */
    protected function getDevelopmentPhoto($id)
    {
        $queryString = sprintf("SELECT `file` FROM `%s` WHERE `type` = ? AND `row` = ? ORDER BY `order` ASC LIMIT 1;", $this->settings->TABLES['UPLOADS']);
        $query = $this->db->prepare($queryString);
        $query->execute(['development', $id]);
        if ($image = $query->fetchColumn()) {
            return sprintf('/uploads/%s', $image);
        }
        return null;
    }

    /**
     * Get development's tags
     * @param int $id
     * @return string[]
     */
    protected function getDevelopmentTags($id)
    {
        $queryString = "SELECT `tag_name` FROM `developments_tags` WHERE `development_id` = ? ORDER BY `tag_order` ASC;";
        $query = $this->db->prepare($queryString);
        $query->execute([$id]);
        return $query->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Get URL to development's page
     * @param string $link
     * @return string
     */
    protected function getDevelopmentUrl($link)
    {
        return sprintf('/development/%s/', $link);
    }
}
