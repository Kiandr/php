<?php

namespace REW\Backend\Dashboard\EventListener\FormEvents;

use REW\Backend\Dashboard\EventListener\AbstractFormEventListener;
use REW\Backend\Dashboard\EventFactory\FormEvents\SellingEventFactory;
use \Util_Curl;

/**
 * Class SellingEventListener
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class SellingEventListener extends AbstractFormEventListener
{

    /**
     * Selling Mode
     * @var string
     */
    const MODE = 'selling';

    /**
     * Selling Forms
     * @var array
     */
    const SELLING_FORMS = ['Seller Form', 'CMA Form', 'Radio Seller Form', 'Guaranteed Sold Form'];

    /**
     * Get Event Mode
     * @return string
     */
    public function getMode()
    {
        return self::MODE;
    }

    /**
     * Get Event Factory
     * @return SellingEventFactory
     */
    public function getFactory()
    {
        return $this->container->get(SellingEventFactory::class);
    }

    /**
     * Get Form Query Strings
     * @return string
     */
    protected function getFormTypeQuery()
    {
        return "(`uf`.`form` IN (" . implode(', ', array_fill(0, count(self::SELLING_FORMS), '?')) . ")"
            . " OR (`uf`.`form` = 'IDX Inquiry'"
            . " AND `uf`.`data` LIKE '%s:12:\"inquire_type\";s:7:\"Selling\";%'))";
    }

    /**
     * Get Form Query Paramaters
     * @return array
     */
    protected function getFormTypeParams()
    {
        return self::SELLING_FORMS;
    }
}
