<?php
namespace REW\Factory\Idx\Search;

use REW\Model\Idx\Search\ListingResult;

class ListingResultFactory
{
    /** @var string */
    const DEFAULT_CURRENCY = '$';

    /**
     * @param array $data
     * @return ListingResult
     */
    public function createFromArray($data)
    {
        $listingResult = new ListingResult();
        $listingResult = $listingResult
        ->withPhoto($data['ListingImage'])
        ->withAddress($data['Address'])
        ->withCity($data['AddressCity'])
        ->withLatitude($data['Latitude'])
        ->withLongitude($data['Longitude'])
        ->withListPrice($data['ListingPrice'])
        ->withOldPrice($data['ListingPriceOld'])
        ->withDom($data['ListingDOM'])
        ->withEnhanced($data['enhanced'])
        ->withCurrency(self::DEFAULT_CURRENCY)
        ->withPropertyType($data['ListingType'])
        ->withBedrooms($data['NumberOfBedrooms'])
        ->withBathrooms($data['NumberOfBathrooms'])
        ->withLotSize($data['NumberOfSqFt'])
        ->withLotSizeUnit('SqFt')
        ->withId($data['ListingMLS'])
        ->withFeed($data['feed'])
        ->withUrl($data['url_details'])
        ->withAgent($data['ListingAgent'])
        ->withOffice($data['ListingOffice'])
        ->withCompliance($data['compliance']);

        return $listingResult;
    }
}
