-- Drop old columns
ALTER TABLE `directory_listings`
  DROP `images`,
  DROP `logo`;
  
-- Add new columns
ALTER TABLE `directory_settings`
  ADD `sitemap` enum('cat','list') NOT NULL default 'cat' AFTER `hide_intro`;
  
-- Add snippet type
ALTER TABLE  `snippets` 
  CHANGE  `type`  `type` ENUM('cms','idx','form','module','directory','old' ) NOT NULL DEFAULT  'cms';
  
-- Add directory snippets
INSERT INTO `snippets` (`agent`, `name`, `code`, `type`) VALUES
(1, 'business-directory-add-listing-formatting', 'Please note that if you want to use separate paragraphs (line breaks) in here, you will need to <a href="/contact.php">contact us</a> with your desired formatting.', 'cms'),
(1, 'business-directory-add-listing-page', '<div class="msg notice">\r\n    <p><strong>Attention:</strong> We moderate all listing submissions. If your listing is not meant to be <strong>useful for our local audience</strong>, it will not be approved, so please don''t waste your time. If your business is legitimately local but we think your listing is intended only for the "backlink", we will put a "nofollow" attribute on your link so that search engine benefits do not apply.</p>\r\n</div>', 'cms'),
(1, 'business-directory-intro', '<div class="msg notice">\r\n    <p>If you know of a business or organization that should appear in our directory, please <a href="/directory/add/">add it</a>!</p>\r\n</div>', 'cms');
