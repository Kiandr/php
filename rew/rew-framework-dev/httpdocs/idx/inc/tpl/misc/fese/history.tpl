<?php

// Property history
if (!empty($history)) {
    $count = 0;
    $lastDate = false;
    echo '<div id="listing-history" class="marV-md">';
    echo sprintf('<h2 class="page-h2">%s (%s%s) Property History</h2>', implode(', ', [$listing['Address'], $listing['AddressCity']]), Lang::write('MLS_NUMBER'), $listing['ListingMLS']);
    echo '<div class="timeline mar0">';
    echo '<div class="bd">';
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
            echo sprintf('<div class="event%s">', $count > 5 ? ' hidden' : '');
            echo sprintf('<time class="event-date padV-md" datetime="%s">%s</time>', date('c', $event['Date']), $date);
            echo '<div class="crd">';
            echo '<div class="tail LT"></div>';
        }
        echo sprintf('<span class="strong">%s</span><br />', $event['Details']);
        $lastDate = $date;

    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    if ($count > 5) {
        echo sprintf(
            '<a href="#listing-history" onclick="%s">Show All History (%d)</a>',
            "$(this).addClass('hidden').parent().find('.event').removeClass('hidden');",
            $count);
    }
    echo '</div>';
}