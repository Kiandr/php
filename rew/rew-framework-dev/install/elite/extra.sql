-- Insert ELITE Snippets
INSERT INTO `snippets` (`agent`, `name`, `code`, `type`) VALUES
	(1, 'communities', '', 'module'),
	(1, 'cta-feature', '', 'cms'),
	(1, 'ctas-homepage', '', 'cms'),
	(1, 'footer-quote', '', 'cms'),
	(NULL, 'form-guaranteed', '', 'form'),
	(1, 'mobile-navigation', '', 'cms'),
	(1, 'navigation', '', 'cms'),
	(1, 'phone-number', '', 'cms'),
	(1, 'popular-pages', '', 'cms'),
	(1, 'useful-links', '', 'cms'),
	(1, 'company-info', '', 'cms'),
	(1, 'phone-number-mobile-icon', '', 'cms')
;

-- Update Homepage Settings
UPDATE `default_info` SET `template` = 'homepage', `variables` = '{}', `category_html` = '' WHERE `agent` = 1;

-- Set 'detailed' as default search results view, and remove default registration page text
UPDATE `rewidx_defaults` SET `view` = 'detailed';
UPDATE `rewidx_system` SET `copy_register` = '';

-- Insert testimonials Page
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
	(3, 'about', 'testimonials', 'Testimonials', 'Testimonials', '', 't', 'f', 'f')
;

-- Insert neighborhoods Page
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
	(4, 'neighborhoods', 'neighborhoods', 'Neighborhoods', 'Neighborhoods', '', 't', 'f', 'f')
;

-- Insert guaranteed.php Page (uses #form-guaranteed#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
	(3, 'guaranteed', 'guaranteed', 'Guaranteed Sold', 'Guaranteed Sold', '', 't', 'f', 'f')
;

-- Insert offices.php Page (uses #offices#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
	(2, 'about', 'offices', 'Our Offices', 'Our Offices', '', 'f', 'f', 'f')
;

-- Insert agents.php Page (uses #agents#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`) VALUES
	(1, 'about', 'agents', 'Our Agents', 'Our Agents', '', 'f', 'f')
;

-- Insert featured-listings.php Page (uses #idx-featured-search#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
	(0, 'about', 'featured-listings', 'Our Featured Listings', 'Our Featured Listings', '', 'f', 'f', 'f')
;

-- Insert rate-sellers form autoresponder Autoresponder (uses id 13)
INSERT INTO `auto_responders` (`id`, `title`, `from`, `from_name`, `from_email`, `cc_email`, `bcc_email`, `subject`, `document`, `tempid`, `is_html`, `active`) VALUES
	(13, 'Guaranteed Sold Form', 'agent', '', '', '', '', 'Welcome to the site!', '<p>Hi {first_name} {last_name},</p><p>Thank you for applying for our Guaranteed Sold program! We will review all of the details that you have provided, and determine your eligibility for the program.</p><p>Thanks</p>', NULL, 'true', 'Y')
;

-- Insert Default Testimonials
INSERT INTO `testimonials` (`client`, `testimonial`, `link`) VALUES
	('Jane & John Doe', 'We can''t thank you enough for all your hard work. Our new home is everything we hoped it would be, and the price you negotiated was great! We wouldn''t hesitate to recommend you to friends and family, and will definitely use your services for our next real estate transaction.', 'http://www.google.com'),
	('', 'Your website was the ONLY resource we found that could meet our needs as we searched for a home. The polygon search tool allowed us to narrow our hunt to the most relevant neighborhoods, and your "Communities" content was the only truly honest material we could find. <strong>Thank you SO much.</strong>', 'http://www.youtube.com')
;

-- Add sample office and link it to the super admin
INSERT INTO `featured_offices` (`title`, `phone`, `fax`, `address`, `city`, `state`, `zip`, `display`, `sort`) VALUES
	('Real Estate Webmasters', '(250) 753-9893', '(250) 753-7209', '223 Commercial Street', 'Nanaimo', 'British Columbia', 'V9R 5G8', 'Y', 0);
UPDATE `agents` SET `office` = LAST_INSERT_ID() WHERE `office` IS NULL AND `id` = 1;
