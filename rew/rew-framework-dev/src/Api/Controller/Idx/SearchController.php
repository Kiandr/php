<?php
namespace REW\Api\Controller\Idx;

use REW\Api\Exception\Request\BadRequestException;
use REW\Api\Validator\Idx\Feed\GetValidator;
use REW\Api\Validator\Idx\Feed\Save\PostValidator;
use REW\Api\Validator\Idx\Feed\Update\PostValidator as UpdatePostValidator;
use REW\Core\Interfaces\SettingsInterface;
use REW\Datastore\Listing\SearchFieldDatastoreInterface;
use REW\Datastore\Listing\SearchPanelDatastore;
use REW\Datastore\User\SearchDatastore as UserDatastore;
use REW\Factory\Idx\Search\FeedInfoFactoryInterface;
use REW\Factory\Idx\Search\ListingRequestFactory;
use REW\Factory\Idx\Search\LayerRequestFactory;
use REW\Datastore\Layers\LayerDatastore;
use REW\Datastore\Listing\SearchDatastore;
use REW\Datastore\Listing\SavedSearchDatastoreInterface;
use REW\Model\Idx\Search\ListingRequest;
use REW\Model\Idx\SavedSearch\Result\ResultInterface as SavedSearchResultModel;
use REW\Factory\Idx\SavedSearch\Request\RequestFactory as SavedSearchRequestFactory;
use REW\Model\User\Search\UserRequestModel;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\HooksInterface;
use \History_Event_Action_SavedSearch;
use \History_User_Lead;
use \Auth;
use \History_User_Agent;
use \IDX_SavedSearch_InstantEmail;
use \Backend_Agent;
use \Backend_Agent_Notifications;
use \Backend_Mailer_SavedSearch;

class SearchController
{
    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var IDXInterface
     */
    protected $idx;

    /**
     * @var HooksInterface
     */
    protected $hooks;

    /**
     * @var SearchDatastore
     */
    protected $searchDatastore;

    /**
     * @var LayerDatastore
     */
    protected $layerDatastore;

    /**
     * @var ListingRequestFactory
     */
    protected $listingRequestFactory;

    /**
     * @var LayerRequestFactory
     */
    protected $layerRequestFactory;

    /**
     * @var GetValidator
     */
    protected $validator;

    /**
     * @var PostValidator
     */
    protected $savedSearchValidator;

    /**
     * @var UpdatePostValidator
     */
    protected $updateSearchValidator;

    /**
     * @var FeedInfoFactoryInterface
     */
    protected $feedInfoFactory;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var IDX_SavedSearch_InstantEmail
     */
    protected $instantEmail;

    /**
     * @var SearchFieldDatastoreInterface
     */
    protected $searchFieldDatastore;

    /**
     * @var \REW\Datastore\Listing\SearchPanelDatastore
     */
    protected $searchPanelDatastore;

    /**
     * @var SavedSearchDatastoreInterface
     */
    protected $savedSearchDatastore;

    /**
     * @var \REW\Datastore\User\SearchDatastore
     */
    protected $userDatastore;

    /**
     * @var SavedSearchRequestFactory
     */
    protected $savedSearchRequestFactory;

    /**
     * SearchController constructor.
     * @param AuthInterface $auth
     * @param IDXInterface $idx
     * @param HooksInterface $hooks
     * @param SearchDatastore $searchDatastore
     * @param LayerDatastore $layerDatastore
     * @param ListingRequestFactory $listingRequestFactory
     * @param LayerRequestFactory $layerRequestFactory
     * @param GetValidator $validator
     * @param PostValidator $savedSearchValidator
     * @param UpdatePostValidator $updateSearchValidator
     * @param FeedInfoFactoryInterface $feedInfoFactory
     * @param SettingsInterface $settings
     * @param SearchFieldDatastoreInterface $searchFieldDatastore
     * @param SearchPanelDatastore $searchPanelDatastore
     * @param SavedSearchDatastoreInterface $savedSearchDatastore
     * @param SavedSearchRequestFactory $savedSearchRequestFactory
     * @param UserDatastore $userDatastore
     * @param IDX_SavedSearch_InstantEmail $instantEmail
     * @todo make an interface for SearchDatastore to implement for DI purposes
     */
    public function __construct(
        AuthInterface $auth,
        IDXInterface $idx,
        HooksInterface $hooks,
        SearchDatastore $searchDatastore,
        LayerDatastore $layerDatastore,
        ListingRequestFactory $listingRequestFactory,
        LayerRequestFactory $layerRequestFactory,
        GetValidator $validator,
        PostValidator $savedSearchValidator,
        UpdatePostValidator $updateSearchValidator,
        FeedInfoFactoryInterface $feedInfoFactory,
        SettingsInterface $settings,
        SearchFieldDatastoreInterface $searchFieldDatastore,
        SearchPanelDatastore $searchPanelDatastore,
        SavedSearchDatastoreInterface $savedSearchDatastore,
        SavedSearchRequestFactory $savedSearchRequestFactory,
        UserDatastore $userDatastore,
        IDX_SavedSearch_InstantEmail $instantEmail
    ) {
        $this->auth = $auth;
        $this->idx = $idx;
        $this->hooks = $hooks;
        $this->searchDatastore = $searchDatastore;
        $this->layerDatastore = $layerDatastore;
        $this->listingRequestFactory = $listingRequestFactory;
        $this->layerRequestFactory = $layerRequestFactory;
        $this->validator = $validator;
        $this->savedSearchValidator = $savedSearchValidator;
        $this->updateSearchValidator = $updateSearchValidator;
        $this->feedInfoFactory = $feedInfoFactory;
        $this->settings = $settings;
        $this->searchFieldDatastore = $searchFieldDatastore;
        $this->savedSearchDatastore = $savedSearchDatastore;
        $this->savedSearchRequestFactory = $savedSearchRequestFactory;
        $this->userDatastore = $userDatastore;
        $this->instantEmail = $instantEmail;
    }

    /**
     * @param array $params
     * @return \REW\Model\Idx\Search\ListingCountResult
     * @throws \Exception
     */
    public function getCount($params) {
        $listingRequest = $this->listingRequestFactory->createFromArray($params);
        return $this->searchDatastore->getCount($listingRequest);
    }

    /**
     * @param array $params
     * @return \REW\Model\Idx\Search\ListingResults
     * @throws \Exception
     */
    public function getListings($params)
    {
        $this->validator->validateFields($params);
        $listingRequest = $this->listingRequestFactory->createFromArray($params);
        return $this->searchDatastore->getListings($listingRequest);
    }

    /**
     * @param array $params
     * @return \REW\Model\Idx\Search\LayerResults
     * @throws \Exception
     */
    public function getLayers($params)
    {
        $this->validator->validateFields($params);
        $layersRequest = $this->layerRequestFactory->createFromArray($params);
        return $this->layerDatastore->getLayers($layersRequest);
    }

    /**
     * @param $params
     * @return \REW\Model\Idx\Search\CommunityResults
     * @return mixed
     */
    public function getCommunityInfo($params)
    {
        $layersRequest = $this->layerRequestFactory->createFromArray($params);
        return $this->layerDatastore->getCommunityInfo($layersRequest);
    }

    /**
     * @return \REW\Model\Idx\FeedInfoInterface[]
     */
    public function getFeedInfo()
    {
        // Get the feed(s) the site is configured to use.
        $config = $this->settings->getConfig();

        $configuredFeeds = [];

        if (isset($config['idx_feed'])) {
            $configuredFeeds[] = $config['idx_feed'];
        }

        if (isset($config['idx_feeds'])) {
            $configuredFeeds = array_unique(array_merge(array_keys($config['idx_feeds']), $configuredFeeds));
        }

        $allowedFeeds = $configuredFeeds;

        if (!empty($config['settings']['team'])) {
            $allowedFeeds = $config['settings']['team_idxs'] ?: [];
        }
        if (!empty($config['settings']['agent']) && $config['settings']['agent'] > 1) {
            $allowedFeeds = $config['settings']['agent_idxs'] ?: [];
        }

        $configuredFeeds = array_intersect($configuredFeeds, $allowedFeeds);

        $feedInfoModels = [];
        foreach ($configuredFeeds as $configuredFeed) {
            $feedInfo = $this->feedInfoFactory->create($configuredFeed, $config['idx_feeds'][$configuredFeed]['title']);
            $feedInfoModels[$feedInfo->getName()] = $this->searchFieldDatastore->getFieldsForFeed($feedInfo);
        }

        return $feedInfoModels;
    }

    /**
     * @param array $params
     * @return \REW\Model\Idx\SavedSearch\Result\ResultInterface
     */
    public function saveSearch($params) {
        // Validate Valid Backnend Agent
        if (!empty($params['lead_id'])) {
            $params['userId'] = $params['lead_id'];
            if (!$this->auth->isValid() || (!$this->auth->isAgent() && !$this->auth->isAssociate())) {
                throw new BadRequestException('You must be logged in to update a save search.');
            }

            // Get User Hook
            $userRequest = (new UserRequestModel())->withId($params['userId']);
            $user = $this->userDatastore->getUserFromId($userRequest);

        // Validate Owner
        } else {
            $user = $this->getUser($params['user']);
            $params['userId'] = $user->getId();
        }

        $this->savedSearchValidator->validateFields($params);
        $params['agentId'] = $this->auth->get()->info('id');
        $savedSearchRequest = $this->savedSearchRequestFactory->createFromArray($params);
        $searchModel = $this->savedSearchDatastore->saveSearch($savedSearchRequest);

        // Map field names to legacy history event
        $searchData = [];
        $searchData['title'] = $searchModel->getTitle();
        $searchData['user_id'] = $searchModel->getUserId();
        $searchData['agent_id'] = $searchModel->getAgentId();
        $searchData['idx'] = $searchModel->getFeed();
        $searchData['criteria'] = $searchModel->getCriteria();

        $historyUsers[] = new History_User_Lead($searchData['user_id']);
        if ($searchData['agent_id']) {
            $historyUsers[] = new History_User_Agent($searchData['agent_id']);
        }

        // Log Event: Lead Saved Search
        // TODO: Acquire $backend_user
        $event = new History_Event_Action_SavedSearch(
            [
                'search' => $searchData
            ],
            $historyUsers
        );

        $event->save();

        if ($params['immediate'] == 'true') {
            $this->immediateSearch($searchModel);
        }

        // Run hook
        $this->hooks->hook(HooksInterface::HOOK_LEAD_SEARCH_SAVED)->run(
            $user->toArray(),
            $this->idx,
            unserialize($searchModel->getCriteria()),
            $searchModel->getTitle(),
            $searchModel->getFrequency(),
            false
        );

        // Send Notification to Assigned Agent (If Saved by Lead)
        if (empty($params['lead_id']) && $user->getNotifySearches()) {
            // Setup Mailer
            $mailer = new Backend_Mailer_SavedSearch([
                'lead' => $user->toArray(),
                'search' => [
                    'criteria' => $searchModel->getCriteria(),
                    'title' => $searchModel->getTitle(),
                    'frequency' => $searchModel->getFrequency(),
                    'idx' => $searchModel->getFeed()
                ]
            ]);

            // Check Incoming Notification Settings for Assigned Agent
            $agent = Backend_Agent::load($user->getAgent());
            $check = $agent->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_SEARCH_SAVED);

            // Send Email
            if (!empty($check)) {
                $mailer->Send();
            }
        }

        return $searchModel;
    }

    /**
     * @param array $params
     * @return \REW\Model\Idx\SavedSearch\Result\ResultInterface
     */
    public function updateSearch($params)
    {
        // Validate Search exists
        $search = $this->savedSearchDatastore->getSearchById($params['id']);
        if (!$search) {
            throw new BadRequestException('Saved search could not be found.');
        }

        // Validate Valid Backnend Agent
        if (!empty($params['lead_id'])) {
            $params['userId'] = $params['lead_id'];
            if (!$this->auth->isValid() || (!$this->auth->isAgent() && !$this->auth->isAssociate())) {
                throw new BadRequestException('You must be logged in to update a save search.');
            }
        // Validate Owner
        } else {
            $params['userId'] = $this->getUser($params['user'])->getId();
        }

        // Valid User Search
        if ($search->getUserId() != $params['userId']) {
            throw new BadRequestException('The listing to be updated does not belong to the user provided.');
        }

        // Validate Required Parameters
        $this->updateSearchValidator->validateFields($params);

        $updateSearchRequest = $this->savedSearchRequestFactory->createFromArray($params);
        $searchModel = $this->savedSearchDatastore->updateSearch($updateSearchRequest);

        if ($params['immediate'] == 'true') {
            $this->immediateSearch($searchModel);
        }

        return $searchModel;
    }

    /**
     * Send Immediate Image
     * @param SavedSearchResultModel $searchModel
     */
    protected function immediateSearch(SavedSearchResultModel $searchModel)
    {
        // Send listings email to lead
        if ($this->auth->isValid()) {
            // Saved search created by agent
            if ($this->auth->isAgent()) {
                $this->instantEmail->setData($searchModel->getId(), $this->auth->info('id'));
            }
        } else {
            // Saved search created by lead
            $this->instantEmail->setData($searchModel->getId());
        }
        $this->instantEmail->sendEmail();
    }

    /**
     * Get User from Token
     * @param string $userToken
     * @throws BadRequestException If no user token is supplied
     * @throws BadRequestException If user token is unknown
     * @return \REW\Model\User\Search\UserResultModel
     */
    protected function getUser($userToken)
    {
        if (empty($userToken)) {
            throw new BadRequestException('A valid user token is required to manage favorites.');
        }
        $userRequest = (new UserRequestModel())->withToken(trim($userToken));
        $user = $this->userDatastore->getUserFromToken($userRequest);
        if (empty($user)) {
            throw new BadRequestException('The provided user token does not match any valid users.');
        }
        return $user;
    }
}
