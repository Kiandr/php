<?php
namespace REW\Model\Idx\Search\Panel;

use REW\Model\Idx\Search\PanelResultInterface;

class PanelResult implements PanelResultInterface
{
    /**
     * @var \IDX_Panel
     */
    protected $panel;

    /**
     * @param \IDX_Panel $panel
     * @return \REW\Model\Idx\Search\Panel\PanelResult
     */
    public function withPanel(\IDX_Panel $panel)
    {
        $clone = clone $this;
        $clone->panel = $panel;
        return $clone;
    }

    /**
     * @return \IDX_Panel
     */
    public function getPanel()
    {
        return $this->panel;
    }

    /**
     * @return \IDX_Panel[]|mixed
     */
    public function jsonSerialize()
    {
        return $this->panel;
    }
}
