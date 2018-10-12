<?php
namespace REW\Model\Idx\FeedInfo;

use REW\Model\Idx\FeedInfoInterface;
use REW\Model\Idx\Search\FieldInterface;

class FeedInfo implements FeedInfoInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var FieldInterface[]
     */
    protected $fields = [];

    /**
     * @var string
     */
    protected $title;

    /**
     * @param string $name
     * @return self
     */
    public function withName($name)
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param FieldInterface[] $fields
     * @return self
     */
    public function withFields(array $fields)
    {
        $clone = clone $this;
        $clone->fields = $fields;
        return $clone;
    }

    /**
     * @param FieldInterface $field
     * @return self
     */
    public function withAdditionalSearchField(FieldInterface $field)
    {
        $clone = clone $this;
        $clone->fields[$field->getFormFieldName()] = $field;
        return $clone;
    }

    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param string $title
     * @return self
     */
    public function withTitle($title)
    {
        $clone = clone $this;
        $clone->title = $title;
        return $clone;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function jsonSerialize()
    {
        return [
            FeedInfoInterface::FLD_TITLE => $this->title,
            FeedInfoInterface::FLD_FIELDS => $this->fields
        ];
    }
}
