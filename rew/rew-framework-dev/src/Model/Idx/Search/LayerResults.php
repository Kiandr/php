<?php
namespace REW\Model\Idx\Search;

/**
 * Class LayerResults
 *
 * This model is used to house ON_BOARD layer results.
 *
 * @package REW\Model\Idx\Search
 */
class LayerResults implements \JsonSerializable
{
    /**
     * @var string
     */
    const FLD_LAYER_RESULTS = 'layerResults';

    /**
     * @var string
     */
    const FLD_LIMIT = 'limit';

    /**
     * @var \REW\Model\Idx\Search\ListingResult[]
     */
    protected $layerResults = [];

    /**
     * @var \REW\Model\Idx\Search\ListingResult[]
     */
    protected $communityResults = [];

    /**
     * @param \REW\Model\Idx\Search\LayerResult[] $layerResults The layer results (or a subset of them).
     * @return self
     */
    public function withLayerResults(array $layerResults)
    {
        $clone = clone $this;
        $clone->layerResults = $layerResults;
        return $clone;
    }

    /**
     * @return \REW\Model\Idx\Search\ListingResult[]
     */
    public function getLayerResults()
    {
        return $this->layerResults;
    }

    /**
     * @param \REW\Model\Idx\Search\CommunityResult $communityResult The community Statistics
     * @return self
     */
    public function withCommunityResult(array $communityResult)
    {
        $clone = clone $this;
        $clone->communityResult = $communityResult;
        return $clone;
    }

    /**
     * @return \REW\Model\Idx\Search\CommunityResult
     */
    public function getCommunityResult()
    {
        return $this->communityResult;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            self::FLD_LIMIT => count($this->layerResults),
            self::FLD_LAYER_RESULTS => $this->layerResults,
        ];
    }
}
