<?php
namespace REW\Model\Idx\Search;

/**
 * Class ListingResults
 *
 * This model is used to house IDX search results, and contains any necessary cursor pagination info.
 *
 * @package REW\Model\Idx\Search
 */
class ListingResults implements \JsonSerializable
{
    /**
     * @var string
     */
    const FLD_LISTING_RESULTS = 'listingResults';

    /**
     * @var string
     */
    const FLD_BEFORE = 'before';

    /**
     * @var string
     */
    const FLD_AFTER = 'after';

    /**
     * @var string
     */
    const FLD_LIMIT = 'limit';

    /**
     * @var \REW\Model\Idx\Search\ListingResult[]
     */
    protected $listingResults = [];

    /**
     * @var \REW\Pagination\Pagination
     */
    protected $pagination;

    /**
     * @var string
     */
    protected $baseSearchUrl;

    /**
     * @param \REW\Model\Idx\Search\ListingResult[] $listingResults The listing results (or a subset of them).
     * @return self
     */
    public function withListingResults(array $listingResults)
    {
        $clone = clone $this;
        $clone->listingResults = $listingResults;
        return $clone;
    }

    /**
     * @return \REW\Model\Idx\Search\ListingResult[]
     */
    public function getListingResults()
    {
        return $this->listingResults;
    }

    /**
     * @param \REW\Pagination\Pagination $pagination
     * @return self
     */
    public function withPagination(\REW\Pagination\Pagination $pagination)
    {
        $clone = clone $this;
        $clone->pagination = $pagination;
        return $clone;
    }

    /**
     * @return \REW\Pagination\Pagination
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @param string $baseSearchUrl
     * @return self
     */
    public function withBaseSearchUrl($baseSearchUrl)
    {
        $clone = clone $this;
        $clone->baseSearchUrl = $baseSearchUrl;
        return $clone;
    }

    /**
     * @return string
     */
    public function getBaseSearchUrl()
    {
        return $this->baseSearchUrl;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            self::FLD_LIMIT => count($this->listingResults),
            self::FLD_BEFORE => $this->pagination->getPrevLink($this->baseSearchUrl),
            self::FLD_AFTER => $this->pagination->getNextLink($this->baseSearchUrl),
            self::FLD_LISTING_RESULTS => $this->listingResults,
        ];
    }
}
