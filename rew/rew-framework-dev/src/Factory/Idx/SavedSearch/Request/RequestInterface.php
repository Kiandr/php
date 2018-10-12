<?php
namespace REW\Factory\Idx\SavedSearch\Request;

interface RequestInterface
{
    /**
     * @param array $params
     * @return \REW\Model\Idx\SavedSearch\Request\RequestInterface
     */
    public function createFromArray(array $params);
}
