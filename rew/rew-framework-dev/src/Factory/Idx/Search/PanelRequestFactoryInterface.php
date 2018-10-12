<?php
namespace REW\Factory\Idx\Search;

interface PanelRequestFactoryInterface
{
    /**
     * @param array $data
     * @return \REW\Model\Idx\Search\PanelRequestInterface
     */
    public function createFromArray(array $data);
}
