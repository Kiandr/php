<?php
namespace REW\Model\Community;

interface ResultModelInterface extends \JsonSerializable
{
    const FLD_TITLE = 'title';

    const FLD_SUBTITLE = 'subtitle';

    const FLD_DESCRIPTION = 'description';

    const FLD_URL = 'url';

    const FLD_IMAGE = 'image';

    const FLD_IMAGES = 'images';

    const FLD_STATS_HEADING = 'stats_heading';

    const FLD_STATS_LOWEST = 'stats_lowest';

    const FLD_STATS_HIGHEST = 'stats_highest';

    const FLD_STATS_AVERAGE = 'stats_average';

    const FLD_STATS_TOTAL = 'stats_total';

    const FLD_LISTINGS = 'listings';

    const FLD_IDX_STATS = 'stats';

    const FLD_TYPE_STATS = 'stats_type';

    const FLD_SEARCH_URL = 'search_url';

    /**
     * @param string $title
     * @return self
     */
    public function withTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $subTitle
     * @return self
     */
    public function withSubTitle($subTitle);

    /**
     * @return string
     */
    public function getSubTitle();

    /**
     * @param string $description
     * @return self
     */
    public function withDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $url
     * @return self
     */
    public function withUrl($url);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $image
     * @return self
     */
    public function withImage($image);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param array $images
     * @return self
     */
    public function withImages(array $images);

    /**
     * @return array
     */
    public function getImages();

    /**
     * @param string $statsHeading
     * @return self
     */
    public function withStatsHeading($statsHeading);

    /**
     * @return string
     */
    public function getStatsHeading();

    /**
     * @param string $statsTotal
     * @return self
     */
    public function withStatsTotal($statsTotal);

    /**
     * @return string
     */
    public function getStatsTotal();

    /**
     * @param string $statsHighest
     * @return self
     */
    public function withStatsHighest($statsHighest);

    /**
     * @return string
     */
    public function getStatsHighest();

    /**
     * @param string $statsAverage
     * @return self
     */
    public function withStatsAverage($statsAverage);

    /**
     * @return string
     */
    public function getStatsAverage();

    /**
     * @param string $statsLowest
     * @return self
     */
    public function withStatsLowest($statsLowest);

    /**
     * @return string
     */
    public function getStatsLowest();

    /**
     * @param array $listings
     * @return self
     */
    public function withListings($listings);

    /**
     * @return array
     */
    public function getListings();

    /**
     * @param array $idxStats
     * @return self
     */
    public function withIdxStats(array $idxStats);

    /**
     * @return array
     */
    public function getIdxStats();

    /**
     * @param array $typeStats
     * @return self
     */
    public function withTypeStats(array $typeStats);

    /**
     * @return array
     */
    public function getTypeStats();

    /**
     * @param string $searchUrl
     * @return self
     */
    public function withSearchUrl($searchUrl);

    /**
     * @return string
     */
    public function getSearchUrl();

}
