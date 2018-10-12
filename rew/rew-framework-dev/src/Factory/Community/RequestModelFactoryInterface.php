<?php
namespace REW\Factory\Community;

interface RequestModelFactoryInterface
{
    const ARRAY_FLD_MODE = 'mode';

    const ARRAY_EXCLUDED_IDS = 'excluded_ids';

    /**
     * @param array $data
     * @return \REW\Model\Community\RequestModelInterface
     */
    public function createFromArray(array $data = []);
}
