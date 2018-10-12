<?php
namespace REW\Factory\Idx\Favorite;

use REW\Model\Idx\Favorite\FavoriteListingModel;

interface FavoriteListingFactoryInterface
{

    /**
     * @param array $data
     * @return FavoriteListingModel
     */
    public function createFromArray(array $data);
}
