<?php

/**
 * Sitemap_Indexed
 *
 */
class Sitemap_Indexed
{

    /**
     * Sitemap Builder
     * @var Sitemap_Builder
     */
    private $sitemap = null;

    /**
     *
     * @var array
     */
    private $sitemaps = array();

    /**
     *
     * @var unknown_type
     */
    private $path = '';

    /**
     *
     * @var int
     */
    private $max_bytes = 9437184; // 9 MB

    /**
     *
     * @var int
     */
    private $max_urls = 45000;

    /**
     * Create Sitemap_Indexed
     *
     * @param string $path
     * @return void
     */
    public function __construct($path = '')
    {
        if (empty($path)) {
            $path = $_SERVER['DOCUMENT_ROOT'] . '/inc/cache/xml';
        }
        $this->path = $path;
        $this->sitemap = new Sitemap_Builder;
    }

    /**
     * Add URL to Sitemap
     *
     * @param string $url
     * @param int $priority
     * @param string $changefreq
     * @return void
     */
    public function add($url, $priority = 0.5, $changefreq = '')
    {
        if ($this->sitemap->getSizeBytes() > $this->max_bytes || $this->sitemap->getSizeURLs() >= $this->max_urls) {
            $this->rotate();
        }
        $this->sitemap->add($url, $priority, $changefreq);
    }

    /**
     * Add New Sitemap
     *
     * @param bool $final
     * @return
     */
    private function rotate($final = false)
    {
        $name = 'sitemap_' . count($this->sitemaps) . '.xml';
        $this->sitemaps[] = Settings::getInstance()->SETTINGS['URL'] . $name;
        $this->sitemap->save($this->path . '/' . $name);
        if (!$final) {
            $this->sitemap = new Sitemap_Builder;
        }
    }

    /**
     * Save XML to File
     *
     * @return void
     */
    public function save()
    {
        $this->rotate(true);
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        foreach ($this->sitemaps as $sitmap) {
            $xml .= '<sitemap><loc>' . htmlentities($sitmap). '</loc></sitemap>' . PHP_EOL;
        }
        $xml .= '</sitemapindex>' . PHP_EOL;
        file_put_contents($this->path.'/sitemap.xml', $xml);
    }
}
