-- Insert CE Snippets
INSERT INTO `snippets` (`agent`, `name`, `code`, `type`) VALUES
    (NULL, 'communities', '', 'module'),
    (1, 'featured-agents', '', 'module'),
    (1, 'featured-offices', '', 'module'),
    (1, 'featured-communities', '', 'module'),
    (1, 'enhanced-listings', '', 'module'),
    (1, 'site-logo', '', 'cms'),
    (1, 'site-navigation', '', 'cms'),
    (1, 'site-phone-number', '', 'cms'),
    (1, 'site-email', '', 'cms'),
    (1, 'site-address', '', 'cms'),
    (1, 'site-business-name', '', 'cms'),
    (1, 'footer-column-2', '', 'cms'),
    (1, 'footer-column-3', '', 'cms'),
    (NULL, 'site-signup-cta', '', 'cms'),
    (1, 'social-links', '', 'cms'),
    (NULL, 'ui-styles', '', 'cms')
;

-- Update Homepage Settings
UPDATE `default_info` SET `template` = 'cover', `variables` = '{
 "background":"video",
 "background.video_id":"NRWUoDpo2fo",
 "foreground":"search",
 "foreground.horizontal":"center",
 "foreground.vertical":"middle",
 "foreground.preheading":"home is...",
 "foreground.heading":"A Place for New Beginnings.",
 "foreground.intro":"The hunt for your next home will be a life-changing experience. To get started on your journey, search for listings in your desired community below."
}', `category_html` = '
#featured-communities#<br />
#featured-agents#<br />
#featured-offices#<br />
', `footer` = '<a href="/sitemap.php">Sitemap</a><a href="/privacy-policy.php">Privacy Policy</a>' WHERE `agent` = 1;

-- Remove default IDX registration form text
UPDATE `rewidx_system` SET `copy_register` = '';

-- Update content for the /communities.php page
UPDATE `pages` SET `category_html` = '<h1>Local Communities</h1><h2>Most Popular Areas</h2>#communities#' WHERE `file_name` = 'communities';

-- Update contact page
UPDATE `pages` SET `category_html` = '<h1>Contact Us</h1><div class="columns"><div class="column -width-2/3 -width-1/2@md -width-1/1@sm"><p>#form-contact#</p><p class="footnote">*Your information will never be shared with any third party.</p></div><div class="column -width-1/3 -width-1/2@md -width-1/1@sm"><div class="contact__info"><div class="divider"><span class="divider__label -left">Contact Info</span></div><p><span class="contact__company">#site-business-name#</span> #site-address#</p><p>#site-phone-number#<br /> <a href="mailto:#site-email#">#site-email#</a></p><div class="divider"><span class="divider__label -left">Follow Us</span></div><div class="contact__social">#social-links#</div></div></div></div>' WHERE `file_name` = 'contact';

-- Insert /agents.php Page (uses #agents#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`) VALUES
    (1, 'about', 'agents', 'Our Agents', 'Our Agents', '<h1>Our Agents</h1>#agents#', 'f', 'f')
;

-- Insert /offices.php Page (uses #offices#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
    (2, 'about', 'offices', 'Our Offices', 'Our Offices', '<h1>Our Offices</h1>#offices#', 'f', 'f', 'f')
;

-- Insert /testimonials.php Page (uses #testimonials#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
    (4, 'about', 'testimonials', 'Testimonials', 'Testimonials', '<h1>Client Testimonials</h1>#testimonials#', 'f', 'f', 'f')
;

-- Insert /enhanced-listings.php Page (uses #enhanced-listings#)
INSERT INTO `pages` (`subcategory_order`, `category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
    (4, 'about', 'enhanced-listings', 'Enhanced Listings', 'Enhanced Listings', '#enhanced-listings#', 'f', 'f', 'f')
;

-- Insert /ui.php Page (uses #ui-styles#)
INSERT INTO `pages` (`category`, `file_name`, `page_title`, `link_name`, `category_html`, `hide`, `is_main_cat`, `is_link`) VALUES
    ('test', 'ui-styles', 'UI Style Guide', 'UI Style Guide', '<h1>UI Style Guide</h1><p>The de facto style guide.</p>#ui-styles#', 'f', 'f', 'f')
;

-- Hide default test pages from showing in navigation menu
UPDATE `pages` SET `hide` = 't' WHERE `file_name` IN ('test', 'forms', 'cma');

-- Make IDX registration forced (Yes, Always)
UPDATE rewidx_system SET registration = 'true';