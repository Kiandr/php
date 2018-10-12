-- `slideshow_images`.`caption`
ALTER TABLE `slideshow_images`
	ADD `caption` TEXT NULL DEFAULT NULL AFTER `image`
;