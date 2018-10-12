<div class="block">

<table class="calendar">

<?php // Calendar Headings ?>
    <thead class="calendar__head">
        <tr><!--<th class="calendar__heading">&nbsp;</th>-->
            <?php foreach (\REW\Backend\Calendar::DAYS_OF_THE_WEEK as $index => $value) { ?>
                <th class="calendar__heading"><abbr title="<?=\REW\Backend\Calendar::DAYS_OF_THE_WEEK[$index]; ?>"><span><?=\REW\Backend\Calendar::ABBRV_DAYS_OF_THE_WEEK[$index][0]; ?></span></abbr></th>
            <?php } ?>
        </tr>
    </thead>

<?php // Calendar Body ?>
    <tbody>
        <tr>

            <?php // Add Extra Columns to Fill Calendar ?>
            <?php if ($weekday > 0) { ?>
                <td colspan="<?=$weekday; ?>" class="empty">&nbsp;</td>
            <?php }

// Build Calendar
for ($day = 1; $day <= $days; $day++) {

    // Day
    $day = str_pad(intval($day), 2, '0', STR_PAD_LEFT);

    // Reached End of Week..
    if ($weekday == 7) {

        // Search by Week
    ?>
        </tr>
        <tr>

<?php
        // Reset Weekday
        $weekday = 0;

    }

    // Current Date
    $date = $calendar->currentMonth() . '-'  . $day;

    // Today?
    $today = ($date == date('Y-m-d'));

    // Day Column
    ?>
            <td align="center" class="calendar__day<?=($today ? ' today' : '')?>">
                <a href="?view=day&date=<?=$date; ?>"><?=$day; ?></a>
                <div class="events"></div>
            </td>
<?php

    // Increment Day
    $weekday++;

}

// Add Extra Columns to Fill Calendar
if ($weekday != 7) { ?>
            <td colspan="<?=(7 - $weekday);?>" class="empty">&nbsp;</td>
<?php } ?>

        </tr>
    </tbody>
</table>

</div>