ALTER TABLE `default_info`
	ADD `auto_rotate_days` SET( '0', '1', '2', '3', '4', '5', '6' ) NOT NULL DEFAULT '1,2,3,4,5' AFTER `auto_rotate_agent`,
	ADD `auto_optout_days` SET( '0', '1', '2', '3', '4', '5', '6' ) NOT NULL DEFAULT '1,2,3,4,5' AFTER `auto_rotate_unassign`,
	ADD `auto_optout_hours` SET('0', '1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23') NOT NULL DEFAULT '9,10,11,12,13,14,15,16,17' AFTER `auto_optout_days`
;