<?php
namespace REW\Factory\Idx\Search\Panel;

use REW\Core\Interfaces\LogInterface;
use REW\Factory\Idx\Search\PanelResultFactoryInterface;
use REW\Model\Idx\Search\Panel\PanelResult;

class PanelResultFactory implements PanelResultFactoryInterface
{
    /**
     * @var \REW\Core\Interfaces\LogInterface
     */
    protected $logger;

    public function __construct(LogInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \IDX_Panel $data
     * @return null|\REW\Model\Idx\Search\Panel\PanelResult
     */
    public function createFromIdxPanel(\IDX_Panel $data)
    {
        if (!$data instanceof \JsonSerializable) {
            $this->logger->log(LogInterface::ERROR,
                sprintf('A panel (%s) on %s doesn\'t implement JsonSerializable!', $data->getId(), `hostname -f`));
            return null;
        }

        // Don't return panels that aren't meant to be used.
        if ($data->isBlocked()) {
            return null;
        }

        $panelResult = new PanelResult();
        $panelResult = $panelResult->withPanel($data);
        return $panelResult;

    }
}
