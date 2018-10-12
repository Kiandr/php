<?php
namespace REW\Api\Controller\Idx;

use REW\Datastore\Listing\SearchPanelDatastoreInterface;
use REW\Factory\Idx\Search\PanelRequestFactoryInterface;

class PanelController
{

    /**
     * @var \REW\Datastore\Listing\SearchPanelDatastoreInterface
     */
    protected $searchPanelDatastore;

    /**
     * @var \REW\Factory\Idx\Search\PanelRequestFactoryInterface
     */
    protected $panelRequestFactory;

    /**
     * PanelController constructor.
     * @param \REW\Datastore\Listing\SearchPanelDatastoreInterface $searchPanelDatastore
     * @param \REW\Factory\Idx\Search\PanelRequestFactoryInterface $panelRequestFactory
     */
    public function __construct(
        SearchPanelDatastoreInterface $searchPanelDatastore,
        PanelRequestFactoryInterface $panelRequestFactory
    ) {
        $this->searchPanelDatastore = $searchPanelDatastore;
        $this->panelRequestFactory = $panelRequestFactory;
    }

    /**
     * @param array $params
     * @return \REW\Model\Idx\Search\PanelResultInterface[]
     * @throws \Exception
     */
    public function getIdxPanels(array $params)
    {
        $panelRequest = $this->panelRequestFactory->createFromArray($params);
        return $this->searchPanelDatastore->getPanels($panelRequest);
    }
}
