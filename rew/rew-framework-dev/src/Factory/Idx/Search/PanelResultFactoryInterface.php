<?php
namespace REW\Factory\Idx\Search;

interface PanelResultFactoryInterface
{
    /**
     * Create an instance of PanelResult from a traditional IDX_Panel.
     * @param \IDX_Panel $data
     * @return null|\REW\Model\Idx\Search\Panel\PanelResult
     */
    public function createFromIdxPanel(\IDX_Panel $data);
}
