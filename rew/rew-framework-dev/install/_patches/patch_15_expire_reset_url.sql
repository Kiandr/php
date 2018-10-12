ALTER TABLE `auth`
  ADD `timestamp_reset` timestamp NOT NULL default '0000-00-00 00:00:00';

ALTER TABLE `users`
  ADD `timestamp_reset` timestamp NOT NULL default '0000-00-00 00:00:00';
