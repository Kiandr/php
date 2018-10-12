<?php
namespace REW\Model\Idx\Search;


/**
 * Class ListingResult
 * @package REW\Model\Idx\Search
 */
class ListingResult implements ListingResultInterface
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $photo;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var double
     */
    protected $latitude;

    /**
     * @var double
     */
    protected $longitude;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $listPrice;

    /**
     * @var int
     */
    protected $oldPrice;

    /**
     * @var int
     */
    protected $dom;

    /**
     * @var array
     */
    protected $enhanced;

    /**
     * @var string
     */
    protected $propertyType;

    /**
     * @var string
     */
    protected $bedrooms;

    /**
     * @var float
     */
    protected $bathrooms;

    /**
     * @var float
     */
    protected $lotSize;

    /**
     * @var string
     */
    protected $lotSizeUnit;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $feed;

    /**
     * @var string
     */
    protected $agent;

    /**
     * @var string
     */
    protected $office;

    /**
     * @var string
     */
    protected $compliance;

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
     * @param string $photo
     * @return self
     */
    public function withPhoto($photo)
    {
        $clone = clone $this;
        $clone->photo = $photo;
        return $clone;
    }

    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param $address
     * @return self
     */
    public function withAddress($address)
    {
        $clone = clone $this;
        $clone->address = $address;
        return $clone;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $city
     * @return self
     */
    public function withCity($city)
    {
        $clone = clone $this;
        $clone->city = $city;
        return $clone;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param double $latitude
     * @return self
     */
    public function withLatitude($latitude)
    {
        $clone = clone $this;
        $clone->latitude = doubleval($latitude);
        return $clone;
    }

    /**
     * @return double
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param double $longitude
     * @return self
     */
    public function withLongitude($longitude)
    {
        $clone = clone $this;
        $clone->longitude = doubleval($longitude);
        return $clone;
    }

    /**
     * @return double
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $currency
     * @return self
     */
    public function withCurrency($currency)
    {
        $clone = clone $this;
        $clone->currency = $currency;
        return $clone;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param int $listPrice
     * @return self
     */
    public function withListPrice($listPrice)
    {
        $clone = clone $this;
        $clone->listPrice = intval($listPrice);
        return $clone;
    }

    /**
     * @return int
     */
    public function getListPrice()
    {
        return $this->listPrice;
    }

    /**
     * @param int $oldPrice
     * @return self
     */
    public function withOldPrice($oldPrice)
    {
        $clone = clone $this;
        $clone->oldPrice = intval($oldPrice);
        return $clone;
    }

    /**
     * @return int
     */
    public function getOldPrice()
    {
        return $this->oldPrice;
    }

    /**
     * @param int $dom
     * @return self
     */
    public function withDom($dom)
    {
        $clone = clone $this;
        $clone->dom = ($dom === null) ? null : intval($dom);
        return $clone;
    }

    /**
     * @return int
     */
    public function getDom()
    {
        return $this->dom;
    }

    /**
     * @param array $enhanced
     * @return self
     */
    public function withEnhanced($enhanced)
    {
        $clone = clone $this;
        $clone->enhanced = $enhanced;
        return $clone;
    }

    /**
     * @return array
     */
    public function getEnhanced()
    {
        return $this->enhanced;
    }


    /**
     * @param string $propertyType
     * @return self
     */
    public function withPropertyType($propertyType)
    {
        $clone = clone $this;
        $clone->propertyType = $propertyType;
        return $clone;
    }

    /**
     * @return string
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }

    /**
     * @param int $bedrooms
     * @return self
     */
    public function withBedrooms($bedrooms)
    {
        $clone = clone $this;
        $clone->bedrooms = intval($bedrooms);
        return $clone;
    }

    /**
     * @return int
     */
    public function getBedrooms()
    {
        return $this->bedrooms;
    }

    /**
     * @param float $bathrooms
     * @return self
     */
    public function withBathrooms($bathrooms)
    {
        $clone = clone $this;
        $clone->bathrooms = floatval($bathrooms);
        return $clone;
    }

    /**
     * @return float
     */
    public function getBathrooms()
    {
        return $this->bathrooms;
    }

    /**
     * @param float $lotSize
     * @return self
     */
    public function withLotSize($lotSize)
    {
        $clone = clone $this;
        $clone->lotSize = floatval($lotSize);
        return $clone;
    }

    /**
     * @return float
     */
    public function getLotSize()
    {
        return $this->lotSize;
    }

    /**
     * @param string $lotSizeUnit
     * @return self
     */
    public function withLotSizeUnit($lotSizeUnit)
    {
        $clone = clone $this;
        $clone->lotSizeUnit = $lotSizeUnit;
        return $clone;
    }

    /**
     * @return string
     */
    public function getLotSizeUnit()
    {
        return $this->lotSizeUnit;
    }

    /**
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
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $feed
     * @return self
     */
    public function withFeed($feed)
    {
        $clone = clone $this;
        $clone->feed = $feed;
        return $clone;
    }

    /**
     * @return string
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param string $agent
     * @return self
     */
    public function withAgent($agent)
    {
        $clone = clone $this;
        $clone->agent = $agent;
        return $clone;
    }

    /**
     * @return string
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param string $office
     * @return self
     */
    public function withOffice($office)
    {
        $clone = clone $this;
        $clone->office = $office;
        return $clone;
    }

    /**
     * @return string
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param string $compliance
     * @return self
     */
    public function withCompliance($compliance)
    {
        $clone = clone $this;
        $clone->compliance = $compliance;
        return $clone;
    }

    /**
     * @return string
     */
    public function getCompliance()
    {
        return $this->compliance;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            self::FLD_URL => $this->url,
            self::FLD_PHOTO => $this->photo,
            self::FLD_ADDRESS => $this->address,
            self::FLD_CITY => $this->city,
            self::FLD_LATITUDE => $this->latitude,
            self::FLD_LONGITUDE => $this->longitude,
            self::FLD_CURRENCY => $this->currency,
            self::FLD_LIST_PRICE => $this->listPrice,
            self::FLD_OLD_PRICE => $this->oldPrice,
            self::FLD_DOM => $this->dom,
            self::FLD_ENHANCED => $this->enhanced,
            self::FLD_PROPERTY_TYPE => $this->propertyType,
            self::FLD_BEDROOMS => $this->bedrooms,
            self::FLD_BATHROOMS => $this->bathrooms,
            self::FLD_LOT_SIZE => $this->lotSize,
            self::FLD_LOT_SIZE_UNIT => $this->lotSizeUnit,
            self::FLD_ID => $this->id,
            self::FLD_FEED => $this->feed,
            self::FLD_AGENT => $this->agent,
            self::FLD_OFFICE => $this->office,
            self::FLD_COMPLIANCE => $this->compliance
        ];
    }
}
