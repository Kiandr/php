<?php
namespace REW\Factory\Idx\SavedSearch\Result;

use \REW\Model\Idx\SavedSearch\Result\ResultInterface as SavedSearchResultModelInterface;
use REW\Model\Idx\SavedSearch\Result\ResultModel;

class ResultFactory implements ResultInterface
{
    const FLD_ID = 'id';

    const FLD_USER_ID = 'user_id';

    const FLD_AGENT_ID = 'agent_id';

    const FLD_TITLE = 'title';

    const FLD_CRITERIA = 'criteria';

    const FLD_FEED = 'idx';

    const FLD_FREQUENCY = 'frequency';

    /**
     * @param array $data
     * @return SavedSearchResultModelInterface
     */
    public function createFromArray(array $data)
    {
        $searchesResultModel = new ResultModel();

        $searchesResultModel = $searchesResultModel
            ->withId((!empty($data[self::FLD_ID]) ? $data[self::FLD_ID] : null))
            ->withCriteria((!empty($data[self::FLD_CRITERIA]) ? $data[self::FLD_CRITERIA] : null))
            ->withFeed((!empty($data[self::FLD_FEED]) ? $data[self::FLD_FEED] : null))
            ->withFrequency((!empty($data[self::FLD_FREQUENCY]) ? $data[self::FLD_FREQUENCY] : null))
            ->withTitle((!empty($data[self::FLD_TITLE]) ? $data[self::FLD_TITLE] : null))
            ->withUserId((!empty($data[self::FLD_USER_ID]) ? $data[self::FLD_USER_ID] : null))
            ->withAgentId((!empty($data[self::FLD_AGENT_ID]) ? $data[self::FLD_AGENT_ID] : null));

        return $searchesResultModel;
    }
}
