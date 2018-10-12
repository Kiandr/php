--
-- Update `users_searches`.`frequency` To Include 'never':
-- 
ALTER TABLE  `users_searches`
	CHANGE  `frequency` `frequency` ENUM('never', 'daily', 'weekly', 'monthly') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'weekly'
;