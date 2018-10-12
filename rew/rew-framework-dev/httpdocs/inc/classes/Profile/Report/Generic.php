<?php

/**
 * Profile_Report_Generic
 * Renders a profile report using generic markup
 * Can be subclassed to customize many aspects of the output
 *
 */
class Profile_Report_Generic extends Profile_Report
{

    /**
     * Flag stopwatch report rows as errors for entries
     * that take this much percent of the application's total duration
     * @var float
     */
    protected $_percent_of_app_time_error = 30;

    /**
     * Flag stopwatch report rows as warnings for entries
     * that take this much percent of the application's total duration
     * @var float
     */
    protected $_percent_of_app_time_warning = 20;

    /**
     * Get the report's markup
     * @return string
     */
    public function getHTML()
    {

        // Stopwatches
        $watches = $this->getOrderedStopwatches();

        // Memory snapshots
        $snapshots = $this->getOrderedMemorySnapshots();

        // No data?
        if (empty($watches) && empty($snapshots)) {
            return $this->markupForUnavailableReport();
        }

        // Reports markup
        $report_stopwatches = $this->markupForStopwatches($watches);
        $report_memory_snapshots = $this->markupForMemorySnapshots($snapshots);

        // Start buffer
        ob_start();
        ?>
        <?=$this->styleForReport();?>
        <?=$this->scriptForReport();?>
        <?=$this->markupForReportTitle($this->_total_duration);?>
        <?=$report_stopwatches;?>
        <?=$report_memory_snapshots;?>
        <?=$this->markupForReportFooter();?>
        <?php
        return ob_get_clean();
    }

    /**
     * Get the left padding amount for a given nesting level
     * @param int $level
     * @return int
     */
    protected function paddingForNestingLevel($level)
    {
        return 30 * $level;
    }

    /**
     * Animation duration in milliseconds
     * @return int
     */
    protected function animationDuration()
    {
        return 200;
    }

    /**
     * Get HTML to display as the whole report's title
     * @param float $total_app_duration Total amount of time spent running the application (in seconds)
     * @return string
     */
    protected function markupForReportTitle($total_app_duration = null)
    {
        return '<h2>Profile Report</h2>';
    }

    /**
     * Get HTML to display at the bottom of the report
     * @return string
     */
    protected function markupForReportFooter()
    {
        return '';
    }

    /**
     * Get HTML to display on the left side of a collapsed parent's Name
     * @return string
     */
    protected function markupForReportRowLeftAccessoryCollapsed()
    {
        return '+';
    }

    /**
     * Get HTML to display on the left side of an expanded parent's Name
     * @return string
     */
    protected function markupForReportRowLeftAccessoryExpanded()
    {
        return '-';
    }

    /**
     * Get HTML to display on the right side of a collapsed parent's Name
     * @return string
     */
    protected function markupForReportRowRightAccessoryCollapsed()
    {
        return '<small>Show Details</small>';
    }

    /**
     * Get HTML to display on the right side of an expanded parent's Name
     * @return string
     */
    protected function markupForReportRowRightAccessoryExpanded()
    {
        return '<small>Hide Details</small>';
    }

    /**
     * Get HTML to display on a collapsed stack trace
     * @return string
     */
    protected function markupForReportRowStackCollapsed()
    {
        return '[+]';
    }

    /**
     * Get HTML to display on an expanded stack trace
     * @return string
     */
    protected function markupForReportRowStackExpanded()
    {
        return '[-]';
    }

    /**
     * Get HTML to display the timed stopwatches
     * @param array $watches Collection of Profile_Timer_Stopwatch objects
     * @return string
     */
    protected function markupForStopwatches($watches)
    {
        if (empty($watches)) {
            return '';
        }

        // Stopwatch stats
        $watches_total = 0;
        foreach ($watches as $watch) {
            $watches_total += $watch->getElapsedTime();
        }

        // Report columns
        $columns = $this->columnsForStopwatchReport();
        $num_watches = count($watches);

        // Start buffer
        ob_start();
        ?>
        <?=$this->scriptForStopwatchReport();?>
        <?=$this->markupForStopwatchReportTitle($watches_total);?>
        <table <?=$this->attributeWithValue('class', $this->classForStopwatchReportTable());?> <?=$this->attributeWithValue('style', $this->styleForStopwatchReportTable());?>>
            <thead>
                <tr>
                    <?php foreach ($columns as $id => $column_name) { ?>
                        <th <?=$this->attributeWithValue('style', $this->styleForStopwatchReportColumn($id));?>>
                            <?=htmlspecialchars($column_name);?>
                        </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($watches as $k => $watch) { ?>
                    <?=$this->prepareAndRenderRowForStopwatchReport($watch, $watches, $k, $num_watches);?>
                <?php } ?>
                <?php if ($this->shouldDisplayStopwatchReportSummary()) { ?>
                    <?=$this->markupForStopwatchReportSummary($watches_total, $this->_total_duration);?>
                <?php } ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean();
    }

    /**
     * Recursively check duration of a stopwatch's children and collect children of interest
     * @param Profile_Timer_Stopwatch $watch
     * @param float $total_app_duration Total amount of time spent running the application (in seconds)
     * @param array $errors
     * @param array $warnings
     * @param boolean $error_marked
     */
    protected function diagnoseStopwatchChildren(Profile_Timer_Stopwatch $watch, $total_app_duration, &$errors = array(), &$warnings = array(), $error_marked = false)
    {
        if (empty($total_app_duration)) {
            return;
        }

        // Check children
        if ($children = $watch->getChildren()) {
            foreach ($children as $child) {
                $duration = $child->getElapsedTime();
                $total_percent = number_format(($duration / $total_app_duration) * 100, 2);
                if ($total_percent > $this->_percent_of_app_time_error && !$error_marked) {
                    $errors[] = $child;
                    $error_marked = true;
                } else if ($total_percent > $this->_percent_of_app_time_warning) {
                    $warnings[] = $child;
                }

                // Check children of child
                $this->diagnoseStopwatchChildren($child, $total_app_duration, $errors, $warnings, $error_marked);
            }
        }
    }

    /**
     * Perform calculations for a given stopwatch row and get its markup
     * @param Profile_Timer_Stopwatch $watch
     * @param array $root_watches Collection of top-level Profile_Timer_Stopwatch objects that don't have a parent
     * @param int $idx Index of row within its siblings
     * @param int $count Number of siblings the row has
     * @param int $level Current nesting level
     */
    protected function prepareAndRenderRowForStopwatchReport(Profile_Timer_Stopwatch $watch, $root_watches, $idx, $count, $level = 0)
    {

        // Check stopwatch children for troublemakers
        $child_errors = array();
        $child_warnings = array();

        // Require children
        if ($children = $watch->getChildren()) {
            $this->diagnoseStopwatchChildren($watch, $this->_total_duration, $child_errors, $child_warnings);
        }

        // Siblings duration
        $watch_siblings_total = 0;
        if ($watch->getParent()) {
            foreach ($watch->getParent()->getChildren() as $sibling) {
                $watch_siblings_total += $sibling->getElapsedTime();
            }
        } else {
            foreach ($root_watches as $sibling) {
                $watch_siblings_total += $sibling->getElapsedTime();
            }
        }

        // Calculate duration percentage compared to siblings or parent
        $duration = $watch->getElapsedTime();
        if ($watch->getParent()) {
            $percent_of_siblings = number_format(($watch->getElapsedTime() / $watch->getParent()->getElapsedTime()) * 100, 2);
        } else {
            if (!empty($this->_total_duration)) {
                $percent_of_siblings = number_format(($watch->getElapsedTime() / $this->_total_duration) * 100, 2);
            } else {
                $percent_of_siblings = number_format(($watch->getElapsedTime() / $watch_siblings_total) * 100, 2);
            }
        }

        // Calculate duration percentage compared to app total
        $percent_of_app_total = null;
        if (!empty($this->_total_duration)) {
            $percent_of_app_total = number_format(($duration / $this->_total_duration) * 100, 2);
        }

        // Configure entry
        $entry = array();
        $columns = $this->columnsForStopwatchReport();
        foreach ($columns as $id => $column_name) {
            $entry[$id] = $this->valueForStopwatchReportColumn($id, $watch, $level, $child_errors, $child_warnings, $percent_of_siblings, $percent_of_app_total);
        }

        // Render
        return $this->markupForStopwatchReportRow($watch, $entry, $root_watches, $level, $child_errors, $child_warnings, $idx, $count, $percent_of_siblings, $percent_of_app_total);
    }

    /**
     * Get HTML to display for a prepared stopwatch report row
     * @param Profile_Timer_Stopwatch $watch
     * @param array $entry Prepared row entry data
     * @param array $root_watches Collection of top-level Profile_Timer_Stopwatch objects that don't have a parent
     * @param int $level
     * @param array $child_errors
     * @param array $child_warnings
     * @param int $idx Index of row within its siblings
     * @param int $count Number of siblings the row has
     * @param float $percent_of_siblings
     * @param float|NULL $percent_of_app_total
     * @return string
     */
    protected function markupForStopwatchReportRow(Profile_Timer_Stopwatch $watch, $entry, $root_watches, $level, &$child_errors, &$child_warnings, $idx, $count, $percent_of_siblings, $percent_of_app_total = null)
    {

        // Row columns
        $columns = $this->columnsForStopwatchReport();

        // Row class
        $row_class = $this->classForStopwatchReportRow($watch, $level, $child_errors, $child_warnings, $percent_of_siblings, $percent_of_app_total);

        // Expanded details
        $expanded = $this->valueForStopwatchReportExpandedDetails($watch, $level, $child_errors, $child_warnings, $percent_of_siblings, $percent_of_app_total);

        // Accessory
        $accessory_left_expanded = $this->markupForReportRowLeftAccessoryExpanded();
        $accessory_left_collapsed = $this->markupForReportRowLeftAccessoryCollapsed();
        $accessory_right_expanded = $this->markupForReportRowRightAccessoryExpanded();
        $accessory_right_collapsed = $this->markupForReportRowRightAccessoryCollapsed();

        // Row children
        $children = $watch->getChildren();

        // Untracked time
        $untracked = array();

        // Last row?
        if ($idx == ($count - 1)) {
            $watch_siblings_total = 0;
            $remaining_untracked = 0;
            $parent_total = 0;
            if ($parent = $watch->getParent()) {
                foreach ($parent->getChildren() as $sibling) {
                    $watch_siblings_total += $sibling->getElapsedTime();
                }
                $remaining_untracked = $parent->getElapsedTime() - $watch_siblings_total;
                $parent_total = $parent->getElapsedTime();
            } else if (!empty($this->_total_duration)) {
                foreach ($root_watches as $sibling) {
                    $watch_siblings_total += $sibling->getElapsedTime();
                }
                $remaining_untracked = $this->_total_duration - $watch_siblings_total;
                $parent_total = $this->_total_duration;
            }

            // Set untracked data
            if (!empty($remaining_untracked)) {
                // Generate hint
                if (!empty($parent)) {
                    $hint = 'Additional time spent during \'' . $parent->getName() . '\', but not tracked by any of its child stopwatches';
                } else {
                    $hint = 'Additional time spent during application execution, which wasn\'t tracked by any stopwatches';
                }

                // Set row data
                $untracked = array(
                    'name'      => '<span title="' . htmlspecialchars($hint) . '">Not Timed</span>',
                    'portion'   => number_format(($remaining_untracked / $parent_total) * 100, 2) . '%',
                    'duration'  => $this->microsecondsToString($remaining_untracked),
                );
            }
        }

        // Start buffer
        ob_start();
        ?>
        <tr
            class="entry-child closed <?=$watch->getParent() ? $this->classForHiddenProfileComponent() : '';?> <?=$row_class;?>" data-child-level="<?=$level;?>"
            data-accessory-left-expanded='<?=htmlspecialchars(json_encode(array('html' => $accessory_left_expanded)));?>'
            data-accessory-left-collapsed='<?=htmlspecialchars(json_encode(array('html' => $accessory_left_collapsed)));?>'
            data-accessory-right-expanded='<?=htmlspecialchars(json_encode(array('html' => $accessory_right_expanded)));?>'
            data-accessory-right-collapsed='<?=htmlspecialchars(json_encode(array('html' => $accessory_right_collapsed)));?>'
        >
            <?php foreach ($columns as $id => $column_name) { ?>
                <td <?=$this->attributeWithValue('style', $this->styleForStopwatchReportValue($id, $level));?>>
                    <?=$entry[$id];?>
                </td>
            <?php } ?>
        </tr>
        <?php if (!empty($expanded)) { ?>
            <tr class="entry-details <?=$this->classForHiddenProfileComponent();?> <?=!empty($row_class) ? $row_class : '';?>">
                <td style="border-top: 0;"></td>
                <td style="border-top: 0; padding-right: 0;" colspan="<?=count($columns) - 1;?>">
                    <div style="padding-left: <?=$this->paddingForNestingLevel($level);?>px;">
                        <?=$expanded;?>
                    </div>
                </td>
            </tr>
        <?php } ?>
        <?php if (!empty($children)) { ?>
            <?php $num_children = count($children); ?>
            <?php foreach ($children as $k => $child) { ?>
                <?=$this->prepareAndRenderRowForStopwatchReport($child, $root_watches, $k, $num_children, $level + 1);?>
            <?php } ?>
        <?php } ?>
        <?php if (!empty($untracked)) { ?>
            <tr class="entry-child closed <?=$watch->getParent() ? $this->classForHiddenProfileComponent() : '';?>" data-child-level="<?=$level;?>" style="color: #A9A9A9;">
                <?php foreach ($columns as $id => $column_name) { ?>
                    <td <?=$this->attributeWithValue('style', $this->styleForStopwatchReportValue($id, $level));?>>
                        <?php if (isset($untracked[$id])) { ?>
                            <?=$untracked[$id];?>
                        <?php } else { ?>

                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
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
        return '<h3>Stopwatches</h3>';
    }

    /**
     * Get HTML to display after all rows in the stopwatch report's table body
     * @param float $total_watches_duration Total amount of time tracked by stopwatches (in seconds)
     * @param float $total_app_duration Total amount of time spent running the application (in seconds)
     * @return string
     */
    protected function markupForStopwatchReportSummary($total_watches_duration, $total_app_duration = null)
    {

        // Column count
        $column_count = count($this->columnsForStopwatchReport());

        // Start buffer
        ob_start();
        ?>
        <tr>
            <td colspan="<?=$column_count - 1;?>" style="text-align: right;">
                <strong>Stopwatch Total:</strong>
            </td>
            <td style="text-align: right;">
                <strong><?=$this->microsecondsToString($total_watches_duration);?></strong>
            </td>
        </tr>
        <?php if (!empty($total_app_duration)) { ?>
            <tr>
                <td colspan="<?=$column_count - 1;?>" style="text-align: right;">
                    <strong>Application Total:</strong>
                </td>
                <td style="text-align: right;">
                    <strong><?=$this->microsecondsToString($total_app_duration);?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="<?=$column_count - 1;?>" style="text-align: right;">
                    <strong>Coverage:</strong>
                </td>
                <td style="text-align: right;">
                    <strong>~<?=number_format(($total_watches_duration / $total_app_duration) * 100, 2);?>%</strong>
                </td>
            </tr>
        <?php } ?>
        <?php
        return ob_get_clean();
    }

    /**
     * Get the column names for the stopwatch report
     * @return array
     */
    protected function columnsForStopwatchReport()
    {
        return array(
            'alert'         => '', // Alert column
            'name'          => 'Name',
            'time'          => (!empty($this->_start_time) ? 'Start' : 'Timestamp'),
            'iterations'    => 'Iterations',
            'portion'       => 'Portion',
            'duration'      => 'Duration',
        );
    }

    /**
     * Get the class name to apply to elements in order to hide them
     * @return string
     */
    protected function classForHiddenProfileComponent()
    {
        return 'profile-component-hide';
    }

    /**
     * Get the class name for the stack trace table
     * @return NULL
     */
    protected function classForStackTraceTable()
    {
        return 'stack-trace';
    }

    /**
     * Get the class name for the stopwatch report's table
     * @return string|NULL
     */
    protected function classForStopwatchReportTable()
    {
        return null;
    }

    /**
     * Get the class name for a stopwatch entry's iterations table
     * @return string|NULL
     */
    protected function classForStopwatchReportIterationsTable()
    {
        return null;
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
        return null;
    }

    /**
     * Get the value to display for a stopwatch entry's column identifier
     * @param string $column_id Column identifier
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

        // Check column
        switch ($column_id) {
            case 'alert':
                return '';
            case 'name':
                // Expanded details
                $expanded = $this->valueForStopwatchReportExpandedDetails($watch, $level, $child_errors, $child_warnings, $percent_of_siblings, $percent_of_app_total);

                // Accessory
                $accessory_left_collapsed = $this->markupForReportRowLeftAccessoryCollapsed();
                $accessory_right_collapsed = $this->markupForReportRowRightAccessoryCollapsed();

                // Start buffer
                ob_start();
                ?>
                <?php if ($watch->getChildren()) { ?>
                    <a class="toggle-children" <?=$this->attributeWithValue('style', $this->styleForReportRowLeftAccessoryAnchor());?>href="javascript:void(0);" onclick="javascript:toggleChildren(this, <?=$level;?>);">
                        <span class="accessory-left"><?=$accessory_left_collapsed;?></span>
                        <?=$watch->getName();?>
                    </a>
                <?php } else { ?>
                    <?=$watch->getName();?>
                <?php } ?>
                <?php if (!empty($expanded)) { ?>
                    <?php if ($watch->getIterationCount() > 1 || $watch->getDetails()) { ?>
                        <a class="toggle-details" <?=$this->attributeWithValue('style', $this->styleForReportRowRightAccessoryAnchor());?> href="javascript:void(0);" onclick="javascript:toggleDetails(this);">
                            <span class="accessory-right"><?=$accessory_right_collapsed;?></span>
                        </a>
                    <?php } else { ?>
                        <a href="javascript:void(0);" style="text-decoration: none;" onclick='toggleDetails(this); this.innerHTML = (this.innerHTML === <?=json_encode($this->markupForReportRowStackCollapsed()); ?> ? <?=json_encode($this->markupForReportRowStackExpanded()); ?> : <?=json_encode($this->markupForReportRowStackCollapsed()); ?>);'><?=$this->markupForReportRowStackCollapsed(); ?></a>
                    <?php } ?>
                <?php } ?>
                <?php
                return ob_get_clean();

            case 'time':
                // Relative Start time
                if (!empty($this->_start_time)) {
                    $start = $watch->getInitialStartTime() - $this->_start_time;
                    if ($start < 1) {
                        return number_format($start * 1000, 2) . ' ms';
                    } else {
                        return number_format($start, 2) . ' sec';
                    }
                }

                // Timestamp
                return date('H:i:s', ceil($watch->getInitialStartTime()));

            case 'iterations':
                return number_format($watch->getIterationCount());
            case 'portion':
                if (!empty($percent_of_app_total)) {
                    return '<span title="' . $percent_of_app_total . '% of Application Total">' . $percent_of_siblings . '%</span>';
                } else {
                    return $percent_of_siblings . '%';
                }
            case 'duration':
                return $this->microsecondsToString($watch->getElapsedTime());
        }

        // Unrecognized column
        return null;
    }

    /**
     * Get the HTML to display for a stopwatch report entry's expanded details row
     * @param Profile_Timer_Stopwatch $watch
     * @param int $level Current nesting level
     * @param array $child_errors
     * @param array $child_warnings
     * @param float $percent_of_siblings Duration percentage for the stopwatch compared to its siblings
     * @param float|NULL $percent_of_app_total Duration percentage for the stopwatch compared to the app total
     * @return string
     */
    protected function valueForStopwatchReportExpandedDetails(Profile_Timer_Stopwatch $watch, $level, &$child_errors, &$child_warnings, $percent_of_siblings, $percent_of_app_total = null)
    {

        // Row class
        $row_class = $this->classForStopwatchReportRow($watch, $level, $child_errors, $child_warnings, $percent_of_siblings, $percent_of_app_total);

        // Expand details
        $expand = array();

        // More than one iteration?
        $count = $watch->getIterationCount();
        if ($count > 1) {
            $iterations = $watch->getIterations();
            ob_start();

            ?>
            <table style="table-layout: fixed; margin-bottom: 0; width:100%;" <?=$this->attributeWithValue('class', $this->classForStopwatchReportIterationsTable());?>>
                <tbody>
                    <?php foreach ($iterations as $k => $iteration) { ?>
                        <?php $iteration_style = ($k + 1 === $count) ? 'border-bottom: 0;' : 'border-bottom: 1px solid #CCC;'; ?>
                        <tr valign="top" <?=$this->attributeWithValue('class', $row_class);?> nowrap>
                            <td style="<?=$iteration_style;?> width: 75px;">
                                <?php if (!empty($iteration['stack'])) { ?>
                                    <a href="javascript:void(0);" style="float: left; margin-right: 10px; text-decoration: none;" onclick='$(this).closest("tr").next().css("display", (this.innerHTML === <?=json_encode($this->markupForReportRowStackCollapsed()); ?> ? "table-row" : "none")); this.innerHTML = (this.innerHTML === <?=json_encode($this->markupForReportRowStackCollapsed()); ?> ? <?=json_encode($this->markupForReportRowStackExpanded()); ?> : <?=json_encode($this->markupForReportRowStackCollapsed()); ?>);'><?=$this->markupForReportRowStackCollapsed(); ?></a>
                                <?php } ?>
                                #<?=number_format($k + 1);?>
                            </td>
                            <td style="<?=$iteration_style;?>">
                                <div style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;" onclick="this.style.whiteSpace = (this.style.whiteSpace === 'nowrap' ? 'normal' : 'nowrap');">
                                    <?=!empty($iteration['details']) ? $iteration['details'] : '<span style="font-style: italic; color: #A9A9A9;">(No Details)</span>'; ?>
                                </div>
                            </td>
                            <td style="color: #A9A9A9; width: 70px; text-align: left; text-align: right; <?=$iteration_style;?>" nowrap>
                                <?=number_format(($iteration['duration'] / $watch->getDuration()) * 100, 2) . '%'; ?>
                            </td>
                            <td style="color: #A9A9A9; width: 100px; text-align: right; text-align: right; font-weight: bold; <?=$iteration_style;?>" nowrap>
                                <?=$this->microsecondsToString($iteration['duration']); ?>
                            </td>
                        </tr>
                        <?php if (!empty($iteration['stack'])) { ?>
                            <tr style="display: none;">
                                <td>&nbsp;</td>
                                <td colspan="3"><?=$this->markupForStackTrace($iteration['stack'], $row_class); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
            <?php

            $expand[] = ob_get_clean();
        } else {
            // Stack snapshot available?
            $snapshot = $watch->getStackSnapshot();

            // Details available?
            if ($watch->getDetails()) {
                ob_start();

                ?>
                <table style="table-layout: fixed; margin-bottom: 0; width:100%;">
                    <tbody>
                        <tr valign="top" <?=$this->attributeWithValue('class', $row_class);?> nowrap>
                            <td style="width: 75px; border-bottom: 0;">
                                <?php if (!empty($snapshot)) { ?>
                                    <a href="javascript:void(0);" style="float: left; margin-right: 10px; text-decoration: none;" onclick='$(this).closest("tr").next().css("display", (this.innerHTML === <?=json_encode($this->markupForReportRowStackCollapsed()); ?> ? "table-row" : "none")); this.innerHTML = (this.innerHTML === <?=json_encode($this->markupForReportRowStackCollapsed()); ?> ? <?=json_encode($this->markupForReportRowStackExpanded()); ?> : <?=json_encode($this->markupForReportRowStackCollapsed()); ?>);'><?=$this->markupForReportRowStackCollapsed(); ?></a>
                                <?php } ?>
                                #1
                            </td>
                            <td style="border-bottom: 0;">
                                <div style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;" onclick="this.style.whiteSpace = (this.style.whiteSpace === 'nowrap' ? 'normal' : 'nowrap');">
                                    <?=$watch->getDetails(); ?>
                                </div>
                            </td>
                        </tr>
                        <?php if (!empty($snapshot)) { ?>
                            <tr style="display: none;">
                                <td>&nbsp;</td>
                                <td><?=$this->markupForStackTrace($snapshot, $row_class); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php

                $expand[] = ob_get_clean();

            // Only snapshot available
            } elseif (!empty($snapshot)) {
                $expand[] = $this->markupForStackTrace($snapshot, $row_class);
            }
        }

        // Require details
        if (empty($expand)) {
            return '';
        }

        return implode(PHP_EOL, $expand);
    }

    /**
     * Get the inline style for a report row's left accessory anchor
     * @return string
     */
    protected function styleForReportRowLeftAccessoryAnchor()
    {
        return 'cursor: default; text-decoration: none; color:black; outline: none; font-weight: bold;';
    }

    /**
     * Get the inline style for a report row's right accessory anchor
     * @return string
     */
    protected function styleForReportRowRightAccessoryAnchor()
    {
        return 'color:#999999; outline: none; font-size: 80%;';
    }

    /**
     * Get the inline style for the stopwatch report's table
     * @return string
     */
    protected function styleForStopwatchReportTable()
    {
        return 'width: 100%;';
    }

    /**
     * Get the inline style for a given stopwatch column
     * @param string $column_id
     * @return string|NULL
     */
    protected function styleForStopwatchReportColumn($column_id)
    {
        switch ($column_id) {
            case 'alert':
                return 'width: 15px; text-align: center;';
            case 'name':
                return 'text-align: left;';
            case 'time':
                return 'width: 100px; text-align: left;';
            case 'iterations':
                return 'width: 80px; text-align: left;';
            case 'portion':
                return 'width: 70px; text-align: left;';
            case 'duration':
                return 'width: 100px; text-align: right;';
        }
        return null;
    }

    /**
     * Get the inline style for a given stopwatch value
     * @param string $column_id
     * @param int $level
     */
    protected function styleForStopwatchReportValue($column_id, $level)
    {
        switch ($column_id) {
            case 'name':
                return 'padding-left: ' . $this->paddingForNestingLevel($level) . 'px; cursor: default;';
            case 'duration':
                return 'text-align: right;';
        }
        return null;
    }

    /**
     * Whether to display summary rows at the bottom of the stopwatch report
     * @return boolean
     */
    protected function shouldDisplayStopwatchReportSummary()
    {
        return true;
    }

    /**
     * Whether to animate expanding & collapsing rows
     * @return boolean
     */
    protected function shouldAnimateRowVisibility()
    {
        return true;
    }

    /**
     * Get report markup for memory snapshots
     * @param array $snapshots
     * @return string
     */
    protected function markupForMemorySnapshots($snapshots)
    {
        if (empty($snapshots)) {
            return '';
        }

        // Snapshot stats
        $snapshot_max_bytes = 0;
        foreach ($snapshots as $snapshot) {
            if ($snapshot_max_bytes < $snapshot->getUsage()) {
                $snapshot_max_bytes = $snapshot->getUsage();
            }
        }

        // Application stats
        $memory_limit = ini_get('memory_limit');
        $application_max_bytes = memory_get_peak_usage(true);
        $application_max_bytes_allowed = null;
        if (!empty($memory_limit)) {
            $application_max_bytes_allowed = $this->stringToBytes($memory_limit);
        }

        // Chart points
        $chart_points = array();
        foreach ($snapshots as $snapshot) {
            if (!empty($application_max_bytes_allowed)) {
                $percent_of_app_max = number_format(($snapshot->getUsage() / $application_max_bytes_allowed) * 100, 2);
                $chart_points[] = $percent_of_app_max;
            }
        }

        // Report columns
        $columns = $this->columnsForMemorySnapshotReport();

        // Start buffer
        ob_start();
        ?>
        <?=$this->scriptForMemorySnapshotReport();?>
        <?=$this->markupForMemorySnapshotReportTitle($snapshot_max_bytes, $application_max_bytes, $chart_points, $application_max_bytes_allowed);?>
        <table <?=$this->attributeWithValue('class', $this->classForMemorySnapshotReportTable());?> <?=$this->attributeWithValue('style', $this->styleForMemorySnapshotReportTable());?>>
            <thead>
                <tr>
                    <?php foreach ($columns as $id => $column_name) { ?>
                        <th <?=$this->attributeWithValue('style', $this->styleForMemorySnapshotReportColumn($id));?>>
                            <?=htmlspecialchars($column_name);?>
                        </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    $snapshot_previous = null;
                foreach ($snapshots as $snapshot) {
                    echo $this->prepareAndRenderRowForMemorySnapshotReport($snapshot, $snapshot_previous, $application_max_bytes_allowed) . PHP_EOL;
                    $snapshot_previous = $snapshot;
                }
                ?>
                <?php if ($this->shouldDisplayMemorySnapshotReportSummary()) { ?>
                    <?=$this->markupForMemorySnapshotReportSummary($snapshot_max_bytes, $application_max_bytes, $application_max_bytes_allowed);?>
                <?php } ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean();
    }

    /**
     * Perform calculations for a given memory snapshot row and get its markup
     * @param Profile_Memory_Snapshot $snapshot
     * @param Profile_Memory_Snapshot $snapshot_previous
     * @param int $application_max_bytes_allowed Highest amount of memory (in bytes) that the application is allowed to use
     * @return string
     */
    protected function prepareAndRenderRowForMemorySnapshotReport(Profile_Memory_Snapshot $snapshot, Profile_Memory_Snapshot $snapshot_previous = null, $application_max_bytes_allowed = null)
    {

        // Snapshot usage
        $usage = $snapshot->getUsage();

        // Calculate change
        $change = 0;
        if (!empty($snapshot_previous)) {
            $change = $usage - $snapshot_previous->getUsage();
        }

        // Calculate percentage of app max
        $percent_of_app_max = null;
        if (!empty($application_max_bytes_allowed)) {
            $percent_of_app_max = number_format(($usage / $application_max_bytes_allowed) * 100, 2);
        }

        // Configure entry
        $entry = array();
        $columns = $this->columnsForMemorySnapshotReport();
        foreach ($columns as $id => $column_name) {
            $entry[$id] = $this->valueForMemorySnapshotReportColumn($id, $snapshot, $change, $application_max_bytes_allowed, $percent_of_app_max);
        }

        // Render
        return $this->markupForMemorySnapshotReportRow($snapshot, $entry, $change, $application_max_bytes_allowed, $percent_of_app_max);
    }

    /**
     * Get HTML to display for a prepared memory snapshot report row
     * @param Profile_Memory_Snapshot $snapshot
     * @param array $entry Prepared row entry data
     * @param int $change Difference in usage compared to the previous snapshot (in bytes)
     * @param int $application_max_bytes_allowed Highest amount of memory (in bytes) that the application is allowed to use
     * @param float $percent_of_app_max Snapshot usage percentage of the application's maximum allowed usage
     * @return string
     */
    protected function markupForMemorySnapshotReportRow(Profile_Memory_Snapshot $snapshot, $entry, $change, $application_max_bytes_allowed = null, $percent_of_app_max = null)
    {

        // Columns
        $columns = $this->columnsForMemorySnapshotReport();

        // Row class
        $row_class = $this->classForMemorySnapshotReportRow($snapshot, $change, $application_max_bytes_allowed, $percent_of_app_max);

        // Expanded details
        $expanded = $this->valueForMemorySnapshotReportExpandedDetails($snapshot, $change, $application_max_bytes_allowed, $percent_of_app_max);

        // Accessory
        $accessory_right_expanded = $this->markupForReportRowRightAccessoryExpanded();
        $accessory_right_collapsed = $this->markupForReportRowRightAccessoryCollapsed();

        // Start buffer
        ob_start();
        ?>
        <tr
            <?=$this->attributeWithValue('class', $row_class);?>
            data-accessory-right-expanded='<?=htmlspecialchars(json_encode(array('html' => $accessory_right_expanded)));?>'
            data-accessory-right-collapsed='<?=htmlspecialchars(json_encode(array('html' => $accessory_right_collapsed)));?>'
        >
            <?php foreach ($columns as $id => $column_name) { ?>
                <td <?=$this->attributeWithValue('style', $this->styleForMemorySnapshotReportValue($id));?>>
                    <?=$entry[$id];?>
                </td>
            <?php } ?>
        </tr>
        <?php if (!empty($expanded)) { ?>
            <tr class="entry-details <?=$this->classForHiddenProfileComponent();?> <?=!empty($row_class) ? $row_class : '';?>">
                <td style="border-top: 0;"></td>
                <td style="border-top: 0;" colspan="<?=count($columns) - 1;?>">
                    <?=$expanded;?>
                </td>
            </tr>
        <?php } ?>
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
        return '<h3>Memory Snapshots</h3>';
    }

    /**
     * Get HTML to display as the title for a memory snapshot report's expanded details' Trace
     * @return string
     */
    protected function markupForMemorySnapshotReportExpandedDetailsTraceTitle()
    {
        return '<h4>Trace</h4>';
    }

    /**
     * Get HTML to display as the title for a memory snapshot report's expanded details' Details
     * @return string
     */
    protected function markupForMemorySnapshotReportExpandedDetailsDetailsTitle()
    {
        return '<h4>Details</h4>';
    }

    /**
     * Get HTML to display after all rows in the memory snapshot report's table body
     * @param int $snapshot_max_bytes Highest memory usage encountered within all snapshots (in bytes)
     * @param int $application_max_bytes Highest memory usage encountered by the whole application, not just snapshots (in bytes)
     * @param int $application_max_bytes_allowed Highest amount of memory (in bytes) that the application is allowed to use
     * @return string
     */
    protected function markupForMemorySnapshotReportSummary($snapshot_max_bytes, $application_max_bytes, $application_max_bytes_allowed = null)
    {

        // Column count
        $column_count = count($this->columnsForMemorySnapshotReport());

        // Start buffer
        ob_start();
        ?>
        <tr>
            <td colspan="<?=$column_count - 1;?>" style="text-align: right;">
                <?=$this->markupForMemorySnapshotReportSummarySnapshotsPeakKey();?>
            </td>
            <td style="text-align: right;">
                <?=$this->markupForMemorySnapshotReportSummarySnapshotsPeakValue($snapshot_max_bytes);?>
            </td>
        </tr>
        <tr>
            <td colspan="<?=$column_count - 1;?>" style="text-align: right;">
                <?=$this->markupForMemorySnapshotReportSummaryApplicationPeakKey();?>
            </td>
            <td style="text-align: right;">
                <?=$this->markupForMemorySnapshotReportSummaryApplicationPeakValue($application_max_bytes);?>
            </td>
        </tr>
        <?php if (!empty($application_max_bytes_allowed)) { ?>
            <tr>
                <td colspan="<?=$column_count - 1;?>" style="text-align: right;">
                    <?=$this->markupForMemorySnapshotReportSummaryApplicationMaxKey();?>
                </td>
                <td style="text-align: right;">
                    <?=$this->markupForMemorySnapshotReportSummaryApplicationMaxValue($application_max_bytes_allowed);?>
                </td>
            </tr>
            <tr>
                <td colspan="<?=$column_count - 1;?>" style="text-align: right;">
                    <?=$this->markupForMemorySnapshotReportSummaryMaximumUsageKey();?>
                </td>
                <td style="text-align: right;">
                    <?php $usage_percent = number_format(($application_max_bytes / $application_max_bytes_allowed) * 100, 2); ?>
                    <?=$this->markupForMemorySnapshotReportSummaryMaximumUsageValue($usage_percent);?>
                </td>
            </tr>
        <?php } ?>
        <?php
        return ob_get_clean();
    }

    /**
     * Get HTML to display as the Snapshots Peak key in the memory snapshot report's summary
     * @return string
     */
    protected function markupForMemorySnapshotReportSummarySnapshotsPeakKey()
    {
        return '<strong>Snapshots Peak:</strong>';
    }

    /**
     * Get HTML to display as the Snapshots Peak value in the memory snapshot report's summary
     * @param int $bytes
     * @return string
     */
    protected function markupForMemorySnapshotReportSummarySnapshotsPeakValue($bytes)
    {
        return '<strong>' . $this->bytesToString($bytes) . '</strong>';
    }

    /**
     * Get HTML to display as the Application Peak key in the memory snapshot report's summary
     * @return string
     */
    protected function markupForMemorySnapshotReportSummaryApplicationPeakKey()
    {
        return '<strong>Application Peak:</strong>';
    }

    /**
     * Get HTML to display as the Application Peak value in the memory snapshot report's summary
     * @param int $bytes
     * @return string
     */
    protected function markupForMemorySnapshotReportSummaryApplicationPeakValue($bytes)
    {
        return '<strong>' . $this->bytesToString($bytes) . '</strong>';
    }

    /**
     * Get HTML to display as the Application Maximum key in the memory snapshot report's summary
     * @return string
     */
    protected function markupForMemorySnapshotReportSummaryApplicationMaxKey()
    {
        return '<strong>Application Max:</strong>';
    }

    /**
     * Get HTML to display as the Application Maximum value in the memory snapshot report's summary
     * @param int $bytes
     * @return string
     */
    protected function markupForMemorySnapshotReportSummaryApplicationMaxValue($bytes)
    {
        return '<strong>' . $this->bytesToString($bytes) . '</strong>';
    }

    /**
     * Get HTML to display as the Maximum Usage key in the memory snapshot report's summary
     * @return string
     */
    protected function markupForMemorySnapshotReportSummaryMaximumUsageKey()
    {
        return '<strong>Max Usage:</strong>';
    }

    /**
     * Get HTML to display as the Maximum Usage value in the memory snapshot report's summary
     * @param float $percentage
     * @return string
     */
    protected function markupForMemorySnapshotReportSummaryMaximumUsageValue($percentage)
    {
        return '<strong>' . $percentage . '%</strong>';
    }

    /**
     * Get the column names for the memory snapshot report
     * @return array
     */
    protected function columnsForMemorySnapshotReport()
    {
        return array(
            'alert'     => '',
            'name'      => 'Name',
            'time'      => 'Timestamp',
            'change'    => 'Change',
            'usage'     => 'Usage',
        );
    }

    /**
     * Get the class name for the memory snapshot report's table
     * @return string|NULL
     */
    protected function classForMemorySnapshotReportTable()
    {
        return null;
    }

    /**
     * Get the class name for a memory snapshot report's row
     * @param Profile_Memory_Snapshot $snapshot
     * @param int $change Difference in usage compared to the previous snapshot (in bytes)
     * @param int $application_max_bytes_allowed Highest amount of memory (in bytes) that the application is allowed to use
     * @param float $percent_of_app_max Snapshot usage percentage of the application's maximum allowed usage
     * @return string|NULL
     */
    protected function classForMemorySnapshotReportRow(Profile_Memory_Snapshot $snapshot, $change, $application_max_bytes_allowed = null, $percent_of_app_max = null)
    {
        return null;
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
            case 'alert':
                return '';
            case 'name':
                // Expanded details
                $expanded = $this->valueForMemorySnapshotReportExpandedDetails($snapshot, $change, $application_max_bytes_allowed, $percent_of_app_max);

                // Accessory
                $accessory_right_collapsed = $this->markupForReportRowRightAccessoryCollapsed();

                // Start buffer
                ob_start();
                ?>
                <?=$snapshot->getName();?>
                <?php if (!empty($expanded)) { ?>
                    <a class="toggle-details" <?=$this->attributeWithValue('style', $this->styleForReportRowRightAccessoryAnchor());?> href="javascript:void(0);" onclick="javascript:toggleDetails(this);">
                        <span class="accessory-right"><?=$accessory_right_collapsed;?></span>
                    </a>
                <?php } ?>
                <?php
                return ob_get_clean();

            case 'time':
                return date('H:i:s', ceil($snapshot->getTime()));
            case 'change':
                // Absolute change
                $change_abs = abs($change);

                // Require change
                if ($change_abs === 0) {
                    return '';
                }

                // Growth
                if ($change < 0) {
                    return '-' . $this->bytesToString($change_abs) . ' (' . number_format($change_abs) . ')';
                } else { // Decrease
                    return '+' . $this->bytesToString($change_abs) . ' (' . number_format($change_abs) . ')';
                }

                case 'usage':
                    if (!empty($percent_of_app_max)) {
                        return '<span title="' . $percent_of_app_max . '% of Application Max">' . $this->bytesToString($snapshot->getUsage()) . '</span>';
                    } else {
                        return $this->bytesToString($snapshot->getUsage());
                    }
        }

        // Unrecognized column
        return null;
    }

    /**
     * Get the HTML to display for a memory snapshot report entry's expanded details row
     * @param Profile_Memory_Snapshot $snapshot
     * @param int $change Difference in usage compared to the previous snapshot (in bytes)
     * @param int $application_max_bytes_allowed Highest amount of memory (in bytes) that the application is allowed to use
     * @param float $percent_of_app_max Snapshot usage percentage of the application's maximum allowed usage
     * @return string
     */
    protected function valueForMemorySnapshotReportExpandedDetails(Profile_Memory_Snapshot $snapshot, $change, $application_max_bytes_allowed = null, $percent_of_app_max = null)
    {

        // Row class
        $row_class = $this->classForMemorySnapshotReportRow($snapshot, $change, $application_max_bytes_allowed, $percent_of_app_max);

        // Expand details
        $expand = array();

        // Details available?
        if ($snapshot->getDetails()) {
            $expand[] = $this->markupForMemorySnapshotReportExpandedDetailsDetailsTitle() . $snapshot->getDetails();
        }

        // Stack snapshot available?
        if ($snapshot->getStackSnapshot()) {
            $expand[] = $this->markupForMemorySnapshotReportExpandedDetailsTraceTitle() . $this->markupForStackTrace($snapshot->getStackSnapshot(), $row_class);
        }

        // Require details
        if (empty($expand)) {
            return '';
        }

        return implode(PHP_EOL, $expand);
    }

    /**
     * Get the inline style for the memory snapshot report's table
     * @return string
     */
    protected function styleForMemorySnapshotReportTable()
    {
        return 'width: 100%;';
    }

    /**
     * Get the inline style for a given memory snapshot column
     * @param string $column_id
     * @return string|NULL
     */
    protected function styleForMemorySnapshotReportColumn($column_id)
    {
        switch ($column_id) {
            case 'alert':
                return 'width: 15px; text-align: center;';
            case 'name':
                return 'text-align: left;';
            case 'time':
                return 'text-align: left; width: 100px;';
            case 'change':
                return 'text-align: left; width: 250px;';
            case 'usage':
                return 'text-align: right; width: 100px;';
        }
        return null;
    }

    /**
     * Get the inline style for a given memory snapshot value
     * @param string $column_id
     * @param string|NULL
     */
    protected function styleForMemorySnapshotReportValue($column_id)
    {
        switch ($column_id) {
            case 'usage':
                return 'text-align: right;';
        }
        return null;
    }

    /**
     * Whether to display summary rows at the bottom of the memory snapshot report
     * @return boolean
     */
    protected function shouldDisplayMemorySnapshotReportSummary()
    {
        return true;
    }

    /**
     * Get HTML to display when there is nothing for the report to display
     * @return string
     */
    protected function markupForUnavailableReport()
    {
        return '<p>There are no profiled components to display.</p>';
    }

    /**
     * Get markup for a stack snapshot
     * @param array $stack_snapshot
     * @param string $title
     * @param string $row_class
     * @return string
     */
    protected function markupForStackTrace($stack_snapshot, $row_class = '')
    {
        if (empty($stack_snapshot)) {
            return '';
        }
        ob_start();
        ?>
        <table class="<?=$this->classForStackTraceTable();?>" style="background-color: transparent;">
            <tbody>
                <?php foreach ($stack_snapshot as $k => $stack) { ?>
                    <tr <?=!empty($row_class) ? 'class="' . $row_class . '"' : '';?>>
                        <td style="border:0;">
                            <code>
                            <?=htmlspecialchars($stack['file']);?>:<strong><?=$stack['line'];?></strong>
                            @
                            <?php if (!empty($stack['class'])) { ?>
                                <?=htmlspecialchars($stack['class']);?><?=htmlspecialchars($stack['type']);?><?=htmlspecialchars($stack['function']);?>
                            <?php } else { ?>
                                <?=htmlspecialchars($stack['function']);?>
                            <?php } ?>
                            </code>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean();
    }

    /**
     * Get HTML to display for a line chart with a collection of points
     * @param array $points Collection of float or int values
     * @return string
     */
    protected function markupForLineChart($points)
    {
        if (empty($points)) {
            return '';
        }
        $img = '<img src="https://chart.googleapis.com/chart?chs=100x50&cht=ls&chco=0077CC&chd=t:' . implode(',', $points) . '&chf=bg,s,EFEFEF00">';
        return $img;
    }

    /**
     * Get style information to include for the whole report
     * @return string|NULL
     */
    protected function styleForReport()
    {
        ob_start();
        ?>
        <style>
            .animate-height-wrap {
                transition: height <?=$this->animationDuration() / 1000;?>s;
                -webkit-transition: height <?=$this->animationDuration() / 1000;?>s;
            }

            .profile-component-hide {
                display: none;
            }

        </style>
        <?php
        return ob_get_clean();
    }

    /**
     * Get javascript to include for the whole report
     * @return string|NULL
     */
    protected function scriptForReport()
    {
        ob_start();
        ?>
        <script>
            function slideTableRow ($row, hideClass, direction) {
                if (!$row || !$row.length) return;
                if (!direction) return;

                var animateDuration = <?=json_encode($this->animationDuration());?>;

                // Slide down end
                var onSlideDownEnd = function () {
                    var $this = $(this),
                        $children = $this.children();

                    if ($children.length) {
                        $children.unwrap();
                    } else {
                        var contents = $this.html(),
                            $td = $this.parent();
                        $this.remove();
                        $td.html(contents);
                    }
                };

                // Slide up end
                var onSlideUpEnd = function () {
                    var $this = $(this),
                        $children = $this.children();

                    if ($children.length) {
                        $children.unwrap();
                    } else {
                        var contents = $this.html(),
                            $td = $this.parent();
                        $this.remove();
                        $td.html(contents);
                    }

                    $row.addClass(hideClass);
                };

                if (direction === 'down') {
                    $row.children('td').wrapInner('<div style="height:0; overflow: hidden; margin: 0; padding: 0;" class="animate-height-wrap">');
                    $row.removeClass(hideClass);

                    var targetHeight = 0,
                        $animateDivs = $row.find('.animate-height-wrap');

                    $animateDivs.each(function () {
                        var $div = $(this),
                            divHeight = 0;

                        $div.children().each(function () {
                            divHeight += $(this).outerHeight(true);
                        });

                        if (divHeight > targetHeight) {
                            targetHeight = divHeight;
                        }
                    });

                    $animateDivs.each(function () {
                        var $div = $(this);

                        $div.css('height', targetHeight + 'px');

                        setTimeout(function () {
                            onSlideDownEnd.call($div[0]);
                        }, animateDuration);
                    });
                } else if (direction === 'up') {

                    var initialHeight = 0;
                    $row.children('td').each(function () {
                        var $td = $(this),
                            height = $td.outerHeight(true);

                        if (height > initialHeight) {
                            initialHeight = height;
                        }
                    });

                    // Wrap
                    $row.children('td').wrapInner('<div style="height:' + initialHeight + 'px; overflow: hidden; margin: 0; padding: 0;" class="animate-height-wrap">');

                    var targetHeight = 0,
                        $animateDivs = $row.find('.animate-height-wrap');

                    var setHeight = function ($el, h) {
                        $el.css('height', h + 'px');
                    }

                    // Animate height
                    $animateDivs.each(function () {
                        var $div = $(this);

                        setTimeout(function () {
                            setHeight($div, targetHeight);

                            setTimeout(function () {
                                onSlideUpEnd.call($div[0]);
                            }, animateDuration);
                        }, 50);
                    });
                }
            }

            function toggleDetails (el) {
                var $el = $(el),
                    $tr = $el.parents('tr').first(),
                    $details = $tr.next('.entry-details'),
                    hideClass = <?=json_encode($this->classForHiddenProfileComponent());?>,
                    rightAccessoryHTMLExpanded = $tr.data('accessoryRightExpanded').html,
                    rightAccessoryHTMLCollapsed = $tr.data('accessoryRightCollapsed').html,
                    animateToggle = <?=json_encode($this->shouldAnimateRowVisibility());?>;

                var shouldExpand = $details.hasClass(hideClass);

                // Animate?
                if (animateToggle) {
                    if (shouldExpand) {
                        slideTableRow($details, hideClass, 'down');
                        shouldExpand = false;
                    } else {
                        slideTableRow($details, hideClass, 'up');
                        shouldExpand = true;
                    }
                } else {
                    $details.toggleClass(hideClass);
                }

                if (shouldExpand) {
                    $tr.find('.accessory-right').html(rightAccessoryHTMLCollapsed);
                } else {
                    $tr.find('.accessory-right').html(rightAccessoryHTMLExpanded);
                }

                return false;
            }
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Get javascript to include for the stopwatch report
     * @return string|NULL
     */
    protected function scriptForStopwatchReport()
    {
        ob_start();
        ?>
        <script>
            function toggleChildren (el, level) {

                var $el = $(el),
                    $tr = $el.parents('tr').first(),
                    parentLevel = $tr.data('childLevel'),
                    isExpanded = !$tr.hasClass('closed'),
                    hideClass = <?=json_encode($this->classForHiddenProfileComponent());?>,
                    leftAccessoryHTMLExpanded = $tr.data('accessoryLeftExpanded').html,
                    leftAccessoryHTMLCollapsed = $tr.data('accessoryLeftCollapsed').html,
                    rightAccessoryHTMLCollapsed = $tr.data('accessoryRightCollapsed').html,
                    animateToggle = <?=json_encode($this->shouldAnimateRowVisibility());?>;

                // Expand & show children
                if (!isExpanded) {
                    var $children = $tr.nextAll('.entry-child');
                    $children.each(function () {
                        var $row = $(this),
                            childLevel = $row.data('childLevel');

                        // Bail after encountering higher-level child
                        if (childLevel <= parentLevel) return false;

                        // Skip non-immediate children
                        if (childLevel != (parentLevel + 1)) return true;

                        if (animateToggle) {
                            slideTableRow($row, hideClass, 'down');
                            $row.addClass('closed');
                        } else {
                            $row.removeClass(hideClass).addClass('closed');
                        }

                        $row.find('.toggle-children').find('.accessory-left').html(leftAccessoryHTMLCollapsed);
                    });

                    // Remove closed status
                    $tr.removeClass('closed');
                    $el.find('.accessory-left').html(leftAccessoryHTMLExpanded);
                } else {

                    // Collapse
                    var $children = $tr.nextAll('.entry-child');
                    $children.each(function () {
                        var $row = $(this),
                            childLevel = $row.data('childLevel');

                        // Bail after encountering higher-level child
                        if (childLevel <= parentLevel) return false;

                        // Hide row & remove closed state
                        if (animateToggle) {
                            slideTableRow($row, hideClass, 'up');
                            $row.removeClass('closed');
                        } else {
                            $row.addClass(hideClass).removeClass('closed');
                        }

                        $row.find('.toggle-children').find('.accessory-left').html(leftAccessoryHTMLExpanded);
                        $row.find('.accessory-right').html(rightAccessoryHTMLCollapsed);

                        // Hide row expanded details
                        if (animateToggle) {
                            slideTableRow($row.next('.entry-details'), hideClass, 'up');
                        } else {
                            $row.next('.entry-details').addClass(hideClass);
                        }
                    });

                    // Set closed status
                    $tr.addClass('closed');
                    $el.find('.accessory-left').html(leftAccessoryHTMLCollapsed);
                }

                return false;
            }
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Get javascript to include for the memory snapshot report
     * @return string|NULL
     */
    protected function scriptForMemorySnapshotReport()
    {
        return null;
    }

    /**
     * Convert microseconds to human-readable string
     * @param float $microseconds
     * @param int $decimals Decimal precision
     * @return string
     */
    protected function microsecondsToString($microseconds, $decimals = 4)
    {
        if ($microseconds < 1) {
            return '<span title="' . number_format($microseconds, $decimals) . ' sec">' . number_format($microseconds * 1000, 2) . ' ms</span>';
        } else {
            return number_format($microseconds, $decimals) . ' sec';
        }
    }

    /**
     * Get HTML for a given attribute and its value
     * @param string $attribute
     * @param string $value
     * @return string
     */
    private function attributeWithValue($attribute, $value)
    {
        if (empty($value)) {
            return '';
        }
        return $attribute . '="' . htmlspecialchars($value) . '"';
    }
}
