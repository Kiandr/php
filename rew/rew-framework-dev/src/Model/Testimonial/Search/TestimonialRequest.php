<?php
namespace REW\Model\Testimonial\Search;

use \InvalidArgumentException;

class TestimonialRequest
{

    /**
     * @var array
     */
    const DEFAULT_FIELDS = [
        'testimonial',
        'client'
    ];

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @param array $fields
     * @return self
     */
    public function withFields(array $fields)
    {
        $clone = clone $this;
        $clone->fields = $fields;
        return $clone;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields ?: static::DEFAULT_FIELDS;
    }

    /**
     * @param int $limit
     * @return self
     * @throw InvalidArgumentException if $limit isn't an int
     */
    public function withLimit($limit)
    {
        if (!is_int($limit)) {
            throw new InvalidArgumentException(
                sprintf('$limit must be an integer, %s given!', getType($limit))
            );
        }
        $clone = clone $this;
        $clone->limit = $limit;
        return $clone;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
