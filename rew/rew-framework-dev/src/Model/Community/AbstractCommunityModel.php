<?php
namespace REW\Model\Community;

class AbstractCommunityModel
{
    /**
     * @var string
     */
    protected $id = '';

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
    protected $statsHeading = 'Real Estate Statistics';

    /**
     * @var mixed
     */
    protected $statsTotal = 'Total Listings';

    /**
     * @var mixed
     */
    protected $statsAverage = 'Average Price';

    /**
     * @var mixed
     */
    protected $statsHighest = 'Highest Price';

    /**
     * @var mixed
     */
    protected $statsLowest = 'Lowest Price';

    /**
     * @var string
     */
    protected $anchorOneText = 'Community Summary';

    /**
     * @var string
     */
    protected $anchorOneLink = '#community-summary';

    /**
     * @var string
     */
    protected $anchorTwoText = 'Homes for Sale';

    /**
     * @var string
     */
    protected $anchorTwoLink = '#homes-for-sale';

    /**
     * Immutable setter to specify the ID of the desired community.
     * @param string $id
     * @return self
     */
    public function withId($id)
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    /**
     * Returns the ID of the desired community.
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

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
     * @param int $statsTotal
     * @return self
     */
    public function withStatsTotal($statsTotal)
    {
        $clone = clone $this;
        $clone->statsTotal = $statsTotal;
        return $clone;
    }

    /**
     * @return mixed
     */
    public function getStatsTotal()
    {
        return $this->statsTotal;
    }

    /**
     * @param int $statsAverage
     * @return self
     */
    public function withStatsAverage($statsAverage)
    {
        $clone = clone $this;
        $clone->statsAverage = $statsAverage;
        return $clone;
    }

    /**
     * @return mixed
     */
    public function getStatsAverage()
    {
        return $this->statsAverage;
    }

    /**
     * @param int $statsHighest
     * @return self
     */
    public function withStatsHighest($statsHighest)
    {
        $clone = clone $this;
        $clone->statsHighest = $statsHighest;
        return $clone;
    }

    /**
     * @return mixed
     */
    public function getStatsHighest()
    {
        return $this->statsHighest;
    }

    /**
     * @param int $statsLowest
     * @return self
     */
    public function withStatsLowest($statsLowest)
    {
        $clone = clone $this;
        $clone->statsLowest = $statsLowest;
        return $clone;
    }

    /**
     * @return mixed
     */
    public function getStatsLowest()
    {
        return $this->statsLowest;
    }

    /**
     * @param string $anchorOneText
     * @return self
     */
    public function withAnchorOneText($anchorOneText)
    {
        $clone = clone $this;
        $clone->anchorOneText = $anchorOneText;
        return $clone;
    }

    /**
     * @return string
     */
    public function getAnchorOneText()
    {
        return $this->anchorOneText;
    }

    /**
     * @param string $anchorOneLink
     * @return self
     */
    public function withAnchorOneLink($anchorOneLink)
    {
        $clone = clone $this;
        $clone->anchorOneLink = $anchorOneLink;
        return $clone;
    }

    /**
     * @return string
     */
    public function getAnchorOneLink()
    {
        return $this->anchorOneLink;
    }

    /**
     * @param string $anchorTwoText
     * @return self
     */
    public function withAnchorTwoText($anchorTwoText)
    {
        $clone = clone $this;
        $clone->anchorTwoText = $anchorTwoText;
        return $clone;
    }

    /**
     * @return string
     */
    public function getAnchorTwoText()
    {
        return $this->anchorTwoText;
    }

    /**
     * @param string $anchorTwoLink
     * @return self
     */
    public function withAnchorTwoLink($anchorTwoLink)
    {
        $clone = clone $this;
        $clone->anchorTwoLink = $anchorTwoLink;
        return $clone;
    }

    /**
     * @return string
     */
    public function getAnchorTwoLink()
    {
        return $this->anchorTwoLink;
    }
}
