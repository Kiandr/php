<?php
namespace REW\Datastore\Community;

use REW\Model\Community\RequestModelInterface;

interface SearchDatastoreInterface
{
    /**
     * @param RequestModelInterface $communityRequest
     * @return \REW\Model\Community\ResultModelInterface[]
     */
    public function getCommunities(RequestModelInterface $communityRequest);
}
