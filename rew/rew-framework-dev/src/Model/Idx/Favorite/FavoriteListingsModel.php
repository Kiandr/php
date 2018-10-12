<?php
namespace REW\Model\Idx\Favorite;

/**
 * Class FavoriteListingsModel
 * @package REW\Model\Idx\Favorite
 */
class FavoriteListingsModel implements \JsonSerializable
{

    /**
     * @var string
     */
    const FLD_FAVORITE_LISTINGS = 'favoriteListings';

    /**
     * @var \REW\Model\Idx\Favorite\FavoriteListingModel[]
     */
    protected $favoriteListings = [];

    /**
     * @param FavoriteListingModel[] $favoriteListings
     * @return self
     */
    public function withFavoriteListings(array $favoriteListings)
    {
        $clone = clone $this;
        $clone->favoriteListings = $favoriteListings;
        return $clone;
    }

    /**
     * @return \REW\Model\Idx\Favorite\FavoriteListingModel[]
     */
    public function getFavoriteListings()
    {
        return $this->favoriteListings;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            self::FLD_FAVORITE_LISTINGS => $this->favoriteListings,
        ];
    }}
