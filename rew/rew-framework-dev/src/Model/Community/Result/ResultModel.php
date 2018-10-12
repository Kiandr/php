<?php
namespace REW\Model\Community\Result;

use REW\Model\Community\ResultModelInterface;

class ResultModel implements ResultModelInterface
{
    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $subTitle = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var string
     */
    protected $image = '';

    /**
     * @var array
     */
    protected $images = [];

    /**
     * @var string
     */
    protected $statsHeading;

    /**
     * @var string
     */
    protected $statsLowest;

    /**
     * @var string
     */
    protected $statsHighest;

    /**
     * @var string
     */
    protected $statsAverage;

    /**
     * @var string
     */
    protected $statsTotal;

    /**
     * @var array
     */
    protected $listings;

    /**
     * @var array
     */
    protected $idxStats;

    /**
     * @var array
     */
    protected $typeStats;

    /**
     * @var string
     */
    protected $searchUrl;

    /**
     * @param string $title
     * @return self
     */
    public function withTitle($title)
    {
        $clone = clone $this;
        $clone->title = $title;
        return $clone;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $subTitle
     * @return self
     */
    public function withSubTitle($subTitle)
    {
        $clone = clone $this;
        $clone->subTitle = $subTitle;
        return $clone;
    }

    /**
     * @return string
     */
    public function getSubTitle()
    {
        return $this->subTitle;
    }

    /**
     * @param string $description
     * @return self
     */
    public function withDescription($description)
    {
        $clone = clone $this;
        $clone->description = $description;
        return $clone;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $url
     * @return self
     */
    public function withUrl($url)
    {
        $clone = clone $this;
        $clone->url = $url;
        return $clone;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $image
     * @return self
     */
    public function withImage($image)
    {
        $clone = clone $this;
        $clone->image = $image;
        return $clone;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param array $images
     * @return self
     */
    public function withImages(array $images)
    {
        $clone = clone $this;
        $clone->images = $images;
        return $clone;
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param string $statsHeading
     * @return self
     */
    public function withStatsHeading($statsHeading)
    {
        $clone = clone $this;
        $clone->statsHeading = $statsHeading;
        return $clone;
    }

    /**
     * @return string
     */
    public function getStatsHeading()
    {
        return $this->statsHeading;
    }

    /**
     * @param string $statsLowest
     * @return self
     */
    public function withStatsLowest($statsLowest)
    {
        $clone = clone $this;
        $clone->statsLowest = $statsLowest;
        return $clone;
    }

    /**
     * @return string
     */
    public function getStatsLowest()
    {
        return $this->statsLowest;
    }

    /**
     * @param string $statsHighest
     * @return self
     */
    public function withStatsHighest($statsHighest)
    {
        $clone = clone $this;
        $clone->statsHighest = $statsHighest;
        return $clone;
    }

    /**
     * @return string
     */
    public function getStatsHighest()
    {
        return $this->statsHighest;
    }

    /**
     * @param string $statsAverage
     * @return self
     */
    public function withStatsAverage($statsAverage)
    {
        $clone = clone $this;
        $clone->statsAverage = $statsAverage;
        return $clone;
    }

    /**
     * @return string
     */
    public function getStatsAverage()
    {
        return $this->statsAverage;
    }

    /**
     * @param string $statsTotal
     * @return self
     */
    public function withStatsTotal($statsTotal)
    {
        $clone = clone $this;
        $clone->statsTotal = $statsTotal;
        return $clone;
    }

    /**
     * @return string
     */
    public function getStatsTotal()
    {
        return $this->statsTotal;
    }

    /**
     * @param array $listings
     * @return self
     */
    public function withListings($listings)
    {
        $clone = clone $this;
        $clone->listings = $listings;
        return $clone;
    }

    /**
     * @return array
     */
    public function getListings()
    {
        return $this->listings;
    }

    /**
     * @param array $idxStats
     * @return self
     */
    public function withIdxStats(array $idxStats)
    {
        $clone = clone $this;
        $clone->idxStats = $idxStats;
        return $clone;
    }

    /**
     * @return array
     */
    public function getIdxStats()
    {
        return $this->idxStats;
    }

    /**
     * @param array $typeStats
     * @return self
     */
    public function withTypeStats(array $typeStats)
    {
        $clone = clone $this;
        $clone->typeStats = $typeStats;
        return $clone;
    }

    /**
     * @return array
     */
    public function getTypeStats()
    {
        return $this->typeStats;
    }

    /**
     * @param string $searchUrl
     * @return self
     */
    public function withSearchUrl($searchUrl)
    {
        $clone = clone $this;
        $clone->searchUrl = $searchUrl;
        return $clone;
    }

    /**
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->searchUrl;
    }


    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            self::FLD_TITLE => $this->title,
            self::FLD_SUBTITLE => $this->subTitle,
            self::FLD_DESCRIPTION => $this->description,
            self::FLD_URL => $this->url,
            self::FLD_IMAGE => $this->image,
            self::FLD_IMAGES => $this->images,
            self::FLD_STATS_HEADING => $this->statsHeading,
            self::FLD_STATS_AVERAGE => $this->statsAverage,
            self::FLD_STATS_LOWEST => $this->statsLowest,
            self::FLD_STATS_HIGHEST => $this->statsHighest,
            self::FLD_STATS_TOTAL => $this->statsTotal,
            self::FLD_LISTINGS => $this->listings,
            self::FLD_IDX_STATS => $this->idxStats,
            self::FLD_TYPE_STATS => $this->typeStats,
            self::FLD_SEARCH_URL => $this->searchUrl
        ];
    }
}
