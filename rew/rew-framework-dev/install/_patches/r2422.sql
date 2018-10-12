ALTER TABLE `rewidx_defaults` DROP `id` ;
ALTER TABLE `rewidx_defaults` ADD PRIMARY KEY(`idx`) ;
REPLACE INTO `rewidx_defaults` (`idx`, `view`, `sort_by`, `panels`, `criteria`, `timestamp_created`, `timestamp_updated`) VALUES ('', '', '', '', '', NOW(), NOW());
ALTER TABLE `rewidx_details` ADD `idx` VARCHAR( 100 ) NOT NULL FIRST ;
ALTER TABLE `rewidx_details` ADD PRIMARY KEY ( `idx` ) ;
ALTER TABLE `rewidx_system` ADD `idx` VARCHAR( 100 ) NOT NULL FIRST ;
ALTER TABLE `rewidx_system` ADD PRIMARY KEY ( `idx` ) ;
ALTER TABLE `rewidx_system` ADD `registration_verify` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER `registration_phone` ;