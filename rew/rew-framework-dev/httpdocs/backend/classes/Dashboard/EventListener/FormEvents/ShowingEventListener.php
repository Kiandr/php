<?php

namespace REW\Backend\Dashboard\EventListener\FormEvents;

use REW\Backend\Dashboard\EventListener\AbstractFormEventListener;
use REW\Backend\Dashboard\EventFactory\FormEvents\ShowingEventFactory;

/**
 * Class ShowingEventListener
 *
 * @category Dashboard
 * @package  REW\Backend\Dashboard
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class ShowingEventListener extends AbstractFormEventListener
{

    /**
     * Showing Mode
     * @var string
     */
    const MODE = 'showing';

    /**
     * Showing Forms
     * @var array
     */
    const SHOWING_FORMS = ['Property Showing', 'Quick Showing'];

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
     * @return ShowingEventFactory
     */
    public function getFactory()
    {
        return $this->container->get(ShowingEventFactory::class);
    }

    /**
     * Get Form Query Strings
     * @return string
     */
    protected function getFormTypeQuery()
    {
        return "(`uf`.`form` IN (" . implode(', ', array_fill(0, count(self::SHOWING_FORMS), '?')) . ")"
            . " OR (`uf`.`form` = 'IDX Inquiry'"
            . " AND `uf`.`data` LIKE '%s:12:\"inquire_type\";s:16:\"Property Showing\";%'))";
    }

    /**
     * Get Form Query Paramaters
     * @return array
     */
    protected function getFormTypeParams()
    {
        return self::SHOWING_FORMS;
    }
}
