-- Update `users_listings` To Have `bedrooms`, `bathrooms`, `sqft`:
ALTER TABLE `users_listings`
    ADD `bedrooms` TINYINT( 3 ) UNSIGNED NOT NULL AFTER `subdivision` ,
    ADD `bathrooms` DECIMAL( 8, 2 ) UNSIGNED NOT NULL AFTER `bedrooms` ,
    ADD `sqft` INT( 10 ) UNSIGNED NOT NULL AFTER `bathrooms` ;

-- Update `users_viewed_listings` To Have `bedrooms`, `bathrooms`, `sqft`:
ALTER TABLE `users_viewed_listings`
    ADD `bedrooms` TINYINT( 3 ) UNSIGNED NOT NULL AFTER `subdivision` ,
    ADD `bathrooms` DECIMAL( 8, 2 ) UNSIGNED NOT NULL AFTER `bedrooms` ,
    ADD `sqft` INT( 10 ) UNSIGNED NOT NULL AFTER `bathrooms` ;
    
-- Update `users_reminders`.`type`:
ALTER TABLE `users_reminders`
    CHANGE `type` `type` VARCHAR( 100 ) NOT NULL,
    DROP INDEX `type` ; 