-- Add `_listings`.`link`
ALTER TABLE `_listings`
	ADD `link` VARCHAR(100) NULL AFTER `agent`,
	ADD UNIQUE (`link`);