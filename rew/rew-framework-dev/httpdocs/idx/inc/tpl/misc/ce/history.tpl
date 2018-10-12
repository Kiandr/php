<?php

// Property history
if (!empty($history)) {
    $count = 0;
    $lastDate = false;
    echo '<div id="listing-history" class="-mar-bottom">';
    echo '<div class="divider -pad-vertical"><span class="divider__label -left -text-upper -text-xs">Property History</span></div>';
    echo '<div class="timeline -mar-0">';
    echo '<div class="keyvals">';
    foreach ($history as $event) {
        $date = date('M. j', $event['Date']);
        $newDate = $lastDate && $lastDate !== $date;
        $newCard = !$lastDate || $newDate;
        if ($newDate) {
            echo '</div>';
            echo '</div>';
        }
        if ($newCard) {
            $count++;
            echo '<div class="keyvals__body">';
            echo sprintf('<div class="keyval%s">', $count > 5 ? ' hidden' : '');
            echo sprintf('<time class="keyval__key -strong event-date" datetime="%s">%s</time>', date('c', $event['Date']), $date);
            
        }
        echo sprintf('<span class="keyval__val">%s</span>', $event['Details']);
        $lastDate = $date;

    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    if ($count > 5) {
        echo sprintf(
            '<a href="#listing-history" class="button" onclick="%s">Show All History (%d)</a>',
            "$(this).addClass('hidden').parent().find('.event').removeClass('hidden');",
            $count);
    }
    echo '</div>';
}

