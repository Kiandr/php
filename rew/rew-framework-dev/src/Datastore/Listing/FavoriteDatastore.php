<?php
namespace REW\Datastore\Listing;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Util\IDXInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Model\Idx\Favorite\FavoriteRequestModel;
use REW\Model\Idx\Favorite\FavoriteListingsModel;
use REW\Factory\Idx\Favorite\FavoriteListingFactoryInterface;
use \History_Event_Action_SavedListing;
use \History_Event_Delete_SavedListing;
use \History_User_Lead;
use \Backend_Mailer_SavedListing;
use \Backend_Agent;
use \Backend_Agent_Notifications;

/**
 * Class FavoriteDatastore
 * @package REW\Datastore\Listing
 */
class FavoriteDatastore
{

    /**
     * @var array
     */
    const FAVORITE_FIELDS = [
        'Address', 'AddressCity', 'AddressState', 'AddressSubdivision',
        'AddressZipCode', 'ListingImage', 'ListingMLS', 'ListingType',
        'ListingPrice', 'NumberOfBathrooms', 'NumberOfBedrooms', 'NumberOfSqFt'
    ];

    /**
     * @var DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var HooksInterface
     */
    protected $hooks;

    /**
     * @var IDXFactoryInterface
     */
    protected $idxFactory;

    /**
     * @var IDXInterface
     */
    protected $utilIdx;

    /**
     * @var FavoriteListingFactoryInterface
     */
    protected $favoriteListingsFactory;

    /**
     * SearchDatastore constructor.
     * @param \REW\Core\Interfaces\Factories\DBFactoryInterface $dbFactory
     * @param \REW\Core\Interfaces\SettingsInterface $settings
     * @param \REW\Core\Interfaces\HooksInterface $hooks
     * @param \REW\Core\Interfaces\Factories\IDXFactoryInterface $idxFactory
     * @param \REW\Core\Interfaces\Util\IDXInterface $utilIdx
     * @param FavoriteListingFactoryInterface $favoriteListingsFactory
     */
    public function __construct(
        DBFactoryInterface $dbFactory,
        SettingsInterface $settings,
        HooksInterface $hooks,
        IDXFactoryInterface $idxFactory,
        IDXInterface $utilIdx,
        FavoriteListingFactoryInterface $favoriteListingsFactory
    ) {
        $this->dbFactory = $dbFactory;
        $this->settings = $settings;
        $this->hooks = $hooks;
        $this->idxFactory = $idxFactory;
        $this->utilIdx = $utilIdx;
        $this->favoriteListingsFactory = $favoriteListingsFactory;
    }

    public function getFavorites(FavoriteRequestModel $favoriteRequest)
    {
        // Check Agent
        if (!$userData = $this->getUser($favoriteRequest->getUserId())) {
            throw new \InvalidArgumentException('The provided user id is not valid');
        }

        // Get User Listings
        $db = $this->dbFactory->get();
        $stmt = $db->prepare(sprintf(
            'SELECT * FROM `%s` WHERE `user_id` = ? AND `idx` = ?;',
            $this->settings['TABLES']['LM_USER_LISTINGS']
        ));
        $stmt->execute([$favoriteRequest->getUserId(), $favoriteRequest->getFeedName()]);
        $userListingsData  = $stmt->fetchAll();

        // Build User Listings
        $userListings = [];
        foreach ($userListingsData as $userListingData) {
            $userListings[] = $this->favoriteListingsFactory->createFromArray($userListingData);
        }
        return (new FavoriteListingsModel())->withFavoriteListings($userListings);
    }

    /**
     * Create Favorite
     * @param FavoriteRequestModel $favoriteRequest
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     * @return ListingResultModel
     */
    public function createFavorite(FavoriteRequestModel $favoriteRequest)
    {
        // Check Agent
        if (!$userData = $this->getUser($favoriteRequest->getUserId())) {
            throw new \InvalidArgumentException('The provided user id is not valid');
        }

        // Get Listing
        $listingFeed = $favoriteRequest->getFeedName();
        $db = $this->dbFactory->get('cms');
        $idxDb = $this->dbFactory->get($listingFeed);
        $idx = $this->idxFactory->getIdx($listingFeed);
        $stmt = $idxDb->prepare(sprintf("SELECT %s FROM `%s` `l` WHERE `l`.`%s` = ? AND `l`.`%s` = ? LIMIT 1",
            $idx->selectColumns('`l`.', self::FAVORITE_FIELDS),
            $idx->getTable(),
            $idx->field('ListingMLS'),
            $idx->field('ListingType')
        ));
        $stmt->execute([
            $favoriteRequest->getListingId(),
            $favoriteRequest->getListingType()
        ]);
        $listingData = $stmt->fetch();
        $listingData = $this->utilIdx->parseListing($listingData);

        // Already Favorited
        if ($listingResult = $this->getUserListing($favoriteRequest->getUserId(), $listingData['ListingMLS'])) {
            return $listingResult;
        }

        // Build Listing Result
        $listingResult = $this->favoriteListingsFactory->createFromArray([
            'user_id' => $favoriteRequest->getUserId(),
            'mls_number' => $listingData['ListingMLS'],
            'table' => $idx->getTable(),
            'idx' => $idx->getName(),
            'type' => $listingData['ListingType'],
            'city' => $listingData['AddressCity'],
            'subdivision' => $listingData['AddressSubdivision'],
            'bedrooms' => $listingData['NumberOfBedrooms'],
            'bathrooms' => $listingData['NumberOfBathrooms'],
            'sqft' => $listingData['NumberOfSqFt'],
            'price' => $listingData['ListingPrice'],
            'timestamp' => time(),
        ]);

        try {
            // Insert record into saved listings
            $insertUserListings = $db->prepare(sprintf(
                'INSERT INTO `%s` SET
                `user_id`    = :user_id,
                `mls_number` = :mls_number,
                `table`      = :table,
                `idx`        = :idx,
                `type`       = IFNULL(:type, `type`),
                `city`       = IFNULL(:city, `city`),
                `subdivision`= IFNULL(:subdivision, `subdivision`),
                `bedrooms`   = IFNULL(:bedrooms, `bedrooms`),
                `bathrooms`  = IFNULL(:bathrooms, `bathrooms`),
                `sqft`       = IFNULL(:sqft, `sqft`),
                `price`      = IFNULL(:price, `price`),
                `timestamp`  = :time;',
                $this->settings['TABLES']['LM_USER_LISTINGS']
            ));
            $insertUserListings->execute([
                'user_id'     => $listingResult->getUserId(),
                'mls_number'  => $listingResult->getMlsNumber(),
                'table'       => $listingResult->getTable(),
                'idx'         => $listingResult->getIdx(),
                'type'        => $listingResult->getType(),
                'city'        => $listingResult->getCity(),
                'subdivision' => $listingResult->getSubdivision(),
                'bedrooms'    => $listingResult->getBedrooms(),
                'bathrooms'   => $listingResult->getBathrooms(),
                'sqft'        => $listingResult->getSqft(),
                'price'       => $listingResult->getPrice(),
                'time'        => date("Y-m-d H:i:s", $listingResult->getTimestamp())
            ]);
            $userListingId = $db->lastInsertId();
            $listingResult = $listingResult->withId($userListingId);
        } catch (\PDOException $e) {
            throw new \UnexpectedValueException('Listing could not be added to favorites');
        }

        // Track history event
        (new History_Event_Action_SavedListing(
            ['listing' => $listingData],
            [new History_User_Lead($favoriteRequest->getUserId())]
        ))->save($db);

        // Notify agent on save
        if ($userData['notify_favs'] === 'yes') {
            // Load assigned agent
            //@todo Replace with agent store once it exists
            $agent = Backend_Agent::load($userData['agent']);

            // Send notification to assigned agent
            $mailer = new Backend_Mailer_SavedListing([
                'listing' => $listingData,
                'lead'  => $userData
            ]);

            // Check incoming notification settings
            if ($agent->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_LISTING_SAVED)) {
                $mailer->Send();
            }
        }

        // Run hook
        $this->hooks->hook(HooksInterface::HOOK_LEAD_LISTING_SAVED)->run($userData, $idx, $listingData);

        return $listingResult;
    }

    /**
     * @param FavoriteRequestModel $favoriteRequest
     * @return array
     */
    public function deleteFavorite(FavoriteRequestModel $favoriteRequest)
    {
        // Check Agent
        if (!$userData = $this->getUser($favoriteRequest->getUserId())) {
            throw new \InvalidArgumentException('The provided user id is not valid');
        }

        // Get Listing
        $listingFeed = $favoriteRequest->getFeedName();
        $db = $this->dbFactory->get('cms');
        $idxDb = $this->dbFactory->get($listingFeed);
        $idx = $this->idxFactory->getIdx($listingFeed);
        $stmt = $idxDb->prepare(sprintf("SELECT %s FROM `%s` `l` WHERE `l`.`%s` = ? AND `l`.`%s` = ? LIMIT 1",
            $idx->selectColumns('`l`.', self::FAVORITE_FIELDS),
            $idx->getTable(),
            $idx->field('ListingMLS'),
            $idx->field('ListingType')
        ));
        $stmt->execute([
            $favoriteRequest->getListingId(),
            $favoriteRequest->getListingType()
        ]);
        $listingData = $stmt->fetch();
        if (!$listingData) {
            throw new \InvalidArgumentException('The provided listing does not exist');
        }
        $listingData = $this->utilIdx->parseListing($listingData);

        // Not Favorited
        if (!$listingResult = $this->getUserListing($favoriteRequest->getUserId(), $favoriteRequest->getListingId())) {
            return ['removed' => true];
        }

        try {
            // Remove record from database
            $deleteUserListings = $db->prepare(sprintf(
                "DELETE FROM `%s` WHERE `id` = ?;",
                $this->settings['TABLES']['LM_USER_LISTINGS']
            ));
            $deleteUserListings->execute([$listingResult->getId()]);
        } catch (\PDOException $e) {
            throw new \UnexpectedValueException('Listing could not be removed from favorites');
        }

        // Track history event
        (new History_Event_Delete_SavedListing(
            ['listing' => $listingData],
            [new History_User_Lead($favoriteRequest->getUserId())]
        ))->save($db);

        // Trigger hook
        $this->hooks->hook(HooksInterface::HOOK_LEAD_LISTING_REMOVED)->run($userData, $listingData);

        return ['removed' => true];
    }

    /**
     * Get User Record
     * @param int $userId
     * @return array
     */
    protected function getUser($userId)
    {
        $db = $this->dbFactory->get();
        $stmt = $db->prepare(sprintf(
            'SELECT * FROM `%s` `u` WHERE `u`.`id` = ? LIMIT 1;',
            $this->settings['TABLES']['LM_LEADS']
        ));
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /**
     * Get User Listing
     * @param int $userId
     * @param string $listingId
     * @return ListingResultModel
     */
    protected function getUserListing($userId, $listingId)
    {
        $db = $this->dbFactory->get();
        $stmt = $db->prepare(sprintf(
            'SELECT * FROM `%s` WHERE `user_id` = ? AND `mls_number` = ? LIMIT 1;',
            $this->settings['TABLES']['LM_USER_LISTINGS']
        ));
        $stmt->execute([$userId, $listingId]);
        $userListing  = $stmt->fetch();
        if ($userListing) {
            return $this->favoriteListingsFactory->createFromArray($userListing);
        }
        return null;
    }
}
