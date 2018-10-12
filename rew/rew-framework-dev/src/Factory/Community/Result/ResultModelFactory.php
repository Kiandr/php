<?php
namespace REW\Factory\Community\Result;

use REW\Factory\Community\ResultModelFactoryInterface;
use REW\Model\Community\Result\ResultModel;

class ResultModelFactory implements ResultModelFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $data)
    {
        // @todo: define columns to extract, and update the model with each datum
        $model = new ResultModel();

        // Get primary image
        if (isset($data['images'])) {
            $image = array_slice($data['images'], 0, 1);
        }
        if (!empty($image)) {
            $data['image'] = $image['0'];
        }

        $model = $model
            ->withTitle((isset($data['title']) ? $data['title'] : null))
            ->withSubTitle((isset($data['subtitle']) ? $data['subtitle'] : null))
            ->withDescription((isset($data['description']) ? $data['description'] : null))
            ->withUrl((isset($data['url']) ? $data['url'] : null))
            ->withImage((isset($data['image']) ? $data['image'] : null))
            ->withImages((isset($data['images'])) ? $data['images'] : null)
            ->withStatsHeading((isset($data['stats_heading']) ? $data['stats_heading'] : 'Real Estate Statistics'))
            ->withStatsTotal((!empty($data['stats_total']) ? $data['stats_total'] : 'Total Listings'))
            ->withStatsHighest((!empty($data['stats_highest']) ? $data['stats_highest'] : 'Highest Price'))
            ->withStatsAverage((!empty($data['stats_average']) ? $data['stats_average'] : 'Average Price'))
            ->withStatsLowest((!empty($data['stats_lowest']) ? $data['stats_lowest'] : 'Lowest Price'))
            ->withListings((isset($data['listings']) ? $data['listings'] : []))
            ->withIdxStats((isset($data['stats']) ? $data['stats'] : []))
            ->withTypeStats((isset($data['propTypeStats']) ? $data['propTypeStats'] : []))
            ->withSearchUrl((isset($data['search_url']) ? $data['search_url'] : null));

        return $model;
    }
}
