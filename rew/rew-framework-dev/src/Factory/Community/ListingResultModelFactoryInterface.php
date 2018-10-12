<?php
namespace REW\Factory\Community;

interface ListingResultModelFactoryInterface
{
    /**
     * @param array $data
     * @return self
     */
    public function createFromArray(array $data = []);
}
