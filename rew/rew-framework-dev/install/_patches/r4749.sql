-- Add `_listings`.`imported`
ALTER TABLE `_listings`
	ADD `imported` ENUM('true', 'false') NOT NULL DEFAULT 'false' AFTER `views`;
	
-- Delete Bad Location
DELETE FROM `_listing_locations` WHERE `state` = 'select';

-- Change Location 'Yukon' to 'Yukon Territory''
UPDATE `_listing_locations` SET `state` = 'Yukon Territory' WHERE `state` = 'Yukon'; 

-- Update Listings using 'Yukon' to use 'Yukon Territory'
UPDATE `_listings` SET `state` = 'Yukon Territory' WHERE `state` = 'Yukon'; 