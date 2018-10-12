<?php
namespace REW\Factory\Idx\Favorite\Listing;

use REW\Factory\Idx\Favorite\FavoriteListingFactoryInterface;
use REW\Model\Idx\Favorite\FavoriteListingModel;

class FavoriteListingFactory implements FavoriteListingFactoryInterface
{
    /**
     * @param array $data
     * @return FavoriteListingModel
     */
    public function createFromArray(array $data)
    {
        $favoriteListing = new FavoriteListingModel();
        $favoriteListing = $favoriteListing
            ->withId($data['id'])
            ->withUserId($data['user_id'])
            ->withAgentId($data['agent_id'])
            ->withAssociate($data['associate'])
            ->withMlsNumber($data['mls_number'])
            ->withTable($data['table'])
            ->withIdx($data['idx'])
            ->withType($data['type'])
            ->withCity($data['city'])
            ->withSubdivision($data['subdivision'])
            ->withBedrooms($data['bedrooms'])
            ->withBathrooms($data['bathrooms'])
            ->withSqft($data['sqft'])
            ->withPrice($data['price'])
            ->withUserNote($data['user_note'])
            ->withTimestamp($data['timestamp']);
        return $favoriteListing;
    }
}
