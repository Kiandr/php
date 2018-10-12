<?php
namespace REW\Factory\Idx\Search;

use REW\Model\Idx\Search\DefaultOptions;

class DefaultOptionsFactory
{

    const FLD_CRITERIA = 'criteria';

    const FLD_PAGE_LIMIT = 'page_limit';

    const FLD_SORT_BY = 'sort_by';

    /**
     * @param array $data
     * @return \REW\Model\Idx\Search\DefaultOptionsInterface
     */
    public function createFromArray(array $data)
    {
        $defaultOptions = new DefaultOptions();

        // Init criteria
        $criteria = [];

        if (isset($data[self::FLD_CRITERIA])) {
            $criteria = unserialize($data[self::FLD_CRITERIA]);

            if (!is_array($criteria)) {
                $criteria = [];
            }
        }

        $defaultOptions = $defaultOptions
            ->withCriteria($criteria)
            ->withLimit((isset($data[self::FLD_PAGE_LIMIT]) ? $data[self::FLD_PAGE_LIMIT] : []))
            ->withSort((isset($data[self::FLD_SORT_BY]) ? $data[self::FLD_SORT_BY] : []))
            ->withMapDisplay((isset($criteria['map']['open']) ? $criteria['map']['open'] : '0'));

        return $defaultOptions;
    }
}
