ALTER TABLE `blog_pings`
	CHANGE `page_title` `page_title` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
	CHANGE `meta_tag_keywords` `meta_tag_keywords` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
	CHANGE `meta_tag_desc` `meta_tag_desc` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
	CHANGE `ip_address` `ip_address` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
;