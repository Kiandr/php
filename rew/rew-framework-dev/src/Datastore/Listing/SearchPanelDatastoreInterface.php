<?php
namespace REW\Datastore\Listing;

use REW\Model\Idx\Search\PanelRequestInterface;

interface SearchPanelDatastoreInterface
{
    /**
     * @param PanelRequestInterface $panelRequest
     * @return \REW\Model\Idx\Search\PanelResultInterface[]
     * @throws \Exception
     */
    public function getPanels(PanelRequestInterface $panelRequest);
}
