ALTER TABLE `rewidx_defaults`
	ADD `split` INT UNSIGNED NULL DEFAULT NULL AFTER `page_limit`
;

ALTER TABLE `rewidx_searches`
	ADD `split` INT UNSIGNED NULL DEFAULT NULL AFTER `page_limit`
;