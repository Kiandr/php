-- If lead doesn't have a cell phone # but has a phone # - let's swap them and assume it's a cell #
UPDATE `users` SET `phone_cell` = `phone`, `phone` = '' WHERE `phone_cell` = '' AND `phone` != '';