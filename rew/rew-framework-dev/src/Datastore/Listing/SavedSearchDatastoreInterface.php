<?php
namespace REW\Datastore\Listing;

use REW\Model\Idx\SavedSearch\Request\RequestInterface as SavedSearchRequestModel;
use REW\Model\Idx\SavedSearch\Result\ResultInterface as SavedSearchResultModel;

interface SavedSearchDatastoreInterface
{
    /**
     * @param SavedSearchRequestModel $searchRequest
     * @return SavedSearchResultModel
     */
    public function saveSearch(SavedSearchRequestModel $searchRequest);

    /**
     * @param SavedSearchRequestModel $searchRequest
     * @return SavedSearchResultModel
     */
    public function updateSearch(SavedSearchRequestModel $searchRequest);

    /**
     * @param SavedSearchRequestModel $searchRequest
     * @return SavedSearchResultModel[]
     */
    public function getSearches(SavedSearchRequestModel $searchRequest);
}
