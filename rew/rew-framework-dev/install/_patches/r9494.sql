--
-- Added `rewidx_system`.`default_contact_method` to save default contact method used on the registration form
--
ALTER TABLE `rewidx_system`
	ADD `default_contact_method` ENUM('email','phone','text') NOT NULL DEFAULT 'email' AFTER `registration_verify`
;