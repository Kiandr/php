<?php
namespace REW\Factory\Idx\Search\Panel;

use REW\Factory\Idx\Search\PanelRequestFactoryInterface;
use REW\Model\Idx\Search\Panel\PanelRequest;

class PanelRequestFactory implements PanelRequestFactoryInterface
{
    /**
     * @param array $data
     * @return \REW\Model\Idx\Search\PanelRequestInterface
     */
    public function createFromArray(array $data)
    {
        $panelRequest = new PanelRequest();
        $panelRequest = $panelRequest->withFeed(isset($data['feed']) ? $data['feed'] : null);
        return $panelRequest;
    }
}
