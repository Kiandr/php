-- Insert FESE Snippets
INSERT INTO `snippets` (`agent`, `name`, `code`, `type`) VALUES
    (1, 'communities', '', 'module'),
    (NULL, 'developments', '', 'module'),
    (1, 'communities-list', '', 'module'),
    (1, 'site-logo-link', '', 'cms'),
    (1, 'site-navigation', '', 'cms'),
    (1, 'site-contact-cta', '', 'cms'),
    (1, 'site-footer-links', '', 'cms'),
    (1, 'site-footer-contact', '', 'cms'),
    (1, 'site-footer-broker', '', 'cms'),
    (1, 'site-phone-number', '', 'cms')
;

-- Update Homepage Settings
UPDATE `default_info` SET `template` = 'cover', `variables` = '{}', `category_html` = '', `footer` = '<a href="/sitemap.php">Sitemap</a>' WHERE `agent` = 1;

-- Remove default IDX registration form text
UPDATE `rewidx_system` SET `copy_register` = '';

-- Make IDX registration forced (Yes, Always)
UPDATE rewidx_system SET registration = 'true';

-- Update content for the /communities.php page
UPDATE `pages` SET `category_html` = '<h1>Local Communities</h1><h2>Most Popular Areas</h2>#communities#' WHERE `file_name` = 'communities';

-- Insert /agents.php Page (uses #agents#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`) VALUES
    (1, 'about', 'agents', 'Our Agents', 'Our Agents', '<h1>Our Agents</h1>#agents#', 'f', 'f')
;

-- Insert /offices.php Page (uses #offices#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
    (2, 'about', 'offices', 'Our Offices', 'Our Offices', '<h1>Our Offices</h1>#offices#', 'f', 'f', 'f')
;

-- Insert /featured-listings.php Page (uses #idx-featured-search#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
    (3, 'about', 'featured-listings', 'Our Featured Listings', 'Our Featured Listings', '<h1>Our Featured Listings</h1>#idx-featured-search#', 'f', 'f', 'f')
;

-- Insert /testimonials.php Page (uses #testimonials#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
    (4, 'about', 'testimonials', 'Testimonials', 'Testimonials', '<h1>Client Testimonials</h1>#testimonials#', 'f', 'f', 'f')
;

-- Insert /developments.php Page (uses #developments#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
    (0, 'developments', 'developments', 'New Project Developments', 'New Developments', '<h1>Developments</h1><h2>New Project Developments</h2>#developments#', 'f', 'f', 'f')
;
