<?php

namespace REW\Backend\Dashboard\EventListener\FormEvents;

use REW\Backend\Dashboard\EventListener\AbstractFormEventListener;
use REW\Backend\Dashboard\EventFactory\FormEvents\InquiryEventFactory;

/**
 * Class InquiryEventListener
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class InquiryEventListener extends AbstractFormEventListener
{

    /**
     * Inquiry Mode
     * @var string
     */
    const MODE = 'inquiry';

    /**
     * Showing/Selling Forms
     * @var array
     */
    const SHOWING_OR_SELLING_FORMS = ['Property Showing', 'Quick Showing', 'Seller Form', 'CMA Form', 'Radio Seller Form', 'Guaranteed Sold Form'];

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
     * @return InquiryEventFactory
     */
    public function getFactory()
    {
        return $this->container->get(InquiryEventFactory::class);
    }

    /**
     * Get Form Query Strings
     * @return string
     */
    protected function getFormTypeQuery()
    {
        return "(`uf`.`form` NOT IN (" . implode(', ', array_fill(0, count(self::SHOWING_OR_SELLING_FORMS), '?')) . ")"
            . " AND (`uf`.`form` != 'IDX Inquiry'"
            . " OR (`uf`.`data` NOT LIKE '%s:12:\"inquire_type\";s:16:\"Property Showing\";%' AND `uf`.`data` NOT LIKE '%s:12:\"inquire_type\";s:7:\"Selling\";%')))";
    }

    /**
     * Get Form Query Paramaters
     * @return array
     */
    protected function getFormTypeParams()
    {
        return self::SHOWING_OR_SELLING_FORMS;
    }
}
