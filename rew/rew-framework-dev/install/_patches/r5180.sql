-- Added `blog_settings`.`captcha` to toggle CAPTCHA for Blog Comments
ALTER TABLE `blog_settings`
	ADD `captcha` ENUM('t', 'f') NOT NULL DEFAULT 'f' AFTER `hide_slideshow`
;