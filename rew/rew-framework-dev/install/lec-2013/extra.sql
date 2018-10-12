-- Insert LEC-2013 Snippets
INSERT INTO `snippets` (`agent`, `name`, `code`, `type`) VALUES
	(1, 'var-site-name', '', 'cms'),
	(1, 'var-phone-number', '', 'cms'),
	(1, 'lec-navigation', '', 'cms'),
	(1, 'lec-message', '', 'cms'),
	(1, 'lec-sidebar', '', 'cms'),
	(1, 'lec-footer', '', 'cms'),
	(1, 'social-share', '', 'cms');
	
-- Update Homepage Settings
UPDATE `default_info` SET `template` = 'homepage', `variables` = '{}' WHERE `agent` = 1;
	
-- Insert featured-listings.php Page (uses #idx-featured-search#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
	(0, 'about', 'featured-listings', 'Our Featured Listings', 'Our Featured Listings', '<h1>Our Featured Listings</h1>#idx-featured-search#', 'f', 'f', 'f');

-- Insert agents.php Page (uses #agents#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
	(1, 'about', 'agents', 'Our Agents', 'Our Agents', '<h1>Our Agents</h1>#agents#', 'f', 'f', 'f');

-- Insert offices.php Page (uses #offices#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
	(2, 'about', 'offices', 'Our Offices', 'Our Offices', '<h1>Our Offices</h1>#offices#', 'f', 'f', 'f');

-- Insert testimonials.php Page (uses #testimonials#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
	(3, 'about', 'testimonials', 'Testimonials', 'Testimonials', '<h1>Client Testimonials</h1>#testimonials#', 'f', 'f', 'f');
	
-- Insert Default Testimonials
INSERT INTO `testimonials` (`client`, `testimonial`) VALUES 
	('Jane & John Doe', 'We can''t thank you enough for all your hard work. Our new home is everything we hoped it would be, and the price you negotiated was great! We wouldn''t hesitate to recommend you to friends and family, and will definitely use your services for our next real estate transaction.'),
	('', 'Your website was the ONLY resource we found that could meet our needs as we searched for a home. The polygon search tool allowed us to narrow our hunt to the most relevant neighborhoods, and your "Communities" content was the only truly honest material we could find. <strong>Thank you SO much.</strong>');
	
-- Update IDX Defaults
UPDATE `rewidx_defaults` SET
	`split` = 9, 
	`panels` = 'a:13:{s:7:"polygon";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:6:"radius";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:6:"bounds";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:8:"location";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"city";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"type";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:5:"price";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:8:"bedrooms";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:9:"bathrooms";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:7:"address";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:3:"zip";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:3:"mls";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:8:"features";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}'
WHERE `idx` = '';