-- Add column for shared documents
ALTER TABLE `docs`
	ADD `share` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER `is_html`
;