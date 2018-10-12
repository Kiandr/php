<?php

namespace REW\Theme\Enterprise\Module\SinglePropertyWebsite;

use REW\Core\Interfaces\InstallableInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Core\Website\RouteInformation;

/**
 * @package REW\Theme\Enterprise\Module\SinglePropertyWebsite
 */
class ModuleController implements InstallableInterface
{

    /**
     * @var HooksInterface
     */
    protected $hooks;

    /**
     * @var HooksInterface
     */
    protected $routeInformation;

    /**
     * @param HooksInterface $hooks
     * @param RouteInformation $routeInformation
     */
    public function __construct(HooksInterface $hooks, RouteInformation $routeInformation)
    {
        $this->hooks = $hooks;
        $this->routeInformation = $routeInformation;
    }

    /**
     * {@inheritDoc}
     */
    public function install()
    {

        // Single property website URL
        if ($this->routeInformation->isFrontend()) {
            $this->hooks->on(
                HooksInterface::HOOK_IDX_POST_PARSE_LISTING,
                [$this, 'idxPostParseListingHook'],
                10
            );
        }
    }

    /**
     * @param array $listing
     * @param IDXInterface $idx
     * @return array
     */
    public function idxPostParseListingHook(array $listing, IDXInterface $idx)
    {
        $enhanced = $listing['enhanced'];
        $website = $enhanced['website'];
        if (!empty($website['enabled'])) {
            // Force popup for ?spw
            if (isset($_GET['spw'])) {
                $_GET['popup'] = true;
            }
        }
        return $listing;
    }
}
