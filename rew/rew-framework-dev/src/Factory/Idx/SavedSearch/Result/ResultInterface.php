<?php
namespace REW\Factory\Idx\SavedSearch\Result;

interface ResultInterface
{
    /**
     * @param array $data
     * @return \REW\Model\Idx\SavedSearch\Result\ResultInterface
     */
    public function createFromArray(array $data);
}
