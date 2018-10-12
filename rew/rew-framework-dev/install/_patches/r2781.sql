ALTER TABLE `users_reminders` 
  ADD `timestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `type`,
  DROP INDEX `sent`;

UPDATE `users_reminders` SET `timestamp` = CONCAT(`date`, ' ', IF(`time` IS NULL, 0, `time`));

ALTER TABLE `users_reminders` DROP `date`, DROP `time`;
