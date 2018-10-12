<?php
namespace REW\Factory\Community;

interface ResultModelFactoryInterface
{
    /**
     * @param array $data
     * @return \REW\Model\Community\ResultModelInterface
     */
    public function createFromArray(array $data);
}
