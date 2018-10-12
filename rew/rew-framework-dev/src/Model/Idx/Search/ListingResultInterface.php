<?php
namespace REW\Model\Idx\Search;

interface ListingResultInterface extends \JsonSerializable
{
    /**
     * @var string
     */
    const FLD_URL = 'url';

    /** @var string */
    const FLD_PHOTO = 'photo';

    /** @var string */
    const FLD_ADDRESS = 'address';

    /** @var string */
    const FLD_CITY = 'city';

    /** @var string  */
    const FLD_LONGITUDE = 'longitude';

    /** @var string */
    const FLD_LATITUDE = 'latitude';

    /** @var string */
    const FLD_CURRENCY = 'currency';

    /** @var string */
    const FLD_LIST_PRICE = 'listPrice';

    /** @var string */
    const FLD_OLD_PRICE = 'oldPrice';

    /** @var string */
    const FLD_DOM = 'dom';

    /** @var string */
    const FLD_ENHANCED = 'enhanced';

    /** @var string */
    const FLD_PROPERTY_TYPE = 'propertyType';

    /** @var string */
    const FLD_BEDROOMS = 'bedrooms';

    /** @var string */
    const FLD_BATHROOMS = 'bathrooms';

    /** @var string */
    const FLD_LOT_SIZE = 'lotSize';

    /** @var string */
    const FLD_LOT_SIZE_UNIT = 'lotSizeUnit';

    /** @var string */
    const FLD_ID = 'id';

    /** @var string */
    const FLD_FEED = 'feed';

    /** @var string */
    const FLD_AGENT = 'agent';

    /** @var string */
    const FLD_OFFICE = 'office';

    /** @var string */
    const FLD_COMPLIANCE = 'compliance';

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
     * @param string $photo
     * @return self
     */
    public function withPhoto($photo);

    /**
     * @return string
     */
    public function getPhoto();

    /**
     * @param $address
     * @return self
     */
    public function withAddress($address);

    /**
     * @return string
     */
    public function getAddress();

    /**
     * @param string $city
     * @return self
     */
    public function withCity($city);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param double $latitude
     * @return self
     */
    public function withLatitude($latitude);

    /**
     * @return double
     */
    public function getLatitude();

    /**
     * @param double $longitude
     * @return self
     */
    public function withLongitude($longitude);

    /**
     * @return double
     */
    public function getLongitude();

    /**
     * @param string $currency
     * @return self
     */
    public function withCurrency($currency);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param int $listPrice
     * @return self
     */
    public function withListPrice($listPrice);

    /**
     * @return int
     */
    public function getListPrice();

    /**
     * @param int $oldPrice
     * @return self
     */
    public function withOldPrice($oldPrice);

    /**
     * @return int
     */
    public function getOldPrice();

    /**
     * @param int $dom
     * @return self
     */
    public function withDom($dom);

    /**
     * @return int
     */
    public function getDom();

    /**
     * @param array $enhanced
     * @return self
     */
    public function withEnhanced($enhanced);

    /**
     * @return array
     */
    public function getEnhanced();

    /**
     * @param string $propertyType
     * @return self
     */
    public function withPropertyType($propertyType);

    /**
     * @return string
     */
    public function getPropertyType();

    /**
     * @param int $bedrooms
     * @return self
     */
    public function withBedrooms($bedrooms);

    /**
     * @return int
     */
    public function getBedrooms();

    /**
     * @param float $bathrooms
     * @return self
     */
    public function withBathrooms($bathrooms);

    /**
     * @return float
     */
    public function getBathrooms();

    /**
     * @param float $lotSize
     * @return self
     */
    public function withLotSize($lotSize);

    /**
     * @return float
     */
    public function getLotSize();

    /**
     * @param string $lotSizeUnit
     * @return self
     */
    public function withLotSizeUnit($lotSizeUnit);

    /**
     * @return string
     */
    public function getLotSizeUnit();

    /**
     * @param string $id
     * @return self
     */
    public function withId($id);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     * @return self
     */
    public function withFeed($feed);

    /**
     * @return string
     */
    public function getFeed();

    /**
     * @param string $agent
     * @return self
     */
    public function withAgent($agent);

    /**
     * @return string
     */
    public function getAgent();

    /**
     * @param string $office
     * @return self
     */
    public function withOffice($office);

    /**
     * @return string
     */
    public function getOffice();

    /**
     * @param string $compliance
     * @return self
     */
    public function withCompliance($compliance);

    /**
     * @return string
     */
    public function getCompliance();


    /**
     * @return array
     */
    public function jsonSerialize();
}
