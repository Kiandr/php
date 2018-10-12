## Changing "Other" task type value to "Custom"

ALTER TABLE `tasks` CHANGE `type` `type` enum('Custom','Email','Call','Text','Group','Listing','Search') NOT NULL DEFAULT 'Custom';

ALTER TABLE `users_tasks` CHANGE `type` `type` enum('Custom','Email','Call','Text','Group','Listing','Search') NOT NULL DEFAULT 'Custom';
