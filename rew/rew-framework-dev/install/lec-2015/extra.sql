-- Insert LEC-2015 Snippets
REPLACE INTO `snippets` (`agent`, `name`, `code`, `type`) VALUES
	(1, 'navigation', '', 'cms'),
	(1, 'phone-number', '', 'cms'),
	(1, 'social-share', '', 'cms'),
	(1, 'footer-links', '', 'cms'),
	(1, 'footer-logos', '', 'cms'),
	(1, 'footer-contact', '', 'cms'),
	(1, 'cta-communities', '', 'cms'),
	(1, 'cta-calculator', '', 'cms'),
	(1, 'communities', '', 'module'),
	(NULL, 'form-guaranteed', '', 'form')
;

-- Update Homepage Settings
UPDATE `default_info` SET `template` = 'homepage', `variables` = '{}', `category_html` = '' WHERE `agent` = 1;

-- Update Communities Page Content
UPDATE `pages` SET `category_html` = '<div class="deck text-center">\r\n\t<h1>Browse Communities &amp; Lifestyles</h1>\r\n</div>\r\n<p>Home seekers will enjoy the many different neighborhoods to choose from here, as well as the variety of houses, condos and townhomes available. Depending on where you settle, you could be right downtown, next to a popular community park or near top-rated schools.</p>\r\n<p> We hope that our website''s community information will help you to choose between the many wonderful areas; each has a lot to offer the home buyer.</p>\r\n<p class="highlight"><a href="/contact.php">Contact us</a> anytime for more information about local areas!</p>\r\n<p>#communities#</p>' WHERE `file_name` = 'communities' AND `agent` = 1;

-- Update Registration Form Content
UPDATE `rewidx_system` SET `copy_register` = '<div class="hgroup">\n\t<h1>Sign Up Now for Free!</h1>\n\t<h2>Join others who trust our Home Search.</h2>\n</div>\n<p>Save valuable time in your property search by signing up (it''s free). Save your searches so you don''t have to fill out your search criteria each time you browse the current listings. Best of all, you can choose to receive property updates by email whenever a new listing (matching your needs) comes on the market.</p>';

-- Insert testimonials.php Page (uses #testimonials#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`) VALUES
	(3, 'about', 'testimonials', 'Testimonials', 'Testimonials', '<h1>Client Testimonials</h1>#testimonials#', 'f', 'f')
;

-- Insert offices.php Page (uses #offices#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
	(2, 'about', 'offices', 'Our Offices', 'Our Offices', '<h1>Our Offices</h1>#offices#', 'f', 'f', 'f')
;

-- Insert agents.php Page (uses #agents#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`) VALUES
	(1, 'about', 'agents', 'Our Agents', 'Our Agents', '<h1>Our Agents</h1>#agents#', 'f', 'f')
;

-- Insert featured-listings.php Page (uses #idx-featured-search#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
	(0, 'about', 'featured-listings', 'Our Featured Listings', 'Our Featured Listings', '<h1>Our Featured Listings</h1>#idx-featured-search#', 'f', 'f', 'f')
;

-- Insert form autoresponder (requires ID #14)
INSERT INTO `auto_responders` (`id`, `title`, `from`, `from_name`, `from_email`, `cc_email`, `bcc_email`, `subject`, `document`, `tempid`, `is_html`, `active`) VALUES
	(14, 'Seller Feature', 'agent', '', '', '', '', 'Welcome to the site!', '<p>Hi {first_name} {last_name},</p>\r\n<p>Thank you for requesting our Free Home Evaluation. We will review all of the details that you have provided, and complete a Comparable Market Analysis for your home. This report will help gauge an appropriate asking price for your home, based on current market conditions.</p>\r\n<p>We will be in contact very soon. Take care!</p>', NULL, 'true', 'N')
;