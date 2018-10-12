<?php
namespace REW\Test\Datastore\Listing;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\IDX\ComplianceInterface;
use REW\Datastore\Listing\SearchDatastore;
use REW\Factory\Idx\Search\Field\FieldFactory;
use REW\Model\Idx\Search\Field\Field;
use REW\Model\Idx\Search\ListingRequest;
use REW\Factory\Idx\Search\ListingResultFactory;
use REW\Pagination\Cursor;
use REW\Pagination\Cursor\After;
use REW\Pagination\Cursor\Before;
use REW\Pagination\Pagination;
use Mockery as m;

class SearchDatastoreTest extends \Codeception\Test\Unit
{
    /** @var int */
    const LIMIT_TEN = 10;

    /**
     * @var SearchDatastore
     */
    protected $searchDatastore;

    /**
     * @var \REW\Core\Interfaces\Factories\DBFactoryInterface
     */
    protected $database;

    /**
     * @var \REW\Core\Interfaces\Factories\IDXFactoryInterface
     */
    protected $idx;

    /**
     * @var \REW\Core\Interfaces\Util\IDXInterface
     */
    protected $mockedIdxUtilInterface;

    /**
     * @var ListingResultFactory
     */
    protected $listingResultFactory;

    /**
     * @var \REW\Core\Interfaces\IDX\ComplianceInterface
     */
    protected $compliance;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->listingResultFactory = new ListingResultFactory;

        $this->database = m::mock(DBFactoryInterface::class);

        // Setup the ComplianceInterface
        $this->compliance = m::mock(ComplianceInterface::class);
        $this->compliance->shouldReceive('load');
        $this->compliance->shouldReceive('offsetGet');

        // Setup the IDXInterface
        $idxInterface = m::mock(IDXInterface::class);
        $idxInterface->shouldReceive('getTable')->andReturn('_rewidx_listings');
        $idxInterface->shouldReceive('getFields')->andReturn([
           'ListingMLS'
        ]);
        $idxInterface->shouldReceive('field')->andReturn('a field');
        $idxInterface->shouldReceive('selectColumns')->andReturn('columns');
        $this->idx = m::mock(IDXFactoryInterface::class);
        $this->idx->shouldReceive('getIdx')->andReturn($idxInterface);
        $this->mockedIdxUtilInterface = m::mock(\REW\Core\Interfaces\Util\IDXInterface::class);
        $this->mockedIdxUtilInterface->shouldReceive('parseListing')->andReturn(['url_details' => 'http://url-here.com/listing']);
    }
    /**
     * @covers \REW\Datastore\Listing\SearchDatastore::getListings
     * @return void
     */
    public function testGetListings()
    {
        $mockedStmt = m::mock(\PDOStatement::class);
        $mockedStmt->shouldReceive('execute')
            ->andReturn(true);
        $mockedStmt->shouldReceive('fetchAll')
            ->andReturn($this->generateSampleListings(self::LIMIT_TEN));
        $mockedPdo = m::mock(\PDO::class);
        $mockedPdo->shouldReceive('prepare')
            ->andReturn($mockedStmt);
        $this->database->shouldReceive('get')
            ->with('abor')
            ->andReturn($mockedPdo);
        $datastore = new SearchDatastore($this->database, $this->idx, $this->mockedIdxUtilInterface, $this->listingResultFactory, $this->compliance);
        $cursor = new Cursor('ListingMLS', self::LIMIT_TEN, 'ListingMLS', 'ASC');
        $pagination = new Pagination($cursor);
        $listingRequest = (new ListingRequest())->withPagination($pagination)->withFeedName('abor');
        $result = $datastore->getListings($listingRequest);
        $this->assertEquals(self::LIMIT_TEN, count($result->getListingResults()));
    }

    /**
     * @covers \REW\Datastore\Listing\SearchDatastore::getListings
     * @return void
     */
    public function testGetListingsByPriceRange()
    {
        $mockedStmt = m::mock(\PDOStatement::class);
        $mockedStmt->shouldReceive('execute')
            ->andReturn(true);
        $mockedStmt->shouldReceive('fetchAll')
            ->andReturn($this->generateSampleListings(self::LIMIT_TEN, 50000, 100000));
        $mockedPdo = m::mock(\PDO::class);
        $mockedPdo->shouldReceive('prepare')
            ->andReturn($mockedStmt);
        $this->database->shouldReceive('get')
            ->with('abor')
            ->andReturn($mockedPdo);

        $datastore = new SearchDatastore($this->database, $this->idx, $this->mockedIdxUtilInterface, $this->listingResultFactory, $this->compliance);
        $cursor = new Cursor('ListingMLS', self::LIMIT_TEN, 'ListingMLS', 'ASC');
        $pagination = new Pagination($cursor);
        $fieldFactory = new FieldFactory();
        $maxPrice = $fieldFactory->createFromArray(['idx_field' => 'ListingPrice', 'form_field' => 'maximum_price', 'name' => 'Max. Price', 'match' => 'lessthan'])->withSearchValue(100000);
        $minPrice = $fieldFactory->createFromArray(['idx_field' => 'ListingPrice', 'form_field' => 'minimum_price', 'name' => 'Min. Price', 'match' => 'morethan'])->withSearchValue(50000);

        $listingRequest = (new ListingRequest())->withPagination($pagination)->withAdditionalSearchCriterion($minPrice)->withAdditionalSearchCriterion($maxPrice)->withFeedName('abor');
        $result = $datastore->getListings($listingRequest);
        $this->assertEquals(self::LIMIT_TEN, count($result->getListingResults()));
        foreach ($result->getListingResults() as $listingResult) {
            // Make sure the price is within the bounds specified
            $this->assertEquals(true, (50000 <= $listingResult->getListPrice() && $listingResult->getListPrice() <= 100000));
        }
    }

    /**
     * @covers \REW\Datastore\Listing\SearchDatastore::getListings
     * @return void
     */
    public function testResultsPagination()
    {
        $mockedStmt = m::mock(\PDOStatement::class);
        $mockedStmt->shouldReceive('execute')
            ->andReturn(true);
        $mockedStmt->shouldReceive('fetchAll')
            ->andReturn(
                $this->generateSampleListings(self::LIMIT_TEN + 1, null, null, null, 'Residential'),
                $this->generateSampleListings(self::LIMIT_TEN, null, null, 9, 'Residential'),
                $this->generateSampleListings(self::LIMIT_TEN, null, null, 19, 'Residential'),
                $this->generateSampleListings(self::LIMIT_TEN, null, null, 9, 'Residential'),
                $this->generateSampleListings(self::LIMIT_TEN, null, null, null, 'Residential')
            );
        $mockedPdo = m::mock(\PDO::class);
        $mockedPdo->shouldReceive('prepare')
            ->andReturn($mockedStmt);
        $this->database->shouldReceive('get')
            ->with('abor')
            ->andReturn($mockedPdo);
        $datastore = new SearchDatastore($this->database, $this->idx, $this->mockedIdxUtilInterface, $this->listingResultFactory, $this->compliance);
        $cursor = new Cursor('ListingMLS', self::LIMIT_TEN, 'ListingMLS', 'ASC');
        $pagination = new Pagination($cursor);
        $fieldFactory = new FieldFactory();
        $searchType = $fieldFactory->createFromArray(['idx_field' => 'ListingType', 'form_field' => 'search_type', 'name' => 'Property Type', 'match' => 'equals'])->withSearchValue('Residential');
        $listingRequest = (new ListingRequest())->withPagination($pagination)->withAdditionalSearchCriterion($searchType)->withFeedName('abor');
        $firstResult = $datastore->getListings($listingRequest);
        $this->assertEquals(self::LIMIT_TEN, count($firstResult->getListingResults()));
        foreach ($firstResult->getListingResults() as $listingResult) {
            // Make sure the price is within the bounds specified
            $this->assertEquals(true, $listingResult->getPropertyType() == 'Residential');
        }

        // Paginate forward
        $afterEncode = $firstResult->getPagination()->getAfter()->encode();
        $pagination = new Pagination(After::decode($afterEncode));
        $listingRequest = $listingRequest->withPagination($pagination);
        $secondResult = $datastore->getListings($listingRequest);
        $this->assertNotEquals($secondResult, $firstResult);
        foreach ($secondResult->getListingResults() as $listingResult) {
            $this->assertGreaterThanOrEqual($pagination->getBefore()->getId(), $listingResult->getId());
            $this->assertEquals(true, $listingResult->getPropertyType() == 'Residential');
        }

        $afterEncode = $secondResult->getPagination()->getAfter()->encode();
        $pagination = new Pagination(After::decode($afterEncode));
        $listingRequest = $listingRequest->withPagination($pagination);
        $thirdResult = $datastore->getListings($listingRequest);
        $this->assertNotEquals($thirdResult, $secondResult);
        $this->assertNotEquals($thirdResult, $firstResult);
        foreach ($thirdResult->getListingResults() as $listingResult) {
            $this->assertGreaterThanOrEqual($pagination->getBefore()->getId(), $listingResult->getId());
            $this->assertEquals(true, $listingResult->getPropertyType() == 'Residential');
        }

        // And now go back to see if that works...
        $beforeEncode = $thirdResult->getPagination()->getBefore()->encode();
        $pagination = new Pagination(Before::decode($beforeEncode));
        $listingRequest = $listingRequest->withPagination($pagination);
        $fourthResult = $datastore->getListings($listingRequest);
        foreach ($fourthResult->getListingResults() as $listingResult) {
            $this->assertLessThanOrEqual($pagination->getBefore()->getId() + 1, $listingResult->getId());
            $this->assertEquals(true, $listingResult->getPropertyType() == 'Residential');
        }

        $beforeEncode = $fourthResult->getPagination()->getBefore()->encode();
        $pagination = new Pagination(Before::decode($beforeEncode));
        $listingRequest = $listingRequest->withPagination($pagination);
        $fifthResult = $datastore->getListings($listingRequest);
        foreach ($fifthResult->getListingResults() as $listingResult) {
            $this->assertLessThanOrEqual($pagination->getBefore()->getId() + 1, $listingResult->getId());
            $this->assertEquals(true, $listingResult->getPropertyType() == 'Residential');
        }
    }

    /**
     * Generates some sample listing data based on some parameters.
     * @param int $count
     * @param int $minPrice
     * @param int $maxPrice
     * @param int $startingMls
     * @param string $listingType
     * @return array
     */
    protected function generateSampleListings($count, $minPrice = null, $maxPrice = null, $startingMls = null, $listingType = null)
    {
        $sampleListings = [];
        if (is_null($startingMls) || is_nan($startingMls)) {
            $startingMls = 0;
        }
        for ($i = $startingMls; $i < $startingMls + $count; $i++) {
            $price = 0;
            if (isset($minPrice)) {
                $price = $minPrice;
            }
            if (isset($maxPrice)) {
                $price = $maxPrice;
            }
            if (isset($minPrice) && isset($maxPrice)) {
                if ($i % 2 == 0) {
                    $price = $minPrice;
                } else {
                    $price = $maxPrice;
                }
            }

            if (is_null($listingType)) {
                $listingType = 'listing_type_here';
            }
            $sampleListings[] = [
                'id' => $i,
                'ListingImage' => 'image_url_here',
                'Address' => 'address_here',
                'AddressCity' => 'city_here',
                'ListingPrice' => $price,
                'ListingPriceOld' => $price,
                'ListingDOM' => 0,
                'ListingType' => $listingType,
                'ListingAgent' => 'Carl Sagan',
                'ListingOffice' => 'The Comos',
                'NumberOfBedrooms' => 4,
                'NumberOfBathrooms' => 3,
                'NumberOfSqFt' => 2,
                'ListingMLS' => $i,
                'Latitude' => 0.00,
                'Longitude' => 0.00
            ];
        }
        return $sampleListings;
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }
}
