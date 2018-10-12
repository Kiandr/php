<?php

/* Include Common File */
require_once '../../../common.inc.php';

/* JSON Collection */
$json = array();

/* Select Calendar Reminders */
$query = "SELECT
            `t`.`title` AS `type`,
            `e`.`title`,
            `e`.`body`,
            `r`.`id`,
            `r`.`reminder_type`,
            `r`.`reminder_time`,
            `r`.`reminder_interval`,
            `d`.`start`,
            `d`.`end`,
            IF(TIME(`d`.`start`) = '00:00:00', 1, 0) AS `all_day`,
            CASE
                r.reminder_interval
                WHEN 'minutes' THEN DATE_SUB(`d`.`start`, INTERVAL `r`.`reminder_time` MINUTE)
                WHEN 'hours'   THEN DATE_SUB(`d`.`start`, INTERVAL `r`.`reminder_time` HOUR)
                WHEN 'days'    THEN DATE_SUB(`d`.`start`, INTERVAL `r`.`reminder_time` DAY)
                WHEN 'weeks'   THEN DATE_SUB(`d`.`start`, INTERVAL (`r`.`reminder_time` * 7) DAY)
                WHEN 'months'  THEN DATE_SUB(`d`.`start`, INTERVAL `r`.`reminder_time` MONTH)
            END AS `timestamp`,
            DATEDIFF(CURDATE(), `d`.`start`) AS `days`" . "\n"
     . " FROM `" . TABLE_CALENDAR_EVENTS . "` `e`" . "\n"
     . " LEFT JOIN `" . TABLE_CALENDAR_REMINDERS . "` `r` ON `e`.`id` = `r`.`event`" . "\n"
     . " LEFT JOIN `" . TABLE_CALENDAR_DATES . "` `d` ON `e`.`id` = `d`.`event`" . "\n"
     . " LEFT JOIN `" . TABLE_CALENDAR_TYPES . "` `t` ON `e`.`type` = `t`.`id`" . "\n"
     . " WHERE `r`.`id` IS NOT NULL " . "\n"
     . ($authuser->isAgent() ? " AND `e`.`agent` = '" . $authuser->info('id') . "'" : "")
     . ($authuser->isAssociate() ? " AND `e`.`associate` = '" . $authuser->info('id') . "'" : "")
     . " AND `r`.`sent` = 'N'" . "\n"
     . " AND `r`.`reminder_type` = 'Pop-up'" . "\n"
     . " HAVING `timestamp` <= '" . date('Y-m-d H:i:s', time()) . "'" . "\n"
     . " ORDER BY `d`.`start` ASC";

$event_reminders = mysql_query($query) or die(mysql_error());

/* Check Count */
$count = @mysql_num_rows($event_reminders);
if ($count > 0) {
    /* Reminder Collection */
    $json['reminders'] = array();

    /**
     * Loop through Results
     */
    while ($event_reminder = mysql_fetch_assoc($event_reminders)) {
        /* Date Format */
        $format = !empty($event_reminder['all_day']) ? 'D, M. jS' : 'D, M. jS @ g:i A';

        /* Reminder */
        $reminder = array();
        $reminder['message']  = '<strong>' . date($format, strtotime($event_reminder['start'])) . '</strong>' . '<br />';
        $reminder['message'] .= '<strong>Event Title:</strong> ' . $event_reminder['title'] . ' (' . $event_reminder['type'] . ') ' . '<br />';
        $reminder['message'] .= !empty($event_reminder['body']) ? '<strong>Event Body:</strong> ' . $event_reminder['body'] : '';

        /* Update Reminder */
        mysql_query("UPDATE `" . TABLE_CALENDAR_REMINDERS . "` SET `sent` = 'Y' WHERE `id` = '" . $event_reminder['id'] . "'");

        /* Add to Collection */
        $json['reminders'][] = $reminder;
    }
}

/* Send as JSON */
header('Content-type: application/json');

/* Return JSON */
die(json_encode($json));
