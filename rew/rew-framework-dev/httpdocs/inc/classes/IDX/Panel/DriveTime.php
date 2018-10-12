<?php

use REW\Backend\Partner\Inrix\DriveTime;

/**
 * Inrix Drive Time - search fields
 * @package IDX_Panel
 */
class IDX_Panel_DriveTime extends IDX_Panel
{

    const DEFAULT_FORM_VALUES = [
        'direction' => 'D',
        'arrival_time' => '08:15',
        'travel_duration' => 30
    ];

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Drive Time';

    /**
     * Panel Class
     * @var string
     */
    protected $panelClass = 'drivetime';

    /**
     * Hide Visibility Config
     * @var bool
     */
    protected $hide_visibility_toggle = true;

    /**
     * Set panel's CSS classes
     * @param string $panelClass
     */
    public function setPanelClass($panelClass)
    {
        $classes = explode(' ', $panelClass);
        foreach ($classes as $key => $class) {
            if (stripos($class, '-width-') !== false) {
                unset($classes[$key]);
            }
        }
        $classes[] = '-width-1/1';
        $classes = implode(' ', $classes);
        $this->panelClass = $classes;
    }

    /**
     * Build Panel Content Markup
     *
     * @return string
     */
    public function getMarkup()
    {
        $dt_defaults = [
            'direction' => (!empty($_REQUEST['dt_direction']) ? $_REQUEST['dt_direction'] : self::DEFAULT_FORM_VALUES['direction']),
            'arrival_time' => (!empty($_REQUEST['dt_arrival_time']) ? $_REQUEST['dt_arrival_time'] : self::DEFAULT_FORM_VALUES['arrival_time']),
            'travel_duration' => ((!empty($_REQUEST['dt_travel_duration'])) ? (int) $_REQUEST['dt_travel_duration'] : self::DEFAULT_FORM_VALUES['travel_duration']),
        ];

        ob_start();
        ?>
<div class="columns">
    <div class="column -width-3/5 -width-1/2@md -width-1/1@sm -flex drivetime__to-from">
        <div class="field -flex">
            <label class="-pad-right-sm"><input type="radio" name="dt_direction" value="A" <?=('A' === $dt_defaults['direction'] ? ' checked' : ''); ?>> To</label>
            <label><input type="radio" name="dt_direction" value="D" <?=('D' === $dt_defaults['direction'] ? ' checked' : ''); ?>> From</label>
        </div>
        <div class="field drivetime__location">
            <input placeholder="Enter a location" name="dt_address" class="drivetime-ac-search" value="<?=!empty($_REQUEST['dt_address']) ? $_REQUEST['dt_address'] : ''; ?>">
            <span class="drivetime-ac-search-tooltip hidden"><span class="dt_caret"></span>You must select from the drop-down list.</span>
        </div>
    </div>
    <div class="drivetime__arriving column -width-2/5 -width-1/2@md -width-1/1@sm">
        <div class="field -mar-right-sm">
            <label class="-mar-right-sm">Arriving at</label>
            <select name="dt_arrival_time">
                <?php
                foreach (DriveTime::getArrivalTimeOptions() as $option) {
                    echo sprintf(
                        '<option value="%s"%s>%s</option>',
                        $option['value'],
                        ($option['value'] === $dt_defaults['arrival_time'] ? ' selected' : ''),
                        $option['display']
                    );
                }
                ?>
            </select>
        </div>
        <div class="field">
            <label class="-mar-right-sm">in</label>
            <select name="dt_travel_duration">
                <?php
                foreach (DriveTime::getTravelDurationOptions() as $option) {
                    echo sprintf(
                        '<option value="%s"%s>%s</option>',
                        $option['value'],
                        ($option['value'] === $dt_defaults['travel_duration'] ? ' selected' : ''),
                        $option['display']
                    );
                }
                ?>
            </select>
        </div>
    </div>
    <input type="hidden" name="place_zip" value="<?=!empty($_REQUEST['place_zip']) ? $_REQUEST['place_zip'] : ''; ?>">
    <input type="hidden" name="place_lat" value="<?=!empty($_REQUEST['place_lat']) ? $_REQUEST['place_lat'] : ''; ?>">
    <input type="hidden" name="place_lng" value="<?=!empty($_REQUEST['place_lng']) ? $_REQUEST['place_lng'] : ''; ?>">
    <input type="hidden" name="place_zoom" value="<?=!empty($_REQUEST['place_zoom']) ? $_REQUEST['place_zoom'] : ''; ?>">
    <input type="hidden" name="place_adr" value="<?=!empty($_REQUEST['place_adr']) ? $_REQUEST['place_adr'] : ''; ?>">
</div>
        <?php
        return ob_get_clean();
    }
}
