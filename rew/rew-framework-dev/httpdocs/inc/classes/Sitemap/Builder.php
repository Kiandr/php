<?php

/**
 * Sitemap_Builder
 *
 */
class Sitemap_Builder
{

    /**
     * Sitemap XML
     * @var string
     */
    private $xml = '';

    /**
     * Number of URLS
     * @var int
     */
    private $urls = 0;

    /**
     * Create Sitemap_Builder
     *
     * @return void
     */
    public function __construct()
    {
        $this->xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $this->xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    }

    /**
     * Add URL to Sitemap
     *
     * @param string $url
     * @param float $priority
     * @param string $changefreq
     * @return void
     */
    public function add($url, $priority = 0.5, $changefreq = '')
    {
        $this->xml .= '<url>';
        $this->xml .= '<loc>'.htmlentities($url).'</loc>';
        if ($priority != 0.5) {
            $this->xml .= '<priority>'.$priority.'</priority>';
        }
        if (!empty($changefreq)) {
            $this->xml .= '<changefreq>'.$changefreq.'</changefreq>';
        }
        $this->xml .= '</url>'."\n";
        $this->urls++;
    }

    /**
     * Save to File
     *
     * @param string $filename
     * @return void
     */
    public function save($filename)
    {
        $this->xml .= '</urlset>'."\n";
        file_put_contents($filename, $this->xml);
    }

    /**
     * Get File Size
     *
     * @return int
     */
    public function getSizeBytes()
    {
        return strlen($this->xml);
    }

    /**
     * Get # of URLs
     *
     * @return int
     */
    public function getSizeURLs()
    {
        return $this->urls;
    }
}
