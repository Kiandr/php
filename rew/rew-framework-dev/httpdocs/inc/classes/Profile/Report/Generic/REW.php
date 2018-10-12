<?php

/**
 * Profile_Report_Generic_Bootstrap
 * Extends the generic report to use REW-specific markup & styles
 *
 */
class Profile_Report_Generic_REW extends Profile_Report_Generic
{

    /**
     * Assigned classes for stopwatch entries
     * @var array
     */
    protected static $_watch_classes = array();

    /**
     * Assigned alert icons for stopwatch entries
     * @var array
    */
    protected static $_watch_alerts = array();

    /**
     * Nesting levels which have had an error stopwatch row marked
     * @var array
    */
    protected static $_error_class_levels = array();

    /**
     * Nesting levels which have had an error stopwatch row marked
     * @var array
    */
    protected static $_error_alert_levels = array();

    /**
     * Assigned classes for memory snapshot entries
     * @var array
    */
    protected static $_snapshot_classes = array();

    /**
     * Table row class for errors
     * @var string
    */
    protected $_class_row_error = 'error';

    /**
     * Table row class for warnings
     * @var string
     */
    protected $_class_row_warning = 'warning';

    /**
     * Table row class for success
     * @var string
     */
    protected $_class_row_success = 'success';

    /**
     * Flag memory snapshot report rows as errors for entries
     * that use this much percent of the application's max allowed memory
     * @var float
     */
    protected $_percent_of_app_memory_error = 40;

    /**
     * Flag memory snapshot report rows as warnings for entries
     * that use this much percent of the application's max allowed memory
     * @var float
     */
    protected $_percent_of_app_memory_warning = 30;

    /**
     * Flag memory snapshot report rows as successful for entries
     * that reclaim this much percent of the application's max allowed memory
     * @var float
     */
    protected $_percent_of_app_memory_success = 30;

    /**
     * Get the report's markup
     * @return string
     */
    public function getHTML()
    {
        ob_start();
        ?>
        <div id="profile-report-controls" draggable="true" title="You can Drag &amp; Drop this" style="position: fixed; top: 0; right: 0; padding: 10px; z-index: 9999; display: none;">
            <a class="btn positive" id="profile-report-show" href="javascript:void(0);"><?=$this->microsecondsToString($this->_total_duration); ?></a>
        </div>
        <div id="profile-report" class="window closed" style="position: absolute; display: none; max-width: 90%;">
            <header>
                <h4 class="title">Profiler</h4>
                <div class="btnset"><a class="close btn"><span class="icon-remove"></span></a></div>
            </header>
            <div class="pane">
                <?=parent::getHTML();?>
            </div>
        </div>

        <script>

            <?= $this->getUIJavascript(); ?>

        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Gets drag and drop JS
     * @return string
     */
    protected function getUIJavascript()
    {
        ob_start();
        ?>
        (function () {
            var $report = $('#profile-report'),
                $showButton = $('#profile-report-show'),
                $closeButton = $report.find('header').find('.btn.close'),
                posCookie = 'profile-report-pos';

            $report.Window({
                width : '90%',
                open : false,
                noClose : true
            });

            $showButton.on('click', function () {
                $report.show().Window('open');
                return false;
            });

            $closeButton.on('click', function () {
                $report.Window('hide');
                return false;
            });

            var BREW_Profiler = {
                'drag_start' : function(event) {
                    rect = event.target.getBoundingClientRect();
                    event.dataTransfer.setData("text/plain",
                    (parseInt(rect.left,10) - event.clientX) + ',' + (parseInt(rect.top,10) - event.clientY));
                },
                'drag_over' : function(event) {
                    event.preventDefault();
                    return false;
                },
                'drag_id' : 'profile-report-controls',
                'obj' : null,
                'get_obj' : function() {
                    if (BREW_Profiler.obj === null || typeof BREW_Profiler.obj !== 'object') {
                        BREW_Profiler.obj = document.getElementById(BREW_Profiler.drag_id);
                    }
                    return BREW_Profiler.obj;
                },
                'set_pos': function (top, left) {
                    var pctrl = BREW_Profiler.get_obj();
                    pctrl.style.right = null;
                    pctrl.style.left = left + 'px';
                    pctrl.style.top = top + 'px';
                },
                'drop' : function(event) {
                    var offset = event.dataTransfer.getData("text/plain").split(',');
                    var left = event.clientX + parseInt(offset[0],10);
                    var top = event.clientY + parseInt(offset[1],10);
                    BREW.Cookie(posCookie, [top, left].join(';'));
                    BREW_Profiler.set_pos(top, left);
                    event.preventDefault();
                    return false;
                },
                'init' : function(drag_id) {
                    if (drag_id && typeof drag_id === 'string' && 0 === drag_id.length) {
                        BREW_Profiler.drag_id = drag_id;
                    }
                    var pctrl = BREW_Profiler.get_obj();
                    pctrl.addEventListener('dragstart', BREW_Profiler.drag_start, false);
                    document.body.addEventListener('dragover', BREW_Profiler.drag_over, false);
                    document.body.addEventListener('drop', BREW_Profiler.drop, false);

                    // Restore button position
                    var posCookieVal = BREW.Cookie(posCookie);
                    if (posCookieVal) {
                        var btnPos = posCookieVal.split(';');
                        var btnPosTop = btnPos[0];
                        var btnPosLeft = btnPos[1];
                        if (btnPosTop && btnPosLeft) {
                            BREW_Profiler.set_pos(btnPosTop, btnPosLeft);
                        }
                    }
                    pctrl.style.display = 'block';

                }
            };
            BREW_Profiler.init();

        })();
        <?php
        return ob_get_clean();
    }

    /**
     * Get HTML to display as the whole report's title
     * @param float $total_app_duration The total amount of time spent running the application (in seconds)
     * @return string
     */
    protected function markupForReportTitle($total_app_duration = null)
    {
        ob_start();
        ?>
        <h2>
            Profile Report
            <?php if (!empty($total_app_duration)) { ?>
                <small><?=$this->microsecondsToString($total_app_duration);?></small>
            <?php } ?>
        </h2>
        <?php
        return ob_get_clean();
    }

    /**
     * Get HTML to display as the stopwatch report's title
     * @param float $total_watches_duration Total amount of time tracked by stopwatches (in seconds)
     * @return string
     */
    protected function markupForStopwatchReportTitle($total_watches_duration)
    {
        ob_start();
        ?>
        <h3>
            Timers
            <small><?=$this->microsecondsToString($total_watches_duration);?></small>
        </h3>
        <?php
        return ob_get_clean();
    }

    /**
     * Get HTML to display as the memory snapshot report's title
     * @param int $snapshot_max_bytes Highest memory usage encountered within all snapshots (in bytes)
     * @param int $application_max_bytes Highest memory usage encountered by the whole application, not just snapshots (in bytes)
     * @param array $chart_points Array of usage percentages (of the maximum application allowed) for all snapshots
     * @param int $application_max_bytes_allowed Highest amount of memory (in bytes) that the application is allowed to use
     * @return string 
     */
    protected function markupForMemorySnapshotReportTitle($snapshot_max_bytes, $application_max_bytes, $chart_points = array(), $application_max_bytes_allowed = null)
    {
        ob_start();
        ?>
        <h3>
            Memory
            <small>
                <?=$this->bytesToString($snapshot_max_bytes);?>
                <?php if (!empty($application_max_bytes_allowed)) { ?>
                    / <?=$this->bytesToString($application_max_bytes_allowed);?>
                <?php } ?>
            </small>
        </h3>
        <?php
        return ob_get_clean();
    }

    /**
     * Get HTML to display as the Maximum Usage value in the memory snapshot report's summary
     * @param float $percentage
     * @return string
     */
    protected function markupForMemorySnapshotReportSummaryMaximumUsageValue($percentage)
    {
        ob_start();
        ?>
        <?php if ($percentage > 90) { ?>
            <span class="profile-label label-danger"><?=$percentage;?>%</span>
        <?php } else if ($percentage > 75) { ?>
            <span class="profile-label label-warning"><?=$percentage;?>%</span>
        <?php } else { ?>
            <span class="profile-label label-success"><?=$percentage;?>%</span>
        <?php } ?>
        <?php
        return ob_get_clean();
    }

    /**
     * Get the class name for a stopwatch report's row
     * @param Profile_Timer_Stopwatch $watch
     * @param int $level Current nesting level
     * @param array $child_errors
     * @param array $child_warnings
     * @param float $percent_of_siblings Duration percentage for the stopwatch compared to its siblings
     * @param float $percent_of_app_total Duration percentage for the stopwatch compared to the app total
     * @return string|NULL
     */
    protected function classForStopwatchReportRow(Profile_Timer_Stopwatch $watch, $level, &$child_errors, &$child_warnings, $percent_of_siblings, $percent_of_app_total = null)
    {

        // Check cache
        if (isset(self::$_watch_classes[$watch->getUID()])) {
            return self::$_watch_classes[$watch->getUID()];
        }

        // Class name
        $class_name = '';

        // Percent of total
        if (!empty($percent_of_app_total) && (empty($child_errors) && empty($child_warnings))) {
            $error_marked = isset(self::$_error_class_levels[$level]);
            if ($percent_of_app_total > $this->_percent_of_app_time_error) {
                $class = !$error_marked ? $this->_class_row_error : $this->_class_row_warning;
                $class_name = $class;
            } else if ($percent_of_app_total > $this->_percent_of_app_time_warning) {
                $class_name = $this->_class_row_warning;
            }
        }

        if ($class_name === $this->_class_row_error) {
            self::$_error_class_levels[$level] = true;
        }

        // Cache class
        self::$_watch_classes[$watch->getUID()] = $class_name;
        return $class_name;
    }

    /**
     * Get the value to display for a stopwatch entry's column identifier
     * @param unknown $column_id Column identifier
     * @param Profile_Memory_Snapshot $snapshot
     * @param int $change Difference in usage compared to the previous snapshot (in bytes)
     * @param int $application_max_bytes_allowed Highest amount of memory (in bytes) that the application is allowed to use
     * @param float $percent_of_app_max Snapshot usage percentage of the application's maximum allowed usage
     * @return string
     */
    protected function valueForMemorySnapshotReportColumn($column_id, Profile_Memory_Snapshot $snapshot, $change, $application_max_bytes_allowed = null, $percent_of_app_max = null)
    {

        // Check column
        switch ($column_id) {
            case 'change':
                // Class name
                $class_name = null;

                // Check cache
                if (isset(self::$_snapshot_classes[$snapshot->getUID()])) {
                    $class_name = self::$_snapshot_classes[$snapshot->getUID()];
                }

                // Decide on class
                if (empty($class_name)) {
                    // Memory freed
                    if ($change < 0) {
                        if (!empty($application_max_bytes_allowed)) {
                            $change_abs = abs($change);
                            $total_percent = number_format(($change_abs / $application_max_bytes_allowed) * 100, 2);
                            if ($total_percent > $this->_percent_of_app_memory_success) {
                                $class_name = 'label-success';
                            }
                        }
                    }

                    // Percentage of total usage
                    if (!empty($percent_of_app_max)) {
                        static $problem_flagged;
                        $problem_flagged = is_null($problem_flagged) ? false : $problem_flagged;
                        if (!$problem_flagged) {
                            if ($percent_of_app_max > $this->_percent_of_app_memory_error) {
                                $class_name = 'label-danger';
                                $problem_flagged = true;
                            } else if ($percent_of_app_max > $this->_percent_of_app_memory_warning) {
                                $class_name = 'label-warning';
                                $problem_flagged = true;
                            }
                        }
                    }

                    // Cache class
                    self::$_snapshot_classes[$snapshot->getUID()] = $class_name;
                }

                $value = parent::valueForMemorySnapshotReportColumn($column_id, $snapshot, $change, $application_max_bytes_allowed, $percent_of_app_max);
                return !empty($class_name) ? '<span class="profile-label ' . $class_name . '">' . $value . '</span>' : $value;
            default:
                return parent::valueForMemorySnapshotReportColumn($column_id, $snapshot, $change, $application_max_bytes_allowed, $percent_of_app_max);
        }
    }

    /**
     * Get the inline style for a report row's left accessory anchor
     * @return string
     */
    protected function styleForReportRowLeftAccessoryAnchor()
    {
        return 'cursor: pointer; text-decoration: none; color:black; outline: none; font-weight: bold;';
    }

    /**
     * Get the inline style for a given stopwatch value
     * @param string $column_id
     * @param int $level
     */
    protected function styleForStopwatchReportValue($column_id, $level)
    {
        switch ($column_id) {
            case 'duration':
                return 'text-align: right; font-weight: bold;';
        }
        return parent::styleForStopwatchReportValue($column_id, $level);
    }

    /**
     * Get the value to display for a stopwatch entry's Alert column
     * @param string $column_id
     * @param Profile_Timer_Stopwatch $watch
     * @param int $level Current nesting level
     * @param array $child_errors
     * @param array $child_warnings
     * @param float $percent_of_siblings Duration percentage for the stopwatch compared to its siblings
     * @param float $percent_of_app_total Duration percentage for the stopwatch compared to the app total
     * @return string
     */
    protected function valueForStopwatchReportColumn($column_id, Profile_Timer_Stopwatch $watch, $level, &$child_errors, &$child_warnings, $percent_of_siblings, $percent_of_app_total = null)
    {
        switch ($column_id) {
            case 'alert':
                // Check cache
                $uid =  $watch->getUID();
                if (isset(self::$_watch_alerts[$uid])) {
                    return self::$_watch_alerts[$uid];
                }

                // Icon & colors
                $alert_icon = '';

                // Problematic children
                if (!empty($child_errors) || !empty($child_warnings)) {
                    if (!empty($child_errors)) {
                        $alert_icon = '<span class="profile-dot label-danger"></span>';
                    } else {
                        $alert_icon = '<span class="profile-dot label-warning"></span>';
                    }
                }

                // Percent of total
                if (!empty($percent_of_app_total) && (empty($child_errors) && empty($child_warnings))) {
                    $error_marked = isset(self::$_error_alert_levels[$level]);
                    if ($percent_of_app_total > $this->_percent_of_app_time_error) {
                        $label_class = $error_marked ? 'label-warning' : 'label-danger';

                        self::$_error_alert_levels[$level] = true;
                        $alert_icon = '<span class="profile-dot ' . $label_class . '"></span> ';
                    } else if ($percent_of_app_total > $this->_percent_of_app_time_warning) {
                        $alert_icon = '<span class="profile-dot label-warning"></span> ';
                    }
                }

                // Cache icon
                self::$_watch_alerts[$uid] = $alert_icon;
                return $alert_icon;
            default:
                return parent::valueForStopwatchReportColumn($column_id, $watch, $level, $child_errors, $child_warnings, $percent_of_siblings, $percent_of_app_total);
        }
    }

    /**
     * Get style information to include for the whole report
     * @return string|NULL
     */
    protected function styleForReport()
    {
        ob_start();
        ?>
        <?=parent::styleForReport();?>
        <style>
            .animate-height-wrap {
                transition: height <?=$this->animationDuration() / 1000;?>s;
                -webkit-transition: height <?=$this->animationDuration() / 1000;?>s;
            }

            .profile-component-hide {
                display: none;
            }

            #profile-report h2 {
                font-size: 30px;
            }

                #profile-report h2 small {
                    font-size: 18px;
                }

            #profile-report h3 {
                font-size: 24px;
            }

                #profile-report h3 small {
                    font-size: 14px;
                }

            #profile-report h2 small,
            #profile-report h3 small {
                font-weight: normal;
                color: #999999;
            }

            #profile-report h2,
            #profile-report h3 {
                color: #333333;
            }

            #profile-report table tr.error td {
                background-color: #F2DEDE;
                border-color: #EED3D7;
            }

            #profile-report table tr.warning td {
                background-color: #FCF8E3;
                border-color: #FBEED5;
            }

            #profile-report table tr.success td {
                background-color: #DFF0D8;
                border-color: #D6E9C6;
            }

            .profile-label {
                border-radius: 0.25em;
                color: #FFFFFF;
                display: inline;
                line-height: 1;
                padding: 0.2em 0.6em 0.3em;
                text-align: center;
                vertical-align: baseline;
                white-space: nowrap;
            }

            .profile-dot {
                border-radius: 50%;
                color: #FFFFFF;
                display: inline-block;
                line-height: 1;
                text-align: center;
                vertical-align: baseline;
                white-space: nowrap;
                width: 14px;
                height: 14px;
            }

            .label-success {
                background-color: #5CB85C;
            }

            .label-danger {
                background-color: #D9534F;
            }

            .label-warning {
                background-color: #F0AD4E;
            }
        </style>
        <?php
        return ob_get_clean();
    }
}
