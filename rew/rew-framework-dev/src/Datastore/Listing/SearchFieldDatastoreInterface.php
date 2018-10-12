<?php
namespace REW\Datastore\Listing;

use REW\Model\Idx\FeedInfoInterface;
use REW\Model\Idx\Search\FieldInterface;

interface SearchFieldDatastoreInterface
{
    /**
     * Accepts a FeedInfoInterface (having a minimum of a feedname in it) and returns a copy with all field info populated.
     * @param FeedInfoInterface $feedInfo
     * @return FeedInfoInterface
     * @throws \RuntimeException if $feedInfo doesn't have a feed yet.
     */
    public function getFieldsForFeed(FeedInfoInterface $feedInfo);

    /**
     * Takes in a list of fields and adds the permissible search operation and the database field.
     * @param FeedInfoInterface $feedInfo
     * @return FeedInfoInterface
     */
    public function getMissingFieldInfo(FeedInfoInterface $feedInfo);
}
