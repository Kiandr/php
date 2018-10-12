---
--- Increase field type on id in `keywords` and `users_keywords`
---
ALTER TABLE `users_keywords` DROP FOREIGN KEY `users_keywords_ibfk_3`;
ALTER TABLE `users_keywords` DROP FOREIGN KEY `users_keywords_ibfk_1`;
ALTER TABLE `keywords` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `users_keywords` CHANGE `keyword_id` `keyword_id` INT(10) UNSIGNED DEFAULT 0  NOT NULL;

--
-- Constraints for table `users_keywords`
--
ALTER TABLE `users_keywords`
  ADD CONSTRAINT `users_keywords_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `users_keywords_ibfk_1` FOREIGN KEY (`keyword_id`) REFERENCES `keywords` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
