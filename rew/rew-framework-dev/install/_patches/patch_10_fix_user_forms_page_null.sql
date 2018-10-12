-- Fixes a bug when an insert was made to users_forms.page with a null
-- value it would fail. For example when running in CLI mode or when
-- $_SERVER['HTTP_REFERER'] is empty.
-- https://github.com/Real-Estate-Webmasters/rew-framework/pull/1176

ALTER TABLE `users_forms` MODIFY COLUMN `page` LONGTEXT NULL;
