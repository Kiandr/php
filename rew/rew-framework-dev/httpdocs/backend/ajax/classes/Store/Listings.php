<?php

namespace REW\Api\Internal\Store;

use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Util\IDXInterface as IDXUtilsInterface;

class Listings
{
    /**
     * @var IDXFactoryInterface
     */
    protected $idxFactory;

    /**
     * @var IDXUtilsInterface
     */
    protected $idxUtils;

    /**
     * @param IDXFactoryInterface $idxFactory
     * @param IDXUtilsInterface $idxUtils
     */
    public function __construct(
        IDXFactoryInterface $idxFactory,
        IDXUtilsInterface $idxUtils
    ){
        $this->idxFactory = $idxFactory;
        $this->idxUtils = $idxUtils;
    }

    /**
     * @param string $feed
     * @param string $mlsNumber
     * @param array $fields
     * @return array|null
     */
    public function getListing($mlsFeed, $mlsNumber, array $fields)
    {
        $this->idxFactory->switchFeed($mlsFeed);
        $idx = $this->idxFactory->getIdx($mlsFeed);
        $idxDb = $this->idxFactory->getDatabase($mlsFeed);
        $queryColumns = $idx->selectColumns('', $fields);
        $queryString = "SELECT %s FROM `%s` WHERE `%s` = '%s' LIMIT 1;";
        $queryString = sprintf($queryString, $queryColumns, $idx->getTable(), $idx->field('ListingMLS'), $idxDb->cleanInput($mlsNumber));
        if ($idxListing = $idxDb->fetchQuery($queryString)) {
            $idxListing = $this->idxUtils->parseListing($idxListing);
            return $idxListing;

        }
        return NULL;

    }



    /**
     * @param string[] $mlsNumbers
     * @param string $mlsFeed
     * @return array|NULL
     */
    public function getListings($mlsFeed, array $mlsNumbers, array $fields) {
        $this->idxFactory->switchFeed($mlsFeed);
        $idx = $this->idxFactory->getIdx($mlsFeed);
        $idxDb = $this->idxFactory->getDatabase($mlsFeed);
        $queryColumns = $idx->selectColumns('', $fields);
        $whereQuery = sprintf('`%s` IN (\'%s\')',
            $idx->field('ListingMLS'),
            implode("', '", array_map(function ($mlsNumber) use ($idxDb) {
                return $idxDb->cleanInput($mlsNumber);
            }, $mlsNumbers))
        );
        $mlsListings = [];
        $queryString = sprintf("SELECT %s FROM `%s` WHERE %s;", $queryColumns, $idx->getTable(), $whereQuery);
        $result = $idxDb->query($queryString);
        while ($mlsListing = $idxDb->fetchArray($result)) {
            $mlsNumber = $mlsListing['ListingMLS'];
            $mlsListing = $this->idxUtils->parseListing($mlsListing);
            $mlsListings[$mlsNumber] = $mlsListing;
        }
        return $mlsListings;
    }

}