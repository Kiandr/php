<?php
namespace REW\Model\Community;

interface RequestModelInterface
{
    /**
     * Immutable setter to specify the mode of the request.
     * @param mixed $mode
     * @return self
     */
    public function withMode($mode);

    /**
     * Returns the mode of the request.
     * @return mixed
     */
    public function getMode();

    /**
     * Immutable setter to specify what IDs we want excluded from the result set.
     * @param array $ids
     * @return self
     */
    public function withIdsExcluded(array $ids);

    /**
     * Returns the IDs that we want excluded from the result set.
     * @return array
     */
    public function getIdsExcluded();
}
