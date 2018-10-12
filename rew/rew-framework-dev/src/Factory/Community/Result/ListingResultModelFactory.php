<?php
namespace REW\Factory\Community\Result;

use REW\Factory\Community\ListingResultModelFactoryInterface;
use REW\Model\Community\Result\ListingResultModel;

class ListingResultModelFactory implements ListingResultModelFactoryInterface
{
    public function createFromArray(array $data = [])
    {
        $listingResult = new ListingResultModel();

        return $listingResult;
    }
}
