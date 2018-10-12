ALTER TABLE  `agents` ADD  `showing_suite_email` VARCHAR( 100 ) NOT NULL COMMENT  'Email address tied to agent''s ShowingSuite account';

ALTER TABLE  `rewidx_details` ADD  `showing_suite` ENUM(  'true',  'false' ) NOT NULL DEFAULT  'true' AFTER  `status_history` ;