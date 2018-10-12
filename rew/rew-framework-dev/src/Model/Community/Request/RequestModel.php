<?php
namespace REW\Model\Community\Request;

use REW\Model\Community\RequestModelInterface;

class RequestModel implements RequestModelInterface
{
    /**
     * @var mixed
     */
    protected $mode;

    /**
     * @var array
     */
    protected $idsExcluded;

    /**
     * @param mixed $mode
     * @return self
     */
    public function withMode($mode)
    {
        $clone = clone $this;
        $clone->mode = $mode;
        return $clone;
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * {@inheritdoc}
     */
    public function withIdsExcluded(array $idsExcluded)
    {
        $clone = clone $this;
        $clone->idsExcluded = $idsExcluded;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdsExcluded()
    {
        return $this->idsExcluded;
    }
}
