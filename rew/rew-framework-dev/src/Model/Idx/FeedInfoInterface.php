<?php
namespace REW\Model\Idx;

use REW\Model\Idx\Search\FieldInterface;

interface FeedInfoInterface extends \JsonSerializable
{
    /** @var string */
    const FLD_NAME = 'name';

    /** @var string */
    const FLD_FIELDS = 'fields';

    /** @var string */
    const FLD_TITLE = 'title';

    /**
     * Immutable setter for the name of the feed.
     * @param string $feedName
     * @return self
     */
    public function withName($name);

    /**
     * Returns the feed name.
     * @return string
     */
    public function getName();

    /**
     * Immutable setter for the list of fields the feed supports.
     * @param FieldInterface[] $fields
     * @return self
     */
    public function withFields(array $fields);

    /**
     * Returns a cloned copy of self, adding the field specified.
     * @param FieldInterface $field
     * @return self
     */
    public function withAdditionalSearchField(FieldInterface $field);

    /**
     * Returns all of the fields that a feed has.
     * @return FieldInterface[]
     */
    public function getFields();

    /**
     * Returns a JSON representation of this model.
     * @return mixed
     */
    public function jsonSerialize();
}
