<?php
namespace REW\Datastore\Listing;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Factory\Idx\Search\PanelResultFactoryInterface;
use REW\Model\Idx\Search\PanelRequestInterface;

class SearchPanelDatastore implements SearchPanelDatastoreInterface
{
    /**
     * @var \REW\Core\Interfaces\Factories\DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var \REW\Factory\Idx\Search\PanelResultFactoryInterface
     */
    protected $panelResultFactory;

    public function __construct(
        DBFactoryInterface $dbFactory,
        PanelResultFactoryInterface $panelResultFactory
    ) {
        $this->dbFactory = $dbFactory;
        $this->panelResultFactory = $panelResultFactory;
    }

    /**
     * @param \REW\Model\Idx\Search\PanelRequestInterface $panelRequest
     * @return \REW\Model\Idx\Search\PanelResultInterface[]
     * @throws \Exception
     */
    public function getPanels(PanelRequestInterface $panelRequest)
    {
        $db = $this->dbFactory->get();

        $feed = $panelRequest->getFeed() ?: '';

        // Reset IDX Defaults
        $defaults = $db->prepare("SELECT `panels` FROM `rewidx_defaults` WHERE `idx` = ? LIMIT 1;");
        $defaults->execute([$feed]);
        $results = $defaults->fetch();

        if (empty($results)) {
            $defaults->execute(['']);
            $results = $defaults->fetch();
        }

        $panels = unserialize($results['panels']) ?: [];
        $panelModels = [];

        if (!empty($panels)) {
            foreach ($panels as $id => &$panel) {
                $panelModel = $this->panelResultFactory->createFromIdxPanel(\IDX_Panel::get($id, [
                    'hidden' => !empty($panel['hidden']),
                    'display' => !empty($panel['display']),
                    'collapsed' => !empty($panel['closed'])
                ]));

                if (!empty($panelModel)) {
                    $panelModels[$id] = $panelModel;
                }
            }
        }

        return $panelModels;
    }
}
