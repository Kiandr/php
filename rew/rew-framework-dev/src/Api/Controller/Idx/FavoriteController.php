<?php
namespace REW\Api\Controller\Idx;

use REW\Model\User\Search\UserRequestModel;
use REW\Datastore\User\SearchDatastore;
use REW\Model\Idx\Favorite\FavoriteRequestModel;
use REW\Datastore\Listing\FavoriteDatastore;
use REW\Api\Exception\Request\BadRequestException;

/**
 * Class FavoriteController
 * @package REW\Controller\Idx
 */
class FavoriteController
{

    /**
     * @var SearchDatastore
     */
    protected $userDatastore;

    /**
     * @var FavoriteDatastore
     */
    protected $favoriteDatastore;

    /**
     * @param SearchDatastore $userDatastore
     * @param FavoriteDatastore $favoriteDatastore
     */
    public function __construct(SearchDatastore $userDatastore, FavoriteDatastore $favoriteDatastore)
    {
        $this->userDatastore = $userDatastore;
        $this->favoriteDatastore = $favoriteDatastore;
    }

    /**
     *
     * @param int $userToken
     * @param string $feed
     * @return ListingResult
     */
    public function getFavorites($userToken, $feed)
    {
        // Get User
        $user = $this->getUser($userToken);
        $listingRequest = (new FavoriteRequestModel())
            ->withUserId($user->getId())
            ->withFeedName($feed);
        return $this->favoriteDatastore->getFavorites($listingRequest);
    }

    /**
     * Add listing add favorite
     * @param int $userToken
     * @param string $feed
     * @param string $listingId
     * @param string $listingType
     * @return ListingResult
     */
    public function addFavorite ($userToken, $feed, $listingId, $listingType)
    {
        // Get User
        $user = $this->getUser($userToken);
        $listingRequest = (new FavoriteRequestModel())
            ->withUserId($user->getId())
            ->withListingId($listingId)
            ->withListingType($listingType)
            ->withFeedName($feed);
        return $this->favoriteDatastore->createFavorite($listingRequest);
    }


    /**
     * Remove listing from favorite
     * @param int $userToken
     * @param string $feed
     * @param string $listingId
     * @param string $listingType
     * @return ListingResult
     */
    public function removeFavorite ($userToken, $feed, $listingId, $listingType)
    {
        // Get User
        $user = $this->getUser($userToken);
        $listingRequest = (new FavoriteRequestModel())
            ->withUserId($user->getId())
            ->withListingId($listingId)
            ->withListingType($listingType)
            ->withFeedName($feed);
        return $this->favoriteDatastore->deleteFavorite($listingRequest);
    }

    /**
     * Get User from Token
     * @param string $userToken
     * @throws BadRequestException If no user token is supplied
     * @throws BadRequestException If user token is unknown
     * @return UserResultModel
     */
    protected function getUser($userToken)
    {
        if (empty($userToken)) {
            throw new BadRequestException('A valid user token is required to manage favorites.');
        }
        $userRequest = (new UserRequestModel())->withToken($userToken);
        $user = $this->userDatastore->getUserFromToken($userRequest);
        if (empty($user)) {
            throw new BadRequestException('The provided user token does not match any valid users.');
        }
        return $user;
    }
}
