SET @AGENT_PASSWORD = ENCRYPT('3UjG8egS');
SET @LENDER_PASSWORD = ENCRYPT('2uWuxvUP');

DELETE FROM `auth` WHERE `username` RLIKE '^(agent|associate|lender)[0-9]$';
DELETE FROM `agents` WHERE `email` LIKE 'demoagent%@rewdemo.com';
DELETE FROM `lenders` WHERE `email` LIKE 'demolender%@rewdemo.com';
DELETE FROM `associates` WHERE `email` LIKE 'demoassociate%@rewdemo.com';
DELETE FROM `users` WHERE `email` LIKE 'demolead%@rewdemo.com';
DELETE FROM `groups` WHERE `timestamp` = '0000-00-00 00:00:00';
DELETE FROM `groups` WHERE `name` IN ('Buyer Leads', 'Seller Leads');
DELETE FROM `action_plans` WHERE `name` IN ('First Contact', 'Home Buyer Plan', 'Home Seller Plan');
DELETE FROM `campaigns` WHERE `name` IN ('Buyer Leads', 'Home Renters', 'Home Purchase');
DELETE FROM `docs_categories` WHERE `name` IN ('Buyer Lead Emails', 'Renters Campaign Emails', 'Home Purchase Emails');
DELETE FROM `docs_categories` WHERE `name` IN ('Email Response Templates', 'Financing', 'Rentals', 'Selling', 'Real Estate Information', 'Introduction Emails');
DELETE FROM `docs_templates` WHERE `timestamp` = '0000-00-00 00:00:00';
DELETE FROM `cms_files` WHERE `type` = 'text/plain' AND `data` = '';
DELETE FROM `featured_offices` WHERE `email` LIKE 'demooffice%@rewdemo.com';
DELETE FROM `pages_rewrites` WHERE `old` = '/property-valuation.php' AND `new` = '/cma.php';
DELETE FROM `featured_communities` WHERE `timestamp_created` = '0000-00-00 00:00:00';
DELETE FROM `blog_categories` WHERE `timestamp_created` = '0000-00-00 00:00:00';
DELETE FROM `blog_entries` WHERE `timestamp_created` = '0000-00-00 00:00:00';
DELETE FROM `blog_links` WHERE `timestamp_created` = '0000-00-00 00:00:00';
DELETE FROM `api_applications` WHERE `name` = 'REW Demo API Key';
DELETE FROM `teams` WHERE `timestamp` = '0000-00-00 00:00:00';
DELETE FROM `_listings` WHERE `link` LIKE '%-sample-%';
DELETE FROM `pages` WHERE `file_name` IN ('east-nashville', 'hendersonville', 'mount-juliet', 'hillsboro-village', 'green-hills', 'murfreesboro', 'idx-snippets', 'idx-snippet', 'polygon-snippet', 'radius-snippet', 'demos', 'cover-video', 'cover-360', 'cover-photo', 'cover-slideshow', 'cover-panoramic');
DELETE FROM `snippets` WHERE `name` IN ('idx-east-nashville', 'idx-hendersonville', 'idx-mount-juliet', 'idx-hillsboro-village', 'idx-green-hills', 'idx-murfreesboro', 'idx-snippet', 'polygon-snippet', 'radius-snippet');
DELETE FROM `cms_uploads` WHERE `file` IN ('associate-1.png', 'associate-2.png', 'associate-3.png', 'community-1-1.jpg', 'community-1-2.jpg', 'community-1-3.jpg', 'community-2-1.jpg', 'community-3-1.jpg', 'community-4-1.jpg', 'community-5-1.jpg', 'community-6-1.jpg', 'cover-360.jpg', 'cover-panorama.jpg', 'cover-photo.jpg', 'cover-slideshow-1.jpg', 'cover-slideshow-2.jpg', 'cover-slideshow-3.jpg', 'lender-1.png', 'lender-2.png', 'lender-3.png', 'listing-1-1.jpg', 'listing-1-2.jpg', 'listing-1-3.jpg', 'listing-2-1.jpg', 'listing-2-2.jpg', 'listing-2-3.jpg', 'listing-3-1.jpg', 'listing-3-2.jpg', 'listing-3-3.jpg');

-- -------------
-- Site Settings
-- -------------

-- Google Maps API Key
INSERT INTO `api_applications` (`name`, `api_key`, `enabled`, `timestamp`) VALUES
  ('REW Demo API Key', '0185eee809c2e8ca0db74a4033a00cf238cdc07b3021db66cb91181fbb2231e5', 'Y', NOW())
;

-- Google Maps API Key
INSERT IGNORE INTO `settings` SET `value` = 'AIzaSyBXXr6Rs3b_GJiA1ni-JBofLs2oWtXjRxI', `name` = 'google.maps.api_key';

-- Google Analytics
UPDATE `agents` SET `network_google` = '{
  "token":"1\/HmRGSfthnh_crXtaZvTJrn6Ycy_smBNBufAs9R1Gwrw",
  "secret":"j0231_VYj5FRmBM-vJzxp_ru"
}' WHERE `id` = 1;

-- Partner Integrations
UPDATE `agents` SET `partners` = '{
  "grasshopper":{
    "api_key":"U8YhUsEDUBe7amaZyjaQa9YbAjUreQUG",
    "username":"mayzes.scott@realestatewebmasters.com",
    "password":"Tha5awef",
    "user_key":"hNnFHr7Xj1uYUe4gBOcplqVywMx2C3Wd",
    "user_code":"4e4fc273bfab2ac2a5b8a8d087c58aac"
  },
  "bombbomb":{
    "api_key":"4efc6cac-6aa8-c9ff-27e6-330d764089a3",
    "list_id":"a3a5a1f9-e6e1-cbfc-f62b-83a3c377c33b"
  },
  "followupboss":{
    "api_key":"6eac103dc1413ee2ce45797925fdf0215e5aa2"
  },
  "wiseagent":{
    "api_key":"XEVrH19XBcKEobF30Nrf1qtJXmVXgzoJ"
  }
}' WHERE `id` = 1;

-- Facebook API Key
UPDATE `default_info` SET `facebook_apikey` = '265889470180465', `facebook_secret` = '09432bb5242bc96c6e6343d1af95121a';

-- Google+ API Key
UPDATE `default_info` SET `google_apikey` = '965353939946-gmro1nuetpqq6lob1jgpk9qqdho53m5b.apps.googleusercontent.com', `google_secret` = '_chHWR-qDS_4j1VKF-D-9fZv';

-- Microsoft Live API Key
UPDATE `default_info` SET `microsoft_apikey` = '00000000400E0939', `microsoft_secret` = ' ApaJEBqBrQubi9hzcH6-LJHWVowzwwii';

-- LinkedIn API Key
UPDATE `default_info` SET `linkedin_apikey` = 'f9t8bq3yt4fl', `linkedin_secret` = 'uRMPdGjvPwsy2XWv';

-- Twitter API Key
UPDATE `default_info` SET `twitter_apikey` = 'TfU7DfSSSXPmmFT8Jz6DQ', `twitter_secret` = 'sEGj5tvvd5aGmZrYu757pYwnYcaPayZk3akmGUTzhJ8';

-- Yahoo! API Key
UPDATE `default_info` SET `yahoo_apikey` = 'dj0yJmk9MUlYd1lxZUZpRU5RJmQ9WVdrOVIxVkxVV0pyTldjbWNHbzlPRGd4TmpNd05EWXkmcz1jb25zdW1lcnNlY3JldCZ4PWFj', `yahoo_secret` = 'ae07008156ae81a3529f5b0f47828f8ef20559d3';

-- Registration Settings
UPDATE `rewidx_system` SET `registration` = 'optional';

-- -------
-- Offices
-- -------
INSERT INTO `featured_offices` SET `display` = 'Y', `image` = 'vision-austin.jpg', `title` = 'Vision Austin', `description` = 'If you are looking to sell your home or find homes for sale, we are able to assist you in many different locations.', `email` = 'demooffice1@rewdemo.com', `phone` = '(718) 749-7001', `fax` = '', `address` = '223 Commercial Street', `city` = 'Austin', `state` = 'TX', `zip` = '78708';
INSERT INTO `featured_offices` SET `display` = 'Y', `image` = 'vision-montreal.jpg', `title` = 'Vision Montreal', `description` = 'Our Montreal agents have extensive knowledge and experience in selling and buying real estate in Montreal.', `email` = 'demooffice2@rewdemo.com', `phone` = '(718) 749-7001', `fax` = '', `address` = '8899 Third Avenue', `city` = 'Montreal', `state` = '', `zip` = '11209';
INSERT INTO `featured_offices` SET `display` = 'Y', `image` = 'vision-new-york.jpg', `title` = 'Vision New York', `description` = 'Our Flagship New York office, opened in 2011, conveniently located in a heritage building in Hell\'s Kitchen.', `email` = 'demooffice3@rewdemo.com', `phone` = '(512) 892-0708', `fax` = '(512) 308-1441', `address` = '4103 William Cannon Drive', `city` = 'New York', `state` = 'NY', `zip` = '78749';
INSERT INTO `featured_offices` SET `display` = 'Y', `image` = 'vision-seattle.jpg', `title` = 'Vision Seattle', `description` = 'We have been assisting many happy buyers finding their home overseas for over 15 years.', `email` = 'demooffice4@rewdemo.com', `phone` = '(206) 443-2001', `fax` = '', `address` = '324 Terminal Avenue', `city` = 'Seattle', `state` = 'WA', `zip` = '98101';

-- -------------------------
-- First Contact Action Plan
-- -------------------------

-- Create "First Contact" Plan
INSERT INTO `action_plans` SET `name` = 'First Contact', `description` = 'Contact all new leads.', `style` = 'red', `day_adjust` = '0,1,2,3,4,5,6', `timestamp_created` = NOW();
SET @action_plan_id = LAST_INSERT_ID();

-- Add "First Contact" Tasks
INSERT INTO `tasks` (`actionplan_id`, `offset`, `automated`, `type`, `name`, `info`, `time`, `expire`, `timestamp_created`, `timestamp_updated`) VALUES
  (@action_plan_id, 0, 'N', 'Email', 'First email', '', '00:00:00', 1, NOW(), NOW()),
  (@action_plan_id, 0, 'N', 'Call', 'First phone call', '', '00:00:00', 1, NOW(), NOW()),
  (@action_plan_id, 0, 'N', 'Text', 'First text message', '', '00:00:00', 1, NOW(), NOW()),
  (@action_plan_id, 1, 'N', 'Search', 'Create a saved search', '', '18:30:00', 1, NOW(), NOW()),
  (@action_plan_id, 1, 'N', 'Listing', 'Send recommended listings', '', '18:30:00', 1, NOW(), NOW()),
  (@action_plan_id, 2, 'N', 'Text', 'Follow up text message', '', '20:00:00', 1, NOW(), NOW()),
  (@action_plan_id, 2, 'N', 'Call', 'Follow up phone call', '', '20:00:00', 1, NOW(), NOW()),
  (@action_plan_id, 2, 'N', 'Email', 'Follow up email', '', '20:00:00', 1, NOW(), NOW())
;

-- ----------------------
-- Home Buyer Action Plan
-- ----------------------

-- Create "Home Leads" Group
INSERT INTO `groups` (`agent_id`, `name`, `description`, `style`, `user`) VALUES (NULL, 'Buyer Leads', '', 'purple', 'false');
SET @buyer_group_id = LAST_INSERT_ID();

-- Create "Home Buyer" Plan
INSERT INTO `action_plans` SET `name` = 'Home Buyer Plan', `description` = 'Action plan for home buyer leads.', `style` = 'lime', `timestamp_created` = NOW();
SET @action_plan_id = LAST_INSERT_ID();

-- Add Automated Task
INSERT INTO `tasks` SET `actionplan_id` = @action_plan_id, `offset` = 0, `automated` = 'Y', `type` = 'Group', `name` = 'Add to buyers group', `info` = '', `timestamp_created` = NOW(), `timestamp_updated` = NOW();
INSERT INTO `tasks_groups` SET `task_id` = LAST_INSERT_ID(), `group_id` = @buyer_group_id;

-- Add "Home Buyer" Tasks
INSERT INTO `tasks` (`actionplan_id`, `offset`, `automated`, `type`, `name`, `info`, `time`, `expire`, `timestamp_created`, `timestamp_updated`) VALUES
  (@action_plan_id, 1, 'N', 'Email', 'Introduction email', '', '00:00:00', 1, NOW(), NOW()),
  (@action_plan_id, 1, 'N', 'Call', 'Introduction call', '', '00:00:00', 1, NOW(), NOW()),
  (@action_plan_id, 2, 'N', 'Search', 'Create a saved search', '', '18:30:00', 1, NOW(), NOW()),
  (@action_plan_id, 5, 'N', 'Listing', 'Send listing recommendations', '', '18:30:00', 1, NOW(), NOW()),
  (@action_plan_id, 7, 'N', 'Call', 'Follow up call', '', '20:00:00', 1, NOW(), NOW()),
  (@action_plan_id, 7, 'N', 'Email', 'Follow up email', '', '20:00:00', 1, NOW(), NOW())
;

-- -----------------------
-- Home Seller Action Plan
-- -----------------------

-- Create "Seller Leads" Group
INSERT INTO `groups` (`agent_id`, `name`, `description`, `style`, `user`) VALUES (NULL, 'Seller Leads', '', 'blue', 'false');
SET @seller_group_id = LAST_INSERT_ID();

-- Create "Home Seller" Plan
INSERT INTO `action_plans` SET `name` = 'Home Seller Plan', `description` = 'Action plan for home seller leads.', `style` = 'seafoam', `timestamp_created` = NOW();
SET @action_plan_id = LAST_INSERT_ID();

-- Add Automated Task
INSERT INTO `tasks` SET `actionplan_id` = @action_plan_id, `offset` = 0, `automated` = 'Y', `type` = 'Group', `name` = 'Add to sellers group', `info` = '', `timestamp_created` = NOW(), `timestamp_updated` = NOW();
INSERT INTO `tasks_groups` SET `task_id` = LAST_INSERT_ID(), `group_id` = @seller_group_id;

-- Add "Home Seller" Tasks
INSERT INTO `tasks` (`actionplan_id`, `offset`, `automated`, `type`, `name`, `info`, `time`, `expire`, `timestamp_created`, `timestamp_updated`) VALUES
  (@action_plan_id, 1, 'N', 'Email', 'Introduction email', '', '00:00:00', 1, NOW(), NOW()),
  (@action_plan_id, 1, 'N', 'Call', 'Introduction call', '', '00:00:00', 1, NOW(), NOW()),
  (@action_plan_id, 2, 'N', 'Listing', 'Create saved listing', '', '18:30:00', 1, NOW(), NOW()),
  (@action_plan_id, 5, 'N', 'Call', 'Follow up call', '', '20:00:00', 1, NOW(), NOW()),
  (@action_plan_id, 7, 'N', 'Email', 'Follow up email', '', '20:00:00', 1, NOW(), NOW())
;

-- --------------------
-- Buyer Leads Campaign
-- --------------------

-- Create "Buyer Leads" Campaign
INSERT INTO `campaigns` SET `name` = 'Buyer Leads', `description` = 'Drip email campaign for buyer leads.', `sender` = 'agent';
SET @campaign_id = LAST_INSERT_ID();

-- Campaign's "Buyer Lead" Group
INSERT INTO `campaigns_groups` SET `campaign_id` = @campaign_id, `group_id` = @buyer_group_id;

-- Campaign's Document Category
INSERT INTO `docs_categories` SET `name` = 'Buyer Lead Emails', `description` = 'Form letters used in the buyer leads campaign.';
SET @cat_id = LAST_INSERT_ID();

-- Campaign Email #1
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '1 - Introduction Email', `document` = '<p>Hello {first_name},</p>\n<p>You submitted a request to search properties on <a href="http://www.rewdemo.com/">www.rewdemo.com</a>. Just wanted to follow up and see if you had any questions or if there were any properties you would like to view.</p>\n<p>To search again, visit: <a href="http://www.rewdemo.com/">www.rewdemo.com</a></p>\n<p>My goal is to provide you the easiest and most effective way for you to find your next home. If there is anything that I can do to help, please do not hesitate to call me at 1 (877) 753-9893.</p>\n<p>&nbsp;</p>\n<p>{signature}</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 1, `subject` = 'Thank you for visiting!';

-- Campaign Email #2
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '2 - See the latest listings!', `document` = '<p>Hi {first_name}</p>\n<p>Curious to see what''s new on the market? Check out the latest homes for sale to list in the area by <a href="http://www.rewdemo.com">clicking here,</a> where I routinely update the most recent listings and offer comprehensive details for each one: everything from asking price, to home dimensions, to property history.</p>\n<p>&nbsp;</p>\n<p>{signature}</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 2, `subject` = 'See the latest listings!';

-- Campaign Email #3
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '3 - Top 5 Property Types in The City', `document` = '<p>Hi {first_name}</p>\n<p>There are countless unique homes for sale. To get an idea of what the most popular types are in the local market, <a href="http://www.rewdemo.com.com/idx/">check out the latest homes for sale</a> on our website.&nbsp;</p>\n<p>There, you''ll get to see some of the most beautiful residences up for sale.&nbsp;</p>\n<p>&nbsp;</p>\n<p>{signature}</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 3, `subject` = 'Top 5 Property Types in The City';

-- Campaign Email #4
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '4 - More About The City That I Love', `document` = '<p>Hi {first_name},</p>\n<p>In addition to some of the most expansive and gorgeous residences you''ll see in the entire state, the city is also home to much much more.</p>\n<p>The community is rich with great people, successful businesses, boundless entertainment, and beautiful scenery.&nbsp;</p>\n<p>Find out what I love most about <a href="http://www.rewdemo.com/communities.php">the city here.&nbsp;</a></p>\n<p>&nbsp;</p>\n<p>{signature}&nbsp;</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 7, `subject` = 'More About The City That I Love!';

-- Campaign Email #5
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '5 - The Ultimate Guide to Home Buying', `document` = '<p>Hi {first_name},</p>\n<p>Whether you''re an experience home buyer or a first-timer checking out the market, <a href="http://www.rewdemo.com/first-time-home-buying.php">this comprehensive guide&nbsp;can ensure you navigate the process with ease: </a>from organizing your personal finances, to learning where you can find the best homes for sale online.&nbsp;</p>\n<p>&nbsp;</p>\n<p>{signature}</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 14, `subject` = 'The Ultimate Guide to Home Buying';

-- Campaign Email #6
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '6 - It''s been a while! Are you still looking?', `document` = '<p>Hi {first_name},</p>\n<p>Are you still looking? Let me know, I don''t want to keep bothering you if you aren''t.&nbsp;</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 30, `subject` = 'It''s Been a While! Are You Still Looking?';

-- --------------------
-- Home Renter Campaign
-- --------------------

-- Create "Home Renters Campaign" Campaign
INSERT INTO `campaigns` SET `name` = 'Home Renters', `description` = 'Send this campaign to all renters.', `sender` = 'agent';
SET @campaign_id = LAST_INSERT_ID();

-- Campaign's Document Category
INSERT INTO `docs_categories` SET `name` = 'Renters Campaign Emails', `description` = 'Form letters used in the renters campaign.';
SET @cat_id = LAST_INSERT_ID();

-- Campaign Email #1
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '1 - Advanced Home Search Tools', `document` = '<p>Hi {first_name},</p>\n<p>Thank you again for visiting our site! Make sure you don''t miss the advanced search tools available for free on the site. For instance, you can search for properties by Area, Map, Zip Code, or MLS Number. If you see a property that you like, click on its thumbnail picture or hit the View Details button to see additional photographs and a detailed description of the home.</p><p>If you need any help with your home search, please don''t hesitate to call or email us. We''ll be happy to help.</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 1, `subject` = 'Advanced home search tools';

-- Campaign Email #2
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '2 - Determine Your Needs', `document` = '<p>Hello {first_name},</p>\n<p>Finding a home can be overwhelming if you don''t have a clear idea of what you''re looking for. Do you want to live in the city or out in the suburbs? Are you looking for a detached property or perhaps a condominium? How many bedrooms and bathrooms will you need? What about a yard, or essential amenities in the area? Once you''ve come up with a list of requirements, you can save time by saving your preferred search parameters.</p><p>Need a hand with your search? Call or email us for personalized assistance.</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 3, `subject` = 'Determine your needs and wants';

-- Campaign Email #3
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '3 - Bookmark Your Favorites', `document` = '<p>Hi {first_name},</p>\n<p>Did you know that you can bookmark your favorite properties for quick viewing later? When you''re looking at a property''s Details page, you''ll see a button that says Add to Favorites. Click the button, and that property will be instantly saved to your own hidden Favorites folder on the site.</p><p>Have questions about a particular property? Feel free to call or email us any time.</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 5, `subject` = 'It pays to bookmark your favorites!';

-- Campaign Email #4
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '4 - Looking for More', `document` = '<p>{first_name},</p>\n<p>Are you looking for more than just property listings? Would you like to learn about neighborhoods in the local real estate market? We have tons of great information about market conditions, nearby amenities, local schools, and much more in our Featured Areas section. Simply select the community or neighborhood you''d like to read more about, or call us for the inside scoop. We''ll be happy to share our knowledge of the area with you.</p><p>{signature}</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 10, `subject` = 'More than just a house';

-- Campaign Email #5
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '5 - Ready to Look', `document` = '<p>Hi {first_name},</p>\n<p>Have you spotted any listings you like? If so, you can easily arrange to view it in person by clicking Request a Showing from a property''s Details page. You''ll be taken to a brief form where you can post your preferred dates and times, or ask questions about the property. We''ll be in contact with you as soon as possible to schedule your tour!</p>\n<p>Take care,</p>\n<p>{signature}</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 15, `subject` = 'Ready to look?';

-- Campaign Email #6
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '6 - Financing Options', `document` = '<p>Hi {first_name},</p>\n<p>Have you investigated your financing options yet? If you''re in the early stages of your home search, you may not have thought seriously about shopping for a mortgage, but knowing how much you can afford is a critical part of your search.</p><p>To learn about your financing options, call or email us any time.</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 30, `subject` = 'Have you considered buying?';

-- Campaign Email #7
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '7 - Why Use a REALTOR', `document` = '<p>Hello {first_name},</p>\n<p>Are you thinking about leasing a home or apartment without the help of a real estate agent? Did you know that as a tenant, you pay nothing for a REALTOR''s* services? We have years of experience in the local real estate market, so we can recommend neighborhoods that would best suit your needs, provide you with local market stats and other information that isn''t available to non-REALTORS*. As soon as new rental listings enter the market, we will notify you so you can get a jump on the competition, and when you find a property that you like, we will negotiate the best price for you.</p>\n<p>Call or email us today to start a working relationship.</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 45, `subject` = 'Why use a REALTOR&reg;?';

-- Campaign Email #8
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '8 - Still Looking?', `document` = '<p>{first_name},</p>\n<p>Thanks again for visiting our website. I hope that you have been successful in locating a home or apartment. Just wanted to let you know I''m here to help if you need my services.</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 60, `subject` = 'Still Looking?';

-- Campaign Email #9
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = '9 - Ready to Help', `document` = '<p>Hi {first_name},</p>\n<p>By now you have most likely found a place to live. I would like to stay in touch with you to help you with your future real estate needs, whether that will be finding another place to lease or perhaps moving into homeownership.</p>\n<p>Either way, I''m here to help. Let me know if I can be of assistance.</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 90, `subject` = 'Ready to help!';

-- ----------------------
-- Home Purchase Campaign
-- ----------------------

-- Create "Home Purchase" Campaign
INSERT INTO `campaigns` SET `name` = 'Home Purchase', `description` = '', `sender` = 'agent';
SET @campaign_id = LAST_INSERT_ID();

-- Campaign's Document Category
INSERT INTO `docs_categories` SET `name` = 'Home Purchase Emails', `description` = 'Form letters used in the home purchase campaign.';
SET @cat_id = LAST_INSERT_ID();

-- Campaign Email #1
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Congratulations On Your Purchase', `document` = '<p>I wanted to take a moment and say congratulations on your new purchase!</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 1, `subject` = 'Congratulations - You Bought a Beautiful Home!';

-- Campaign Email #2
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Are You All Settled?', `document` = '<p>I just wanted to check in with you and see if you had any questions at this point? If there is anything I can help you with please don''t hesitate to hit the reply button and let me know, I''m here to help you and make this as hassle and stress free as possible. </p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 10, `subject` = 'Are You All Settled In?';

-- Campaign Email #3
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Just Checking In', `document` = '<p>Hello {first_name},</p>\n<p>I just wanted to see how you were doing.</p>\n<p>I look forward to hearing the latest from you.<br /><br />Regards,</p>\n<p>{signature}</p>';
INSERT INTO `campaigns_emails` SET `campaign_id` = @campaign_id, `doc_id` = LAST_INSERT_ID(), `send_delay` = 40, `subject` = 'Just checking in!';

-- -------------------
-- Sample Form Letters
-- -------------------

INSERT INTO `docs_categories` SET `name` = 'Email Response Templates', `description` = 'Miscellaneous email responses.';
SET @cat_id = LAST_INSERT_ID();

INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Appointment Confirmation', `document` = '<p>{first_name},</p><p>Just a note to confirm our appointment on <b>DATE</b> at <b>TIME</b>. If you need to reschedule, please contact me as soon as possible.</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Attached Buyer Presentation', `document` = '<p>{first_name},</p>\n<p>I look forward to helping you find the perfect home! That''s why I''ve attached a&nbsp;list of properties matching your preferences for area, style of home, price, etc.</p>\n<p>The details include:</p>\n<p>- a short description of each property</p>\n<p>- a Comparative Market Analysis that lets you compare features across all selected properties</p>\n<p>- an ownership analysis of the charges and payments required to purchase</p>\n<p>If one of the featured properties catches your interest, I''d be happy to help you prepare an offer. If you have other preferences that aren''t reflected in this selection of houses, please let me know. I can make the necessary adjustments and present you with a new report.</p>\n<p>&nbsp;</p>\n<p>As I cannot guarantee the continued availability of these properties for long, I urge you to look through this report as soon as you can. I look forward to finding the perfect home for you!</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Listing Agent & Buyer Agent', `document` = '<p>Tried you phone earlier but no answer.</p>\n<p>From your request it appears you want to get the market selling price for the home you own.</p>\n<p>Are you planning on selling that one soon and buying another?</p>\n<p>The reason I ask is that we have agents on our team that specialize in representing the seller on a home and looking out for their best interests.</p>\n<p>We also have agents that specializing in representing the buyer when buying a home to look out for the buyer'' best interests.</p>\n<p>As an example, if you want to sell your current home you would need a listing agent specialist.</p>\n<p>When you are buying another home you would need a buyer''s agent to represent you and look out for your best interests.</p>\n<p>I am a buyer''s agent and can represent you on the purchase on a new home.</p>\n<p>I can refer your contact information over to our listing agent department to get with you to discuss the possible sale of you current home.</p>\n<p>Let me know.</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'First Time Owner', `document` = '<p>{first_name},</p>\n<p>If you''re at all interested in owning your own home, you owe it to yourself to take a serious look at the possibilities. Let me help you. I have many tools at my disposal, such as:</p>\n<p>&nbsp;</p>\n<p>- Mortgage calculators, to calculate a payment schedule based on the current numbers</p>\n<p>- Rent vs. Buy Calculator, to estimate the costs of renting vs. buying. You might be surprised!</p>\n<p>- Loan Comparisons, to see which scenario is most suited to you.</p>\n<p>&nbsp;</p>\n<p>Owning your first home can be both daunting and rewarding. I can help you make it a rewarding experience. Please give me a call and we can set up an appointment.</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Property For You', `document` = '<p>{first_name},</p>\n<p>I''ve found a property that I''m sure will interest you. Click the link below to view. Please call me to arrange a time to view it. If you are interested in any other properties, please send me the address or listing number and I''ll be happy to provide you with additional information.</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Thank you for meeting', `document` = '<p>{first_name},</p>\n<p>Thank you for meeting with me today! I''m looking forward to working with you. If I can answer any other questions you may have, please don''t hesitate to call me.</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Resume Your Home Search', `document` = '<p>{first_name},</p>\n<p>I''m here for you when you are ready to resume your home search.</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Still in the market?', `document` = '<p>Hello,</p>\n<p>I''ve tried to contact you a few times in the past and haven''t been able to connect with you.</p>\n<p>I would be honored to have you as a customer but do not want to pester you with endless emails &amp; voice messages.</p>\n<p>Please simply reply to this email with just one of the following words typed in the subject line to help me understand how or if I should continue to follow up with you.</p>\n<p>In - I am still "In" the market for a home. I just haven''t had time to call or email back. Feel free to contact me ASAP.</p>\n<p>Out - I have decided not to buy, so I''m, "Out" of the market for now.</p>\n<p>Bought - Tough Luck, I "Bought" a home already.</p>\n<p>Jump - Leave me alone; go "Jump" in a lake and don''t contact me again unless you are giving away new homes :-)</p>\n<p>Thank you for the opportunity to earn your business and I look forward to hearing from you.</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Thank you for your inquiry', `document` = '<p>{first_name},</p>\n<p>Thank you for contacting me about my real estate listing. I''ll be in touch with you shortly. If you need to speak with me immediately, please feel free to call.</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Where are you in your home search?', `document` = '<p>Hi there!</p><p>Just checking with you to see where you are in your home search.</p>';

INSERT INTO `docs_categories` SET `name` = 'Financing', `description` = '';
SET @cat_id = LAST_INSERT_ID();

INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Getting Pre Qualified For a Home Loan', `document` = '<p>{first_name}</p>\n<p>As a prudent suggestion, if you are not going to be paying cash for your new home, be sure to be in the driver’s seat by getting pre-qualified for a home loan before you begin your new home search and start going out to visit homes that interest you.</p>\n<p>By getting pre-qualified up front you will know exactly what price range you should be looking in.  By getting pre-qualified you will save a lot of time, effort, fuel costs, and avoid the disappointment of not qualifying for a home loan that is not in your price range.</p>\n<p>You want to be able to act quickly and deal from a position of strength when we find the right home for you.  Having a pre-qualification letter from the lender in hand when we make an offer on a home will make your offer much stronger.</p>\n<p>{signature}</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Lender Information', `document` = '<p>{first_name}</p>\n<p>Here is the lender information that we talked about earlier. Please feel free to contact any or all of them. Please don''t hesitate to call, text, or email me with any questions.</p>\n<p>{signature}</p>';

INSERT INTO `docs_categories` SET `name` = 'Rentals', `description` = '';
SET @cat_id = LAST_INSERT_ID();

INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Rental Referral Email', `document` = '<p>It was nice speaking with you earlier today about your upcoming move to the the area.</p>\n<p>I will be happy to help you all with the purchase of a home in the area when the time is right for you.</p>\n<p>At this point, I will set you up on our web site so you can search for homes using the same tools that I use.</p>\n<p>Our team and I have been working with a large national investor over the last several years that buys homes and leases them out in the metro area.</p>\n<p>I have personally dealt with this company and would recommend you use them to find a rental property.</p>\n<p>I will represent you as an exclusive buyer''s agent when you are ready to purchase. I work with buyers only and look out for their best interests.</p>\n<p>Let me know if you have any questions.</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'New Rentals Coming to the Market', `document` = '<p>Here is your sneak peak at several homes that will be coming on the market soon.</p>\n<p>Not all of these homes are listed in the MLS yet, so call us if you are interested in seeing any of them.</p>';

INSERT INTO `docs_categories` SET `name` = 'Selling', `description` = '';
SET @cat_id = LAST_INSERT_ID();

INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'After Sale Follow-up', `document` = '<p>{first_name},</p>\n<p>Congratulations on the sale of your property!</p>\n<p>&nbsp;</p>\n<p>While I''m sure you have many memories there, it''s time to open a new chapter in your life</p>\n<p>&nbsp;</p>\n<p>I want you to know that it was a pleasure working for and with you. If there''s anything more I can do for you, do not hesitate to let me know.</p>\n<p>&nbsp;</p>\n<p>If you know of anyone else who would be interested in my services, I''d very much appreciate the referral. Please forward this email, with my contact information, or tell them to give me a call.</p>';

INSERT INTO `docs_categories` SET `name` = 'Real Estate Information', `description` = '';
SET @cat_id = LAST_INSERT_ID();

INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Local Real Estate Market Conditions', `document` = '<p>Are you looking for more than just property listings? Would you like to learn about neighborhoods in the local real estate market? We have tons of great information about market conditions, nearby amenities, local schools, and much more on our website. Simply select the area you''d like to read more about, or call us for the inside scoop. We''ll be happy to share our knowledge of the area with you.</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'The Home Buying Process', `document` = '<p>Hello {first_name}</p><p>Do you need support or advice during this exciting and stressful time? Just call or email us any time.</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Sharing Your Perfect Home', `document` = '<p>Did you know that you can bookmark your favorite properties for quick viewing later? When you''re looking at a property''s Details page, you''ll see a button that says Add to Favorites. Click the button, and that property will be instantly saved to your own hidden Favorites folder on the site.</p>\n<p>If you''d also like to share listings with friends and family, just click Send to a Friend, and the listing details will be sent to their email inbox. If a paper version of the listing is more convenient, simply hit Print, and you''ll be taken to a printer-friendly version of the listing details. On your browser, go to File and Print. Voila!</p>\n<p>Have questions about a particular property? Feel free to call or email us any time.</p>\n<p>{signature}</p>';

INSERT INTO `docs_categories` SET `name` = 'Introduction Emails', `description` = '';
SET @cat_id = LAST_INSERT_ID();

INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Saving My Home Search', `document` = '<p>Hello {first_name},</p>\n<p>Buying a home can be overwhelming if you don''t have a clear idea of what you''re looking for. Do you want to live in the city or out in the suburbs? Are you looking for a detached property or perhaps a condominium? How many bedrooms and bathrooms will you need? What about a garden, or essential amenities in the area? Once you''ve come up with a list of requirements, you can save time by saving your preferred search parameters.</p>\n<p>On the Property Search page, make your selections for your preferred area, number of bedrooms, and other details, and select Save Search. At this point, you can choose a name for your search, such as "2 bedroom Rural Houses" or "Homes in the Downtown Area." Once you''ve named and saved your search, you can quickly access this search from the Saved Searches folder in your account, 24 hours a day. Every time you select a saved search, your pre-selected search criteria will be applied to the listings, and you''ll see all the properties that match your criteria. This feature can be a real time saver, as you don''t have to enter the information every time you want to look for homes.</p>\n<p>Need a hand with your search? Call or email us for personalized assistance.</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Just Touching Base', `document` = '<p>Hello {first_name},</p>\n<p>I hope this email finds you well.;</p>\n<p>I just wanted to send you a quick note because I like to check in every once in awhile to make sure your wants and needs have not changed, and there isn''t anything I need to update for you.</p>\n<p>Please let me know should you have any questions or need anything at all. As always, if any properties should catch your eye and you would like to view them in person, let me know and I can get it scheduled for us.</p>\n<p>I look forward to hearing the latest from you.<br /><br />Best Regards,</p>\n<p>{signature}</p>\n<p>&nbsp;</p>\n<p>&nbsp;</p>\n<p>&nbsp;</p>\n<p>{unsubscribe}</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Have A Home You Want To See?', `document` = '<p>Hello {first_name},</p>\n<p>Have you spotted any listings you like? If so, you can easily arrange to view it in person by clicking Request a Showing from a property''s Details page. You''ll be taken to a brief form where you can post your preferred dates and times, or ask questions about the property. We''ll be in contact with you as soon as possible to schedule your tour!</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'Know Someone Who''s House Hunting?', `document` = '<p>Hello  {first_name},</p>\n<p>Most people, like yourself, who are in the process of looking for homes have friends who are also looking.</p>\n<p>If you do have a friend or family member searching for a home, please let them know about our site, <a href="http://www.rewdemo.com/">rewdemo.com</a>, as it is the area''s most powerful home search website.</p>\n<p>Also, if you know someone with a home to sell or in need of a property evaluation, please have them call us. We can help them as <p>well and we have plenty of free tools to assist them.</p>\n<p>As always, thank you for your time. I look forward to hearing from you soon.</p>\n<p>{signature}</p>';
INSERT INTO `docs` SET `cat_id` = @cat_id, `name` = 'You recently registered on our web site', `document` = '<p>Hi {first_name},</p>\n<p>You just signed up on our website so I wanted to introduce myself and give you some information about me.</p><p>Be sure to sign up for daily alerts of homes for sale so you know the instant your dream house hits the market.</p><p>Please keep in mind that buying your new home may depend on selling your current home. I will be happy to evaluate and market your home for you.</p>';

-- ---------------
-- Email Templates
-- ---------------

INSERT INTO `docs_templates` (`name`, `template`, `share`, `timestamp`) VALUES
  ('Company Branding Email', '<p>#body#</p>', 'true', '0000-00-00 00:00:00'),
  ('Agent Branding Template', '<p>#body#</p>', 'false', '0000-00-00 00:00:00'),
  ('Lender Information Email', '<p>#body#</p>', 'true', '0000-00-00 00:00:00'),
  ('Real Estate Promotional', '<p>#body#</p>', 'false', '0000-00-00 00:00:00'),
  ('New Listing Updates', '<p>#body#</p>', 'false', '0000-00-00 00:00:00'),
  ('Monthly Newsletter', '<p>#body#</p>', 'false', '0000-00-00 00:00:00'),
  ('Marketing Template', '<p>#body#</p>', 'true', '0000-00-00 00:00:00'),
  ('Event Reminder', '<p>#body#</p>', 'false', '0000-00-00 00:00:00')
;

-- ------------
-- File Manager
-- ------------

INSERT INTO `cms_files` (`agent`, `name`, `type`, `timestamp`, `data`, `size`, `share`, `password`, `views`) VALUES
  (1, 'SELLER-NOTES.txt', 'text/plain', NOW(), '', 5646, 'false', NULL, 0),
  (1, 'Email Marketing.pdf', 'text/plain', NOW(), '', 8791, 'true', NULL, 0),
  (1, 'A Homeowners Guide to Selling.pdf', 'text/plain', NOW(), '', 10264, 'true', NULL, 0),
  (1, 'Five things to know now if you''re thinking of buying real estate.pdf', 'text/plain', NOW(), '', 568, 'true', NULL, 0),
  (1, 'List of Lenders.pdf', 'text/plain', NOW(), '', 1012, 'true', NULL, 0),
  (1, 'Newsletter Email.pdf', 'text/plain', NOW(), '', 1054, 'true', NULL, 0),
  (1, 'company-checklist.jpg', 'text/plain', NOW(), '', 249, 'false', NULL, 0),
  (1, 'PendingLeadsExport.csv', 'text/plain', NOW(), '', 987, 'false', 'secret', 0),
  (1, 'Top 100 Teams in RE.xls', 'text/plain', NOW(), '', 789, 'false', NULL, 0),
  (1, 'Real Estate Sales.xls', 'text/plain', NOW(), '', 1654, 'false', NULL, 0),
  (1, 'Broker-Logo.jpg', 'text/plain', NOW(), '', 866, 'false', NULL, 0),
  (1, 'Listing Agreement.doc', 'text/plain', NOW(), '', 3045, 'true', NULL, 0),
  (1, 'CGR_AnnualReport.doc', 'text/plain', NOW(), '', 1456, 'false', 'secret', 0),
  (1, 'email-signature.jpg', 'text/plain', NOW(), '', 654, 'false', NULL, 0),
  (1, 'Seller Document.pdf', 'text/plain', NOW(), '', 1987, 'true', NULL, 0),
  (1, 'Marketing Plan.pdf', 'text/plain', NOW(), '', 2359, 'true', NULL, 0),
  (1, 'Opt-In-List.csv', 'text/plain', NOW(), '', 897, 'false', NULL, 0),
  (1, 'term_sheet.pdf', 'text/plain', NOW(), '', 1897, 'false', NULL, 0),
  (1, 'Leads_20170223.csv', 'text/plain', NOW(), '', 978, 'false', NULL, 0),
  (1, 'questionnaire.docx', 'text/plain', NOW(), '', 2121, 'false', NULL, 0),
  (1, 'awesome.png', 'text/plain', NOW(), '', 145, 'false', NULL, 0),
  (1, 'REQUIREMENTS.docx', 'text/plain', NOW(), '', 3247, 'false', 'rew', 0),
  (1, 'BEXL3E7S95.txt', 'text/plain', NOW(), '', 475, 'false', NULL, 0),
  (1, 'Doc1.docxv', 'text/plain', NOW(), '', 2011, 'false', NULL, 0),
  (1, 'VendorComparison.xls', 'text/plain', NOW(), '', 1765, 'false', NULL, 0),
  (1, 'HomeChecklist.doc', 'text/plain', NOW(), '', 966, 'false', NULL, 0)
;

-- ------------
-- CMS Listings
-- ------------

-- CMS Listing #1
INSERT INTO `_listings` (`agent`, `link`, `title`, `mls_number`, `address`, `city`, `state`, `zip`, `price`, `bedrooms`, `bathrooms`, `bathrooms_half`, `type`, `status`, `squarefeet`, `garages`, `lotsize`, `lotsize_unit`, `yearbuilt`, `stories`, `subdivision`, `school_district`, `school_elementary`, `school_middle`, `school_high`, `description`, `directions`, `features`, `timestamp_created`, `timestamp_updated`) VALUES
  (1, '444-sample-street-single-family-home', 'Single Family Home w/ Ocean View!', '', '123 Sample Street', 'Seattle', 'Washington', '00000', 650000, 3, 2, 0, 'Residential', 'Active', '2500', 0, '3 acres', '', '2004', '', '', '', '', '', '', 'Outstanding property on the outskirts of the city, situated on the lake. The property is approximately 3 acres in size, and the home features 3 bedrooms, 2 baths, and a den. This is the perfect lakefront retreat, within a half hour''s drive of the downtown core.', '', 'Basement,Dishwasher,Family Room,Fenced Yard,Fireplace,Hardwood Floors,Living Room,Refrigerator,Swimming Pool', NOW(), NOW())
;
SET @listing_id = LAST_INSERT_ID();
INSERT INTO `cms_uploads` (`type`, `row`, `file`, `size`, `order`, `timestamp`) VALUES
  ('listing', @listing_id, 'listing-1-1.jpg', 908806, 1, NOW()),
  ('listing', @listing_id, 'listing-1-2.jpg', 943686, 2, NOW()),
  ('listing', @listing_id, 'listing-1-3.jpg', 958681, 3, NOW())
;

-- CMS Listing #2
INSERT INTO `_listings` (`agent`, `link`, `title`, `mls_number`, `address`, `city`, `state`, `zip`, `price`, `bedrooms`, `bathrooms`, `bathrooms_half`, `type`, `status`, `squarefeet`, `garages`, `lotsize`, `lotsize_unit`, `yearbuilt`, `stories`, `subdivision`, `school_district`, `school_elementary`, `school_middle`, `school_high`, `description`, `directions`, `features`, `timestamp_created`, `timestamp_updated`) VALUES
  (1, '555-sample-drive-austin-texas', 'New Construction in Austin!', '', '555 Sample Drive', 'Austin', 'Texas', '00000', 350000, 2, 1, 1, 'Residential', 'Active', '1500', 1, '', '', '2011', '2', '', '', 'Anytown Elementary', 'Anytown Junior High', 'Anytown High', 'Act now to secure this stunning new home in the heart of Anytown. This property is close to parks, several private and public schools, and is just steps away from shops and restaurants. This is an excellent starter home in a vibrant community. Features of the home include a sun room, heated hardwood floors, and comes completely furnished. Don''t miss out on this unique opportunity -- arrange your showing today!', '', 'Carport,Dishwasher,Dryer,Family Room,Furnished,Hardwood Floors,Heated Floors,Living Room,Microwave,Refrigerator,Sun Room,Washer', NOW(), NOW())
;
SET @listing_id = LAST_INSERT_ID();
INSERT INTO `cms_uploads` (`type`, `row`, `file`, `size`, `order`, `timestamp`) VALUES
  ('listing', @listing_id, 'listing-2-1.jpg', 908806, 1, NOW()),
  ('listing', @listing_id, 'listing-2-2.jpg', 943686, 2, NOW()),
  ('listing', @listing_id, 'listing-2-3.jpg', 958681, 3, NOW())
;

-- CMS Listing #3
INSERT INTO `_listings` (`agent`, `link`, `title`, `mls_number`, `address`, `city`, `state`, `zip`, `price`, `bedrooms`, `bathrooms`, `bathrooms_half`, `type`, `status`, `squarefeet`, `garages`, `lotsize`, `lotsize_unit`, `yearbuilt`, `stories`, `subdivision`, `school_district`, `school_elementary`, `school_middle`, `school_high`, `description`, `directions`, `features`, `timestamp_created`, `timestamp_updated`) VALUES
  (1, '123-sample-ave-california', '5 Bedroom House on Acreage', '', '123 Sample Ave.', 'Los Angeles', 'California', '00000', 750000, 5, 3, 0, 'Residential', 'Active', '3000', 1, '5 acres', '', '2000', '3', 'Maplewood', '', '', '', '', 'This 5 bedroom house is set upon 5 acres of land in the rural Maplewood area, just outside of Anytown. This property offers tranquil surroundings in the country -- perfect for keeping horses. The house itself has granite countertops, floor to ceiling window, and high-end, stainless steel appliances.', '', 'Basement,Carport,Dryer,Family Room,Fenced Yard,Living Room,Refrigerator,Washer', NOW(), NOW())
;
SET @listing_id = LAST_INSERT_ID();
INSERT INTO `cms_uploads` (`type`, `row`, `file`, `size`, `order`, `timestamp`) VALUES
  ('listing', @listing_id, 'listing-3-1.jpg', 908806, 1, NOW()),
  ('listing', @listing_id, 'listing-3-2.jpg', 943686, 2, NOW()),
  ('listing', @listing_id, 'listing-3-3.jpg', 958681, 3, NOW())
;

-- ------------------
-- CMS Redirect Rules
-- ------------------

INSERT INTO `pages_rewrites` (`old`, `new`) VALUES
  ('/property-valuation.php', '/cma.php')
;

-- -------------
-- Unassigned Leads
-- -------------
SET @password = '$2y$10$psbDY7oLJKbcVfmRAToJZ.a595LnoY/rx6vBiVkKiq4.Wbvf4P/2u';
INSERT INTO `users` (`status`, `first_name`, `last_name`, `email`, `password`, `phone`, `phone_cell`, `remarks`, `notes`, `comments`, `value`, `referer`, `opt_texts`, `timestamp`, `timestamp_active`, `timestamp_assigned`, `timestamp_score`) VALUES
  ('unassigned', 'Johnny', 'Adams', 'demolead1@rewdemo.com', @password, '241-251-7264', '303-798-1209', 'Looking for fixer-upper located near Paul Mountains. Must have a jacuzzi tub.', '', 'I wanna buy a house. Phone me!\n\n', 375500, 'Google', 'in', NOW(), NOW(), NOW(), NOW()),
  ('unassigned', 'Carol', 'Wright', 'demolead2@rewdemo.com', @password, '213-770-5163', '', 'Looking to move to Austin in July but has not sold their lake-front apartment yet.', '', 'I wanna sell my home. Phone me!\n\n', 0, '', 'in', NOW() - INTERVAL 1 HOUR, NOW() - INTERVAL 1 HOUR, NOW() - INTERVAL 1 HOUR, NOW() - INTERVAL 1 HOUR),
  ('unassigned', 'Peter', 'Hastings', 'demolead3@rewdemo.com', @password, '250-753-9893', '', 'Only interested in homes outside of New Ricardo with a storage shed', 'Needs a selling agent.', '', 400000, '', 'in', NOW() - INTERVAL 2 HOUR, NOW() - INTERVAL 2 HOUR, NOW() - INTERVAL 2 HOUR, NOW() - INTERVAL 2 HOUR),
  ('unassigned', 'Jane', 'Brown', 'demolead4@rewdemo.com', @password, '771-554-5744', '882-258-2587', 'Sonia asked to see more homes within 9 miles of Kiannaside Middle School', 'Out of state buyer', '', 270000, 'Google', 'in', NOW() - INTERVAL 3 HOUR, NOW() - INTERVAL 3 HOUR, NOW() - INTERVAL 3 HOUR, NOW() - INTERVAL 3 HOUR),
  ('unassigned', 'Jim', 'White', 'demolead5@rewdemo.com', @password, '497.836.1123', '', '', 'Only interested in listings outside of Shermanmouth with plenty of living space', '', 1250999, 'Yahoo', 'in', NOW() - INTERVAL 4 HOUR, NOW() - INTERVAL 4 HOUR, NOW() - INTERVAL 4 HOUR, NOW() - INTERVAL 4 HOUR),
  ('unassigned', 'Lloyd', 'Johnson', 'demolead6@rewdemo.com', @password, '(638) 750-3764', '', 'Moving from Kentucky', '7753 Koepp Cliffs Suite 402 is being considered. Need to bring the keys on Saturday.', '', 850700, '', 'out', NOW() - INTERVAL 5 HOUR, NOW() - INTERVAL 5 HOUR, NOW() - INTERVAL 5 HOUR, NOW() - INTERVAL 5 HOUR),
  ('unassigned', 'Nancy', 'Fulton', 'demolead7@rewdemo.com', @password, '', '741-652-0281', 'Interested in buying a fully furnished acreage in Adrienville.', 'Must bring the keys for thursday''s showing.', '', 768000, 'bing.com', 'out', NOW() - INTERVAL 6 HOUR, NOW() - INTERVAL 6 HOUR, NOW() - INTERVAL 6 HOUR, NOW() - INTERVAL 6 HOUR),
  ('unassigned', 'Billy', 'Briggs', 'demolead8@rewdemo.com', @password, '(831) 680-4204', '', '', 'Set up an meeting for 8am Sunday to meet Axel and their partner.', '', 0, 'Bing', 'in', NOW() - INTERVAL 7 HOUR, NOW() - INTERVAL 7 HOUR, NOW() - INTERVAL 7 HOUR, NOW() - INTERVAL 7 HOUR),
  ('unassigned', 'Linda', 'Hopkins', 'demolead9@rewdemo.com', '', '', '(785) 898-5512', '', 'Wants to find lake-front home in East Lake Monty for over $200,000', '', 0, 'Google', 'in', NOW() - INTERVAL 8 HOUR, NOW() - INTERVAL 8 HOUR, NOW() - INTERVAL 8 HOUR, NOW() - INTERVAL 8 HOUR),
  ('unassigned', 'James', 'O''Brien', 'demolead10@rewdemo.com', '', '1-274-895-1995', '', '', 'Moving from Florida and is interesting in buying a beautifully landscaped home.', '', 315000, '', 'out', NOW() - INTERVAL 9 HOUR, NOW() - INTERVAL 9 HOUR, NOW() - INTERVAL 9 HOUR, NOW() - INTERVAL 9 HOUR),
  ('unassigned', 'Scott', 'Price', 'demolead11@rewdemo.com', '', '596-773-1747', '', '', 'Hoping to find town home 2 miles of East Aaliyah for for $450,000+', '', 0, '', NOW() - INTERVAL 10 HOUR, 'out', NOW() - INTERVAL 10 HOUR, NOW() - INTERVAL 10 HOUR, NOW() - INTERVAL 10 HOUR),
  ('unassigned', 'Jimbo', 'Jones', 'demolead12@rewdemo.com', '', '', '', '', '', '', 0, '', 'out', NOW() - INTERVAL 11 HOUR, NOW() - INTERVAL 11 HOUR, NOW() - INTERVAL 11 HOUR, NOW() - INTERVAL 11 HOUR)
;

-- -------------
-- Lead Action Plans
-- -------------
INSERT INTO `users_action_plans` (`actionplan_id`, `user_id`, `timestamp_assigned`, `timestamp_completed`) VALUES
  (1, 1, NOW(), NOW()),
  (3, 1, NOW(), NULL)
;

-- -------------
-- Lead Messages
-- -------------
SET @user_id = LAST_INSERT_ID();
INSERT INTO `users_messages` (`sent_from`, `agent_id`, `user_id`, `subject`, `message`, `timestamp`) VALUES
  ('lead', 1, @user_id, 'Send me more information', 'I want to learn more about 123 Sample Ave, Los Angeles, CA', NOW()),
  ('lead', 1, @user_id + (@@auto_increment_increment * 1), 'RE: 180 Cozy Cove Drive', 'Do you have floor plan? any bids for completion? Photos of view from second level', NOW() - INTERVAL 1 HOUR),
  ('lead', 1, @user_id + (@@auto_increment_increment * 2), 'Short sale list', 'Please send me a current list of short sale properties in anchorage.', NOW() - INTERVAL 2 HOUR),
  ('lead', 1, @user_id + (@@auto_increment_increment * 3), 'Property showing request', 'I am available to look at the house pretty much any time on Mon, Tue, or Wed. Thank you!', NOW() - INTERVAL 3 HOUR),
  ('lead', 1, @user_id + (@@auto_increment_increment * 4), 'Quick showing request', 'I''d like to request a showing of 5027 N Flying Circus Circle', NOW() - INTERVAL 4 HOUR),
  ('lead', 1, @user_id + (@@auto_increment_increment * 5), 'Homes near Willowbrook', 'I would like to find a home near my son''s school, Willowbrook Elementary', NOW() - INTERVAL 5 HOUR),
  ('lead', 1, @user_id + (@@auto_increment_increment * 6), 'Foreclosures & short sales', 'Please send me a list of recent foreclosures in the Fresno area.', NOW() - INTERVAL 6 HOUR),
  ('lead', 1, @user_id + (@@auto_increment_increment * 7), 'Property Showing', 'I''d like to request a showing of 3321 N Wyoming Drive', NOW() - INTERVAL 7 HOUR),
  ('lead', 1, @user_id + (@@auto_increment_increment * 8), '38797 Fritz Creek Valley Drive', 'This is a place that I would seriously put an offer on.', NOW() - INTERVAL 8 HOUR),
  ('lead', 1, @user_id + (@@auto_increment_increment * 9), 'RE: 40951 Kay Court', 'Are there any pictures of the kitchen and the out side of the house? Thanks', NOW() - INTERVAL 9 HOUR),
  ('lead', 1, @user_id + (@@auto_increment_increment * 10), 'RE: Mortgage information', 'Thank you for the list of available mortgage brokers.', NOW() - INTERVAL 10 HOUR),
  ('lead', 1, @user_id + (@@auto_increment_increment * 11), 'Showing request', 'I''d like to request a showing of 1715 S Hidden View Road', NOW() - INTERVAL 11 HOUR)
;

-- ----------
-- Lead Forms
-- ----------
-- See REW\Seeders\DynamicDataSeeder

-- -----------
-- Lead Groups
-- -----------
INSERT INTO `groups` (`agent_id`, `name`, `description`, `style`, `user`, `timestamp`) VALUES
  (NULL, 'Android Leads', '', 'red', 'false', '0000-00-00 00:00:00'),
  (NULL, 'Mobile Leads', '', 'rose', 'false', '0000-00-00 00:00:00'),
  (NULL, 'GS Leads', '', 'violet', 'false', '0000-00-00 00:00:00'),
  (NULL, 'Zillow Leads', '', 'azure', 'false', '0000-00-00 00:00:00'),
  (NULL, 'Agent Requested', '', 'azure', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Foreign Buyer', '', 'rose', 'true', '0000-00-00 00:00:00'),
  (NULL, 'New Recruits', '', 'seafoam', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Follow Up', '', 'orange', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Survey', '', 'lime', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Pending Sale', '', 'lime', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Active Listing', '', 'green', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Approved Buyer', '', 'purple', 'true', '0000-00-00 00:00:00'),
  (NULL, 'First Time Buyer', '', 'lime', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Priority Follow Up', '', 'blaze', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Been In Touch', 'Talked to lead', 'green', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Cash Buyer', 'Those who intend to buy with cash rather than mortgage', 'orange', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Downtown Condos', 'Looking for Downtown Condos for sale', 'orange', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Buyer <$200K', 'Buyers with price range under $200,000.', 'blaze', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Buyer $200K-$300K', 'Buyers with price range between $200,000 and $300,000.', 'blaze', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Buyer $300K-$400K', 'Buyers with price range between $300,000 and $400,000.', 'blaze', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Buyer $400K-$600K', 'Buyers with price range between $400,000 and $600,000.', 'blaze', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Buyer $600K-$800K', 'Buyers with price range between $600,000 and $800,000.', 'blaze', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Buyer $800K-$1M', 'Buyers with price range between $800,000 and $1,000,000.', 'blaze', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Buyer Over $1M', 'Buyers with a price range of $1,000,000 and beyond.', 'blaze', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Selling Over $1M', 'Sellers with a list price of over $1,000,000', 'blaze', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Foreclosures & Short Sales', 'Looking for foreclosures & short sales primarily', 'grenadine', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Golf Course Community', 'Wants to live in golf course community', 'bean', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Gated Community', 'Wants to live in gated community', 'bean', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Lots & Land', 'Looking for vacant land and lots', 'almond', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Commercial Real Estate', '', 'marigold', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Luxury Real Estate', '', 'marigold', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Listing Prospects', '', 'marigold', 'true', '0000-00-00 00:00:00'),
  (NULL, 'No Answer / Unresponsive', 'Have not heard back.', 'grey', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Met in Person', 'Met in office for consultation', 'canary', 'true', '0000-00-00 00:00:00'),
  (NULL, 'DO NOT CONTACT', '', 'red', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Active Client', '', 'orange', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Premium Lead', '', 'yellow', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Past Client', '', 'blue', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Bad Number', '', 'red', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Junk Leads', '', 'grey', 'true', '0000-00-00 00:00:00'),
  (NULL, 'Referral', '', 'grenadine', 'true', '0000-00-00 00:00:00'),
  (NULL, 'VIP', '', 'yellow', 'true', '0000-00-00 00:00:00')
;

-- --------------------------
-- Assign "Buyer Leads" Group
-- --------------------------
INSERT INTO `users_groups` SET `group_id` = @buyer_group_id, `user_id` = @user_id + (@@auto_increment_increment * 0);
INSERT INTO `users_groups` SET `group_id` = @buyer_group_id, `user_id` = @user_id + (@@auto_increment_increment * 2);
INSERT INTO `users_groups` SET `group_id` = @buyer_group_id, `user_id` = @user_id + (@@auto_increment_increment * 4);
INSERT INTO `users_groups` SET `group_id` = @buyer_group_id, `user_id` = @user_id + (@@auto_increment_increment * 6);
INSERT INTO `users_groups` SET `group_id` = @buyer_group_id, `user_id` = @user_id + (@@auto_increment_increment * 8);
INSERT INTO `users_groups` SET `group_id` = @buyer_group_id, `user_id` = @user_id + (@@auto_increment_increment * 10);

-- ---------------------------
-- Assign "Seller Leads" Group
-- ---------------------------
INSERT INTO `users_groups` SET `group_id` = @seller_group_id, `user_id` = @user_id + (@@auto_increment_increment * 1);
INSERT INTO `users_groups` SET `group_id` = @seller_group_id, `user_id` = @user_id + (@@auto_increment_increment * 3);
INSERT INTO `users_groups` SET `group_id` = @seller_group_id, `user_id` = @user_id + (@@auto_increment_increment * 5);
INSERT INTO `users_groups` SET `group_id` = @seller_group_id, `user_id` = @user_id + (@@auto_increment_increment * 7);
INSERT INTO `users_groups` SET `group_id` = @seller_group_id, `user_id` = @user_id + (@@auto_increment_increment * 9);
INSERT INTO `users_groups` SET `group_id` = @seller_group_id, `user_id` = @user_id + (@@auto_increment_increment * 11);

-- ------------
-- "VIP" Group
-- ------------
INSERT INTO `groups` SET `agent_id` = NULL, `name` = 'VIP', `style` = 'yellow', `user` = 'true', `timestamp` = '0000-00-00 00:00:00';
SET @group_id = LAST_INSERT_ID();
INSERT INTO `users_groups` SET `group_id` = @group_id, `user_id` = @user_id + (@@auto_increment_increment * 1);
INSERT INTO `users_groups` SET `group_id` = @group_id, `user_id` = @user_id + (@@auto_increment_increment * 3);
INSERT INTO `users_groups` SET `group_id` = @group_id, `user_id` = @user_id + (@@auto_increment_increment * 9);

-- -----------------
-- "iOS Leads" Group
-- ------------------
INSERT INTO `groups` SET `agent_id` = NULL, `name` = 'iOS Leads', `style` = 'violet', `user` = 'false', `timestamp` = '0000-00-00 00:00:00';
SET @group_id = LAST_INSERT_ID();
INSERT INTO `users_groups` SET `group_id` = @group_id, `user_id` = @user_id + (@@auto_increment_increment * 0);
INSERT INTO `users_groups` SET `group_id` = @group_id, `user_id` = @user_id + (@@auto_increment_increment * 5);
INSERT INTO `users_groups` SET `group_id` = @group_id, `user_id` = @user_id + (@@auto_increment_increment * 11);

-- -----
-- Teams
-- -----
INSERT INTO `teams` (`agent_id`, `name`, `description`, `style`, `subdomain`, `subdomain_link`, `timestamp`) VALUES
  (1, 'The "A" Team', 'Top performers.', 'red', 'true', 'team', '2017-04-20 11:07:07'),
  (1, 'Enterprise Team', 'All things enterprise.', '', 'false', '', '2017-03-23 23:35:12')
;
SET @team_id = LAST_INSERT_ID();

-- ------
-- Agents
-- ------
INSERT INTO `auth` (`type`, `username`, `password`, `last_logon`, `timestamp_created`, `timestamp_updated`) VALUES
  ('Agent', 'agent1', @AGENT_PASSWORD, NOW(), NOW(), NOW()),
  ('Agent', 'agent2', @AGENT_PASSWORD, NOW() - INTERVAL 1 HOUR, NOW(), NOW()),
  ('Agent', 'agent3', @AGENT_PASSWORD, NOW() - INTERVAL 24 HOUR, NOW() - INTERVAL 48 HOUR, NOW() - INTERVAL 48 HOUR),
  ('Agent', 'agent4', @AGENT_PASSWORD, NULL, NOW() - INTERVAL 72 HOUR, NOW() - INTERVAL 72 HOUR)
;

SET @auth_id = LAST_INSERT_ID();

INSERT INTO `agents` (`auth`, `office`, `first_name`, `last_name`, `email`, `sms_email`, `cms`, `cms_idxs`, `cms_link`, `blog`, `blog_picture`, `blog_profile`, `blog_signature`, `blog_signature_on`, `default_filter`, `timezone`, `page_limit`, `timestamp`, `image`, `remarks`, `title`, `display`, `display_feature`, `cell_phone`, `office_phone`, `home_phone`, `fax`, `agent_id`, `website`, `signature`, `add_sig`, `ar_subject`, `ar_cc_email`, `ar_bcc_email`, `ar_document`, `ar_tempid`, `ar_is_html`, `ar_active`, `auto_assign_admin`, `auto_assign_agent`, `auto_assign_time`, `auto_rotate`, `auto_optout`, `auto_optout_time`, `permissions_admin`, `permissions_user`, `mode`, `columns`, `columns_agents`) VALUES
  (@auth_id + (@@auto_increment_increment * 0), NULL, 'Charlie', 'Angus', 'demoagent1@rewdemo.com', '', 'true',  'carets', 'agent', 'true', '', '', '', 'true', 'all', 6, 10, NOW(), 'agent-1.jpg', 'If you haven''t met Charlie in the local coffee shop or at a children''s hospital fundraiser, you may not know about his remarkable charisma and extensive knowledge of the community. \r\n\r\nCharlie (or "Chuck", or "Chuckles", as he is affectionately known by the hospital kids) has been working in real estate since 1977, when he started as an unpaid intern at the local RE/MAX office. Within ten years, he was getting his broker''s license, and the rest is history!', 'Broker', 'Y', 'Y', '', '250.753.9893', '', '', '', '', '', 'N', '', '', '', '', NULL, 'true', 'N', 'false', 'false', '0000-00-00 00:00:00', 'false', 'false', '0000-00-00 00:00:00', 18446744073709551615, 18446744073709551615, 'admin', 'email,phone,groups,notes,status,forms,calls,emails,listings,searches,agent,visits,origin', 'leads,login'),
  (@auth_id + (@@auto_increment_increment * 1), NULL, 'Daphne', 'Demos', 'demoagent2@rewdemo.com', '', 'false',  NULL, NULL, 'false', '', '', '', 'true', 'all', 6, 20, NOW(), 'agent-2.jpg', 'Daphne has been instrumental in getting our company out of the stone age. She is the heart behind it all! She often works as a partner on our seller agent''s accounts, cleaning the client''s house in preparation for a showing -- even if the client doesn''t want her to.', 'Real Estate Agent', 'Y', 'Y', '250.753.9893', '', '', '', '{"abor":""}', '', '<em>My Agent Signature</em>', 'Y', '', '', '', '', NULL, 'true', 'N', 'false', 'false', '2013-03-15 10:53:52', 'false', 'false', '0000-00-00 00:00:00', 17179869187, 2878, 'admin', 'email,phone,groups,notes,status,forms,calls,emails,listings,searches,agent,visits,origin', 'leads,login'),
  (@auth_id + (@@auto_increment_increment * 2), NULL, 'George', 'Harper', 'demoagent3@rewdemo.com', '', 'false',  NULL, NULL, 'false', '', '', '', 'true', 'all', 6, 20, NOW(), 'agent-3.jpg', 'George is a master of negotiations and is never satisfied with a deal unless he thinks he got the best of it. He''s been a star on our sales team since 2001, and has helped a lot of sellers to get the maxium price for their properties, even when the conditions weren''t in their favour. \r\n\r\nA loving father and husband, George has lived here all his life and loves his job, working for you.', 'Seller Agent', 'Y', 'Y', '250.753.9893', '250.753.9893', '250.753.9893', '250.753.9893', '{"abor":""}', 'http://www.realestatewebmasters.com/', '', 'N', '', '', '', '', NULL, 'true', 'N', 'false', 'false', '2013-03-15 10:55:32', 'false', 'false', '0000-00-00 00:00:00', 0, 2878, 'agent', 'email,phone,groups,notes,status,forms,calls,emails,listings,searches,agent,visits,origin', 'leads,login'),
  (@auth_id + (@@auto_increment_increment * 3), NULL, 'Anita', 'Smith', 'demoagent4@rewdemo.com', '', 'false',  NULL, NULL, 'false', '', '', '', 'true', 'all', 6, 20, NOW(), 'agent-4.jpg', 'You''ve probably never met someone as bubbly and enthusiastic as Anita. She is a charmer who always finds a way to make people smile. \r\n\r\nWhen you consider that Anita is also our highest-rated buyers agent, you can see why so many of her clients are repeats and referrals. It''s been said that Anita could find a home for anyone!', 'Buying Specialist', 'Y', 'Y', '250.753.9893', '250.753.9893', '250.753.9893', '250.753.9893', '', '', '', 'N', '', '', '', '', NULL, 'true', 'N', 'false', 'false', '2013-03-15 10:56:26', 'false', 'false', '0000-00-00 00:00:00', 0, 2878, 'agent', 'email,phone,groups,notes,status,forms,calls,emails,listings,searches,agent,visits,origin', 'leads,login')
;

SET @agent1_id = (SELECT `a`.`id` FROM `agents` `a` JOIN `auth` `u` ON `a`.`auth` = `u`.`id` WHERE `username` = 'agent1');
SET @agent2_id = (SELECT `a`.`id` FROM `agents` `a` JOIN `auth` `u` ON `a`.`auth` = `u`.`id` WHERE `username` = 'agent2');
SET @agent3_id = (SELECT `a`.`id` FROM `agents` `a` JOIN `auth` `u` ON `a`.`auth` = `u`.`id` WHERE `username` = 'agent3');
SET @agent4_id = (SELECT `a`.`id` FROM `agents` `a` JOIN `auth` `u` ON `a`.`auth` = `u`.`id` WHERE `username` = 'agent4');

INSERT INTO `team_agents` (`team_id`, `agent_id`, `granted_permissions`, `granting_permissions`) VALUES
  (@team_id, @agent2_id, 0, 0),
  (@team_id, @agent3_id, 0, 0)
;

UPDATE `users` SET `agent` = @agent2_id, `status` = 'accepted' WHERE `email` = 'demolead7@rewdemo.com';
UPDATE `users` SET `agent` = @agent2_id, `status` = 'pending' WHERE `email` = 'demolead8@rewdemo.com';
UPDATE `users` SET `agent` = @agent2_id, `status` = 'pending' WHERE `email` = 'demolead9@rewdemo.com';

UPDATE `users` SET `agent` = @agent3_id, `status` = 'pending' WHERE `email` = 'demolead10@rewdemo.com';
UPDATE `users` SET `agent` = @agent3_id, `status` = 'accepted' WHERE `email` = 'demolead11@rewdemo.com';
UPDATE `users` SET `agent` = @agent3_id, `status` = 'accepted' WHERE `email` = 'demolead12@rewdemo.com';

-- Assign Agent ID for IDX Listings
UPDATE `agents` SET `agent_id` = '{"uat2": "wunsch.ottilie"}' WHERE `id` = @agent1_id;
UPDATE `agents` SET `agent_id` = '{"uat":"daphned", "uat2": "ddemos"}' WHERE `id` = @agent2_id;
UPDATE `agents` SET `agent_id` = '{"uat":"nharber", "uat2": "jarrod15"}' WHERE `id` = @agent3_id;
UPDATE `agents` SET `agent_id` = '{"uat":"asmith"}' WHERE `id` = @agent4_id;

-- ------------
-- Site Content
-- ------------

-- Insert default sample testimonials
INSERT INTO `testimonials` (`agent_id`, `client`, `testimonial`) VALUES
	(NULL, 'Peppy Piper', 'He may not look like much, but Charlie was an energetic bulldog! Not sure how he did it--but he tirelessly fough for our interests, despite my wife''s crazy high demands. He''ll be hearing from us again!'),
	(NULL, 'Jane & John Doe', 'We can''t thank you enough for all your hard work. Our new home is everything we hoped it would be, and the price you negotiated was great! We wouldn''t hesitate to recommend you to friends and family, and will definitely use your services for our next real estate transaction.'),
	(NULL, '', 'Your website was the ONLY resource we found that could meet our needs as we searched for a home. The polygon search tool allowed us to narrow our hunt to the most relevant neighborhoods, and your "Communities" content was the only truly honest material we could find. <strong>Thank you SO much.</strong>')
;

-- Assign sample agent testimonial
UPDATE `testimonials` SET `agent_id` = (
    SELECT `id` FROM `agents` WHERE `email` = 'demoagent1@rewdemo.com'
) WHERE `client` = 'Peppy Piper';

-- ------------
-- Blog Content
-- ------------

-- Blog Categories
INSERT INTO `blog_categories` (`link`, `parent`, `title`, `description`, `order`, `timestamp_created`, `timestamp_updated`) VALUES
  ('local-real-estate', '', 'Local Real Estate', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
  ('current-market-information', '', 'Current Market Information', 'This is your #1 source for real estate information in your market.&nbsp; Thanks for stopping by!', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
  ('recent-home-sales', 'current-market-information', 'Recent Home Sales', 'A summary of recent home sales in your area -- from all aspects of the spectrum.', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
  ('homeowner-advice', '', 'Homeowner Advice', '', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00')
;

-- Blog Entries
INSERT INTO `blog_entries` (`agent`, `link`, `title`, `body`, `categories`, `tags`, `meta_tag_desc`, `link_title1`, `link_url1`, `link_title2`, `link_url2`, `link_title3`, `link_url3`, `published`, `views`, `timestamp_published`, `timestamp_created`, `timestamp_updated`) VALUES
  (1, 'latest-market-statistics-your-market-your-community', 'Latest Market Statistics - Your Market, Your Community', '<h1>Market Summary <br /></h1><p>Lorem ipsum <b>dolor sit amet</b>, consectetuer adipiscing\r\nelit. Phasellus lorem. Aliquam erat volutpat. Aliquam sit amet massa.\r\nPhasellus non risus ut felis tincidunt vehicula. Proin mattis accumsan\r\ndolor. Sed<em> tortor tortor, varius eu</em>, hendrerit in, vulputate eget, mauris. Mauris elit.Lorem ipsum <b>dolor sit amet</b>, consectetuer adipiscing\r\nelit. Phasellus lorem. Aliquam erat volutpat. Aliquam sit amet massa.\r\nPhasellus non risus ut felis tincidunt vehicula. Proin mattis accumsan\r\ndolor. Sed<em> tortor tortor, varius eu</em>, hendrerit in, vulputate eget, mauris. Mauris elit.</p><h2>Changes from previous month</h2><p>Lorem ipsum <b>dolor sit amet</b>, consectetuer adipiscing\r\nelit. Phasellus lorem. Aliquam erat volutpat. Aliquam sit amet massa.\r\nPhasellus non risus ut felis tincidunt vehicula. Proin mattis accumsan\r\ndolor. Sed<em> tortor tortor, varius eu</em>, hendrerit in, vulputate eget, mauris. Mauris elit.</p><h2>Looking ahead to the future</h2><p>Lorem ipsum <b>dolor sit amet</b>, consectetuer adipiscing\r\nelit. Phasellus lorem. Aliquam erat volutpat. Aliquam sit amet massa.\r\nPhasellus non risus ut felis tincidunt vehicula. Proin mattis accumsan\r\ndolor. Sed<em> tortor tortor, varius eu</em>, hendrerit in, vulputate eget, mauris. Mauris elit. </p>', 'real-esate-highlights-current-market-information', '', '', '', '', '', '', '', '', 'true', 0, NOW(), '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
  (1, 'testing-with-a-first-blog-post', 'Testing With a First Blog Post', '<h1>Testing Heading</h1><p>Lorem ipsum <b>dolor sit amet</b>, consectetuer adipiscing\r\nelit. Phasellus lorem. Aliquam erat volutpat. Aliquam sit amet massa.\r\nPhasellus non risus ut felis tincidunt vehicula. Proin mattis accumsan\r\ndolor. Sed<em> tortor tortor, varius eu</em>, hendrerit in, vulputate eget, mauris. Mauris elit.</p><h3>Testing Sub-heading</h3><p>Lorem ipsum <b>dolor sit amet</b>, consectetuer adipiscing\r\nelit. Phasellus lorem. Aliquam erat volutpat. Aliquam sit amet massa.\r\nPhasellus non risus ut felis tincidunt vehicula. Proin mattis accumsan\r\ndolor. Sed<em> tortor tortor, varius eu</em>, hendrerit in, vulputate eget, mauris. Mauris elit.</p><p><b>Testing Formatting</b></p><p><u>Lorem ipsum <b>dolor sit amet</b>, consectetuer adipiscing\r\nelit.</u> Phasellus lorem. Aliquam erat volutpat. Aliquam sit amet massa.\r\nPhasellus non risus ut felis tincidunt vehicula. <strike>Proin mattis accumsan\r\ndolor.</strike> Sed<em> tortor tortor, varius eu</em>, hendrerit in, <em>vulputate eget, mauris. Mauris elit.</em> </p>', 'testing-my-rew-blog', '', '', '', '', '', '', '', '', 'false', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00')
;

-- Blog Links
INSERT INTO `blog_links` (`title`, `link`, `target`, `order`, `timestamp_created`, `timestamp_updated`) VALUES
  ('Real Estate Webmasters', 'http://www.realestatewebmasters.com/', '_self', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
  ('Guaranteed Sale', 'http://www.guaranteedsale.com/', '_blank', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00')
;

-- --------------------
-- Featured Communities
-- --------------------

INSERT INTO `featured_communities` (`title`, `is_enabled`, `subtitle`, `description`, `stats_heading`, `stats_total`, `stats_average`, `stats_highest`, `stats_lowest`, `anchor_one_text`, `anchor_one_link`, `anchor_two_text`, `anchor_two_link`, `search_idx`, `search_criteria`, `snippet`, `page_id`, `order`, `timestamp_created`, `timestamp_updated`) VALUES
  ('East Nashville', 'Y', 'Hip & Historic', 'Welcome to East Nashville, one of the city''s most eclectic, hip and historic neighbourhoods. Although being close to downtown, East Nashville has a very relaxed vibe and offers endless opportunities to play and to enjoy a night out. The area boosts a lively restaurant, pub and indie rock scene to keep you busy every night of the week. Whether you are in the mood for great music, vintage shopping or a pint of local handcrafted brew you can all find it here in this great neighbourhood.', '', '', '', '', '', '', '', '', '', 'realtracs', 'a:24:{s:2:"id";s:1:"2";s:4:"feed";s:9:"realtracs";s:5:"title";s:14:"East Nashville";s:8:"subtitle";s:14:"Hip & Historic";s:11:"description";s:488:"Welcome to East Nashville, one of the city\'s most eclectic, hip and historic neighbourhoods. Although being close to downtown, East Nashville has a very relaxed vibe and offers endless opportunities to play and to enjoy a night out. The area boosts a lively restaurant, pub and indie rock scene to keep you busy every night of the week. Whether you are in the mood for great music, vintage shopping or a pint of local handcrafted brew you can all find it here in this great neighbourhood.";s:7:"snippet";s:17:"fc-east-nashville";s:7:"page_id";s:2:"32";s:13:"stats_heading";s:0:"";s:11:"stats_total";s:0:"";s:13:"stats_average";s:0:"";s:13:"stats_highest";s:0:"";s:12:"stats_lowest";s:0:"";s:15:"anchor_one_text";s:0:"";s:15:"anchor_two_text";s:0:"";s:15:"anchor_one_link";s:0:"";s:15:"anchor_two_link";s:0:"";s:6:"panels";a:3:{s:11:"subdivision";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"city";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"type";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}s:18:"search_subdivision";s:14:"East Nashville";s:11:"search_city";a:1:{i:0;s:9:"Nashville";}s:11:"search_type";a:2:{i:0;s:11:"Residential";i:1;s:11:"Condominium";}s:3:"idx";s:9:"realtracs";s:14:"search_subtype";s:0:"";s:4:"file";s:0:"";s:7:"uploads";a:3:{i:0;s:2:"10";i:1;s:2:"18";i:2;s:2:"19";}}', 'fc-east-nashville', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
  ('Hendersonville', 'Y', '', 'Located just 18 miles northeast of downtown Nashville, lovely Hendersonville welcomes you! Indulge yourself in the familiarity and security of this charming small town, while having all city amenities at your doorstep. Hendersonville offers a great variety of restaurants, diners and hip coffee shops. People with an active outdoor lifestyle love Hendersonville for its numerous parks and recreation centres, which offer residents lots of activities for the physical, mental and social well being.', '', '', '', '', '', '', '', '', '', 'realtracs', 'a:24:{s:2:"id";s:1:"3";s:4:"feed";s:9:"realtracs";s:5:"title";s:14:"Hendersonville";s:8:"subtitle";s:0:"";s:11:"description";s:497:"Located just 18 miles northeast of downtown Nashville, lovely Hendersonville welcomes you! Indulge yourself in the familiarity and security of this charming small town, while having all city amenities at your doorstep. Hendersonville offers a great variety of restaurants, diners and hip coffee shops. People with an active outdoor lifestyle love Hendersonville for its numerous parks and recreation centres, which offer residents lots of activities for the physical, mental and social well being.";s:7:"snippet";s:17:"fc-hendersonville";s:7:"page_id";s:2:"33";s:13:"stats_heading";s:0:"";s:11:"stats_total";s:0:"";s:13:"stats_average";s:0:"";s:13:"stats_highest";s:0:"";s:12:"stats_lowest";s:0:"";s:15:"anchor_one_text";s:0:"";s:15:"anchor_two_text";s:0:"";s:15:"anchor_one_link";s:0:"";s:15:"anchor_two_link";s:0:"";s:6:"panels";a:3:{s:11:"subdivision";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"city";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"type";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}s:18:"search_subdivision";s:0:"";s:11:"search_city";a:1:{i:0;s:14:"Hendersonville";}s:11:"search_type";a:2:{i:0;s:11:"Residential";i:1;s:11:"Condominium";}s:3:"idx";s:9:"realtracs";s:14:"search_subtype";s:0:"";s:4:"file";s:0:"";s:7:"uploads";a:1:{i:0;s:2:"11";}}', 'fc-hendersonville', 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
  ('Mount Juliet', 'Y', '', 'Mount Jouliet the gateway to the Appalachians and the Atlantic Seaboard warmly welcomes you. One of the fastest growing communities in Tennessee Mount Jouliet is located only 15 minutes from Nashville International Airport and City Center it offers the perfect location in regards to balance between big city and small town life. Mount Juliet is home to Providence Marketplace, the largest shopping center between Nashville and Knoxville to satisfy all your shopping needs.', '', '', '', '', '', '', '', '', '', 'realtracs', 'a:24:{s:2:"id";s:1:"4";s:4:"feed";s:9:"realtracs";s:5:"title";s:12:"Mount Juliet";s:8:"subtitle";s:0:"";s:11:"description";s:473:"Mount Jouliet the gateway to the Appalachians and the Atlantic Seaboard warmly welcomes you. One of the fastest growing communities in Tennessee Mount Jouliet is located only 15 minutes from Nashville International Airport and City Center it offers the perfect location in regards to balance between big city and small town life. Mount Juliet is home to Providence Marketplace, the largest shopping center between Nashville and Knoxville to satisfy all your shopping needs.";s:7:"snippet";s:15:"fc-mount-juliet";s:7:"page_id";s:2:"34";s:13:"stats_heading";s:0:"";s:11:"stats_total";s:0:"";s:13:"stats_average";s:0:"";s:13:"stats_highest";s:0:"";s:12:"stats_lowest";s:0:"";s:15:"anchor_one_text";s:0:"";s:15:"anchor_two_text";s:0:"";s:15:"anchor_one_link";s:0:"";s:15:"anchor_two_link";s:0:"";s:6:"panels";a:3:{s:11:"subdivision";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"city";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"type";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}s:18:"search_subdivision";s:0:"";s:11:"search_city";a:1:{i:0;s:12:"Mount Juliet";}s:11:"search_type";a:2:{i:0;s:11:"Residential";i:1;s:11:"Condominium";}s:3:"idx";s:9:"realtracs";s:14:"search_subtype";s:0:"";s:4:"file";s:0:"";s:7:"uploads";a:3:{i:0;s:2:"12";i:1;s:2:"13";i:2;s:2:"14";}}', 'fc-mount-juliet', 0, 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
  ('Hillsboro Village', 'Y', '', 'Hillsboro Village is located next to Vanderbilt and Belmont Universities, therefore this beautiful and hip Nashville neighbourhood is attracting lots of young professionals and students for living and playing. The neighbourhood offers a diverse mix of restaurants, pubs and live-music venues. ', '', '', '', '', '', '', '', '', '', 'realtracs', 'a:23:{s:2:"id";s:1:"5";s:4:"feed";s:9:"realtracs";s:5:"title";s:17:"Hillsboro Village";s:8:"subtitle";s:0:"";s:11:"description";s:293:"Hillsboro Village is located next to Vanderbilt and Belmont Universities, therefore this beautiful and hip Nashville neighbourhood is attracting lots of young professionals and students for living and playing. The neighbourhood offers a diverse mix of restaurants, pubs and live-music venues. ";s:7:"snippet";s:20:"fc-hillsboro-village";s:7:"page_id";s:2:"36";s:13:"stats_heading";s:0:"";s:11:"stats_total";s:0:"";s:13:"stats_average";s:0:"";s:13:"stats_highest";s:0:"";s:12:"stats_lowest";s:0:"";s:15:"anchor_one_text";s:0:"";s:15:"anchor_two_text";s:0:"";s:15:"anchor_one_link";s:0:"";s:15:"anchor_two_link";s:0:"";s:6:"panels";a:3:{s:11:"subdivision";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"city";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"type";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}s:18:"search_subdivision";s:9:"Hillsboro";s:11:"search_city";a:1:{i:0;s:9:"Nashville";}s:3:"idx";s:9:"realtracs";s:14:"search_subtype";s:0:"";s:4:"file";s:0:"";s:7:"uploads";a:1:{i:0;s:2:"16";}}', 'fc-hillsboro-village', 0, 6, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
  ('Green Hills', 'Y', '', 'The neighbourhood of Green Hills is a very attractive and desirable place to live in, located in southwest Nashville the community has very easy access to downtown and therefore to all the perks the Music-City has to offer. From boutiques to malls, from lovely live-music neighbourhood pubs to upscale world class restaurants, Green Hills offers it all. Some of the city''s most desirable Real Estate can be found in the Green Hill area.', '', '', '', '', '', '', '', '', '', 'realtracs', 'a:24:{s:2:"id";s:1:"6";s:4:"feed";s:9:"realtracs";s:5:"title";s:11:"Green Hills";s:8:"subtitle";s:0:"";s:11:"description";s:436:"The neighbourhood of Green Hills is a very attractive and desirable place to live in, located in southwest Nashville the community has very easy access to downtown and therefore to all the perks the Music-City has to offer. From boutiques to malls, from lovely live-music neighbourhood pubs to upscale world class restaurants, Green Hills offers it all. Some of the city\'s most desirable Real Estate can be found in the Green Hill area.";s:7:"snippet";s:14:"fc-green-hills";s:7:"page_id";s:2:"35";s:13:"stats_heading";s:0:"";s:11:"stats_total";s:0:"";s:13:"stats_average";s:0:"";s:13:"stats_highest";s:0:"";s:12:"stats_lowest";s:0:"";s:15:"anchor_one_text";s:0:"";s:15:"anchor_two_text";s:0:"";s:15:"anchor_one_link";s:0:"";s:15:"anchor_two_link";s:0:"";s:6:"panels";a:3:{s:11:"subdivision";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"city";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"type";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}s:18:"search_subdivision";s:11:"Green Hills";s:11:"search_city";a:1:{i:0;s:9:"Nashville";}s:11:"search_type";a:2:{i:0;s:11:"Residential";i:1;s:11:"Condominium";}s:3:"idx";s:9:"realtracs";s:14:"search_subtype";s:0:"";s:4:"file";s:0:"";s:7:"uploads";a:1:{i:0;s:2:"17";}}', 'fc-green-hills', 0, 4, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
  ('Murfreesboro', 'Y', '', 'The City of Murfreesboro is part of the Nashville metropolitan area and has seen a huge population growth over the last decade. Murfreesboro is  often named as one of the fastest growing cities in the country. The city host several annual music events such as the very popular ''Main Street JazzFest''. Being home to Middle Tennessee State University, Murfreesboro sees a large and diverse student population with about 22,000 students being enrolled in MTSU.', '', '', '', '', '', '', '', '', '', 'realtracs', 'a:23:{s:2:"id";s:1:"7";s:4:"feed";s:9:"realtracs";s:5:"title";s:12:"Murfreesboro";s:8:"subtitle";s:0:"";s:11:"description";s:457:"The City of Murfreesboro is part of the Nashville metropolitan area and has seen a huge population growth over the last decade. Murfreesboro is  often named as one of the fastest growing cities in the country. The city host several annual music events such as the very popular \'Main Street JazzFest\'. Being home to Middle Tennessee State University, Murfreesboro sees a large and diverse student population with about 22,000 students being enrolled in MTSU.";s:7:"snippet";s:15:"fc-murfreesboro";s:7:"page_id";s:2:"37";s:13:"stats_heading";s:0:"";s:11:"stats_total";s:0:"";s:13:"stats_average";s:0:"";s:13:"stats_highest";s:0:"";s:12:"stats_lowest";s:0:"";s:15:"anchor_one_text";s:0:"";s:15:"anchor_two_text";s:0:"";s:15:"anchor_one_link";s:0:"";s:15:"anchor_two_link";s:0:"";s:6:"panels";a:3:{s:11:"subdivision";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"city";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"type";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}s:18:"search_subdivision";s:0:"";s:11:"search_city";a:1:{i:0;s:12:"Murfreesboro";}s:11:"search_type";a:2:{i:0;s:11:"Residential";i:1;s:11:"Condominium";}s:3:"idx";s:9:"realtracs";s:4:"file";s:0:"";s:7:"uploads";a:1:{i:0;s:2:"15";}}', 'fc-murfreesboro', 0, 5, '0000-00-00 00:00:00', '0000-00-00 00:00:00')
;

SET @community_id = LAST_INSERT_ID();
INSERT INTO `cms_uploads` (`type`, `row`, `file`, `size`, `order`, `timestamp`) VALUES
  ('community', @community_id + (@@auto_increment_increment * 0), 'community-1-1.jpg', 0, 1, NOW()),
  ('community', @community_id + (@@auto_increment_increment * 0), 'community-1-2.jpg', 0, 2, NOW()),
  ('community', @community_id + (@@auto_increment_increment * 0), 'community-1-3.jpg', 0, 3, NOW()),
  ('community', @community_id + (@@auto_increment_increment * 1), 'community-2-1.jpg', 0, 1, NOW()),
  ('community', @community_id + (@@auto_increment_increment * 2), 'community-3-1.jpg', 0, 1, NOW()),
  ('community', @community_id + (@@auto_increment_increment * 3), 'community-4-1.jpg', 0, 1, NOW()),
  ('community', @community_id + (@@auto_increment_increment * 4), 'community-5-1.jpg', 0, 1, NOW()),
  ('community', @community_id + (@@auto_increment_increment * 5), 'community-6-1.jpg', 0, 1, NOW())
;

-- Add IDX snippets for featured communities
INSERT INTO `snippets` (`agent`, `type`, `name`, `code`) VALUES
  (1, 'idx', 'idx-east-nashville', 'a:15:{s:2:"id";s:18:"idx-east-nashville";s:3:"map";a:4:{s:8:"latitude";s:17:"36.18082048110116";s:9:"longitude";s:18:"-86.74412148632803";s:4:"zoom";s:2:"11";s:4:"open";s:1:"1";}s:4:"feed";s:9:"realtracs";s:14:"search_subtype";s:0:"";s:6:"panels";a:3:{s:4:"city";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:11:"subdivision";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"type";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}s:11:"search_city";a:1:{i:0;s:9:"Nashville";}s:18:"search_subdivision";s:14:"East Nashville";s:11:"search_type";a:2:{i:0;s:11:"Residential";i:1;s:11:"Condominium";}s:3:"idx";s:9:"realtracs";s:10:"snippet_id";s:18:"idx-east-nashville";s:13:"snippet_title";s:23:"East Nashville Listings";s:10:"page_limit";s:1:"6";s:7:"sort_by";s:17:"DESC-ListingPrice";s:4:"view";s:8:"detailed";s:12:"price_ranges";s:5:"false";}'),
  (1, 'idx', 'idx-hendersonville', 'a:13:{s:2:"id";s:18:"idx-hendersonville";s:3:"map";a:4:{s:8:"latitude";s:17:"40.58473873279583";s:9:"longitude";s:19:"-105.08442300000002";s:4:"zoom";s:2:"12";s:4:"open";s:1:"1";}s:4:"feed";s:9:"realtracs";s:6:"panels";a:2:{s:4:"city";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"type";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}s:11:"search_city";a:1:{i:0;s:14:"Hendersonville";}s:11:"search_type";a:2:{i:0;s:11:"Residential";i:1;s:11:"Condominium";}s:3:"idx";s:9:"realtracs";s:10:"snippet_id";s:18:"idx-hendersonville";s:13:"snippet_title";s:32:"Homes for Sale in Hendersonville";s:10:"page_limit";s:1:"6";s:7:"sort_by";s:17:"DESC-ListingPrice";s:4:"view";s:4:"grid";s:12:"price_ranges";s:4:"true";}'),
  (1, 'idx', 'idx-mount-juliet', 'a:14:{s:2:"id";s:16:"idx-mount-juliet";s:3:"map";a:4:{s:8:"latitude";s:17:"40.58447799766964";s:9:"longitude";s:19:"-105.08442300000002";s:4:"zoom";s:2:"12";s:4:"open";s:1:"1";}s:4:"feed";s:9:"realtracs";s:6:"panels";a:2:{s:4:"type";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"city";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}s:11:"search_type";a:2:{i:0;s:11:"Residential";i:1;s:11:"Condominium";}s:3:"idx";s:9:"realtracs";s:14:"search_subtype";s:0:"";s:11:"search_city";a:1:{i:0;s:12:"Mount Juliet";}s:10:"snippet_id";s:16:"idx-mount-juliet";s:13:"snippet_title";s:30:"Homes for Sale in Mount Juliet";s:10:"page_limit";s:1:"6";s:7:"sort_by";s:17:"DESC-ListingPrice";s:4:"view";s:4:"grid";s:12:"price_ranges";s:4:"true";}'),
  (1, 'idx', 'idx-hillsboro-village', 'a:16:{s:2:"id";s:21:"idx-hillsboro-village";s:3:"map";a:4:{s:8:"latitude";s:16:"40.5846083653597";s:9:"longitude";s:19:"-105.08442300000002";s:4:"zoom";s:2:"12";s:4:"open";s:1:"1";}s:4:"feed";s:9:"realtracs";s:6:"panels";a:4:{s:8:"location";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"type";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:4:"city";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}s:11:"subdivision";a:3:{s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";s:6:"hidden";s:1:"0";}}s:15:"search_location";s:17:"Hillsboro Village";s:11:"search_type";a:2:{i:0;s:11:"Residential";i:1;s:11:"Condominium";}s:3:"idx";s:9:"realtracs";s:14:"search_subtype";s:0:"";s:11:"search_city";a:1:{i:0;s:9:"Nashville";}s:18:"search_subdivision";s:17:"Hillsboro Village";s:10:"snippet_id";s:21:"idx-hillsboro-village";s:13:"snippet_title";s:35:"Homes for Sale in Hillsboro Village";s:10:"page_limit";s:1:"6";s:7:"sort_by";s:17:"DESC-ListingPrice";s:4:"view";s:4:"grid";s:12:"price_ranges";s:4:"true";}'),
  (1, 'idx', 'idx-green-hills', 'a:11:{s:4:"feed";s:9:"realtracs";s:3:"map";a:4:{s:9:"longitude";s:18:"-86.78444320000001";s:8:"latitude";s:18:"36.165751315626295";s:4:"zoom";s:2:"12";s:4:"open";s:1:"0";}s:13:"snippet_title";s:11:"Green Hills";s:10:"snippet_id";s:15:"idx-green-hills";s:6:"panels";a:1:{s:11:"subdivision";a:3:{s:6:"hidden";s:1:"0";s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";}}s:18:"search_subdivision";s:11:"Green Hills";s:10:"page_limit";s:2:"12";s:7:"sort_by";s:17:"DESC-ListingPrice";s:4:"view";s:8:"detailed";s:12:"price_ranges";s:5:"false";s:9:"hide_tags";s:5:"false";}'),
  (1, 'idx', 'idx-murfreesboro', 'a:11:{s:4:"feed";s:9:"realtracs";s:3:"map";a:4:{s:9:"longitude";s:18:"-86.78444320000001";s:8:"latitude";s:18:"36.165751315626295";s:4:"zoom";s:2:"12";s:4:"open";s:1:"0";}s:13:"snippet_title";s:12:"Murfreesboro";s:10:"snippet_id";s:16:"idx-murfreesboro";s:6:"panels";a:1:{s:4:"city";a:3:{s:6:"hidden";s:1:"0";s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";}}s:11:"search_city";a:1:{i:0;s:12:"Murfreesboro";}s:10:"page_limit";s:2:"12";s:7:"sort_by";s:17:"DESC-ListingPrice";s:4:"view";s:8:"detailed";s:12:"price_ranges";s:5:"false";s:9:"hide_tags";s:5:"false";}')
;

-- Add CMS pages for featured communities
INSERT INTO `pages` (`agent`, `category`, `file_name`, `link_name`, `page_title`, `category_html`, `hide`, `is_main_cat`) VALUES
  (1, 'communities', 'east-nashville', 'East Nashville', 'East Nashville', '<p>#fc-east-nashville#</p><p>#idx-east-nashville#</p>', 'f', 'f'),
  (1, 'communities', 'hendersonville', 'Hendersonville', 'Hendersonville Real Estate', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec laoreet orci vitae ipsum euismod lacinia. Duis sit amet metus vitae erat efficitur congue. Vivamus non dapibus velit. Donec porta arcu nec dui sodales commodo. Fusce non odio libero. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi malesuada eget quam quis auctor. Maecenas malesuada varius felis, non blandit justo ultrices ut. Mauris pulvinar consequat mi, at malesuada nulla molestie et. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin at gravida mi, ut porta mauris. In commodo in ipsum a laoreet. Sed nisi mauris, feugiat non ipsum vel, aliquet commodo sapien. Ut at interdum enim, id ultrices lacus.</p>\r\n<p>#idx-hendersonville#</p>', 'f', 'f'),
  (1, 'communities', 'mount-juliet', 'Mount Juliet', 'Mount Juliet', '<p>#fc-mount-juliet#</p><p>#idx-mount-juliet#</p>', 'f', 'f'),
  (1, 'communities', 'hillsboro-village', 'Hillsboro Village', 'Hillsboro Village', '<p>#fc-hillsboro-village#</p><p>#idx-hillsboro-village#</p>', 'f', 'f'),
  (1, 'communities', 'green-hills', 'Green Hills', 'Green Hills', '<p>#fc-green-hills#</p><p>#idx-green-hills#</p>', 'f', 'f'),
  (1, 'communities', 'murfreesboro', 'Murfreesboro', 'Murfreesboro', '<p>#fc-murfreesboro#</p><p>#idx-murfreesboro#</p>', 'f', 'f')
;

-- Assign communities to pages
SET @page_id = LAST_INSERT_ID();
UPDATE `featured_communities` SET `page_id` = @page_id + (@@auto_increment_increment * 0) WHERE `title` = 'East Nashville';
UPDATE `featured_communities` SET `page_id` = @page_id + (@@auto_increment_increment * 1) WHERE `title` = 'Hendersonville';
UPDATE `featured_communities` SET `page_id` = @page_id + (@@auto_increment_increment * 2) WHERE `title` = 'Mount Juliet';
UPDATE `featured_communities` SET `page_id` = @page_id + (@@auto_increment_increment * 3) WHERE `title` = 'Hillsboro Village';
UPDATE `featured_communities` SET `page_id` = @page_id + (@@auto_increment_increment * 4) WHERE `title` = 'Green Hills';
UPDATE `featured_communities` SET `page_id` = @page_id + (@@auto_increment_increment * 5) WHERE `title` = 'Murfreesboro';

-- ---------------------
-- Cover Feature Demo Pages
-- ---------------------

-- Cover Feature Uploads
INSERT INTO `cms_uploads` (`type`, `row`, `file`, `size`, `order`, `timestamp`) VALUES
  ('variable', NULL, 'cover-360.jpg', '2968055', '5', NOW()),
  ('variable', NULL, 'cover-photo.jpg', '3214093', '6', NOW()),
  ('variable', NULL, 'cover-slideshow-1.jpg', '1280110', '7', NOW()),
  ('variable', NULL, 'cover-slideshow-2.jpg', '1036587', '8', NOW()),
  ('variable', NULL, 'cover-slideshow-3.jpg', '2199692', '9', NOW()),
  ('variable', NULL, 'cover-panorama.jpg', '2437501', '10', NOW())
;

-- Cover Feature Demo Pages
SET @upload_id = LAST_INSERT_ID();
INSERT INTO `pages` (`agent`, `category`, `file_name`, `link_name`, `page_title`, `category_html`, `hide`, `is_main_cat`, `template`, `variables`) VALUES
  (1, NULL, 'demos', 'Demos', 'Demos', '<h1>Demos</h1><h2>Cover Pages</h2><ul><li><a href="/cover-photo.php">Photo</a></li><li><a href="/cover-slideshow.php">Slideshow</a></li><li><a href="/cover-video.php">Video</a></li><li><a href="/cover-panoramic.php">Panoramic</a></li><li><a href="/cover-360.php">360 Photo</a></li></ul>', 't', 't', 'basic', '{"showNav":"0"}'),
  (1, 'demos', 'cover-video', 'Cover, Video', 'Demo: Cover Video', '', 't', 'f', 'cover', '{"background":"video","background.slides":["{\\"image\\":\\"\\",\\"#\\":1}"],"background.video_id":"AoPiLg8DZ3A","background.video_mute":"true","foreground":"search","foreground.horizontal":"center","foreground.vertical":"middle","foreground.preheading":"","foreground.heading":"","foreground.intro":""}'),
  (1, 'demos', 'cover-360', 'Cover, 360 Photo', 'Demo: Cover 360 Photo', '', 't', 'f', 'cover', CONCAT('{"background":"360","background.slides":["{\\"image\\":\\"\\",\\"#\\":1}"],"background.360_image":"', (@upload_id), '","foreground":"search","foreground.horizontal":"center","foreground.vertical":"middle","foreground.preheading":"","foreground.heading":"","foreground.intro":""}')),
  (1, 'demos', 'cover-photo', 'Cover, Photo', 'Demo: Cover Photo', '', 't', 'f', 'cover', CONCAT('{"background":"photo","background.slides":["{\\"image\\":\\"\\",\\"#\\":1}"],"background.image":"', (@upload_id +2), '","foreground":"search","foreground.horizontal":"center","foreground.vertical":"middle","foreground.preheading":"","foreground.heading":"","foreground.intro":""}')),
  (1, 'demos', 'cover-slideshow', 'Cover, Slideshow', 'Demo: Cover Slideshow', '', 't', 'f', 'cover', CONCAT('{"background":"slideshow","background.slides":["{\\"image\\":', (@upload_id + 4), '}","{\\"image\\":', (@upload_id + 6), '}","{\\"image\\":', (@upload_id + 8), '}"],"foreground":"search","foreground.horizontal":"center","foreground.vertical":"middle","foreground.preheading":"","foreground.heading":"","foreground.intro":""}')),
  (1, 'demos', 'cover-panoramic', 'Cover, Panoramic', 'Demo: Cover Panoramic', '', 't', 'f', 'cover', CONCAT('{"background":"pano","background.slides":["{\\"image\\":\\"\\",\\"#\\":1}"],"background.pano_image":"', (@upload_id + 10), '","foreground":"search","foreground.horizontal":"center","foreground.vertical":"middle","foreground.preheading":"","foreground.heading":"","foreground.intro":""}'))
;

-- -----------------
-- IDX Snippet Pages
-- -----------------

INSERT INTO `pages` (`agent`, `category`, `file_name`, `link_name`, `page_title`, `category_html`, `hide`, `is_main_cat`) VALUES
  (1, 'polygon-snippet', 'polygon-snippet', 'Polygon Snippet', 'Polygon Snippet', '<p>#polygon-snippet#</p>', 't', 't'),
  (1, 'radius-snippet', 'radius-snippet', 'Radius Snippet', 'Radius Snippet', '<p>#radius-snippet#</p>', 't', 't'),
  (1, 'idx-snippets', 'idx-snippets', 'IDX Snippets', 'IDX Snippets', '<p>#idx-snippet#</p><p>#radius-snippet#</p>', 't', 't'),
  (1, 'idx-snippet', 'idx-snippet', 'IDX Snippet', 'IDX Snippet', '<p>#idx-snippet#</p>', 't', 't')
;

INSERT INTO `snippets` (`agent`, `type`, `name`, `code`) VALUES
  (1, 'idx', 'polygon-snippet', 'a:16:{s:5:"agent";s:1:"1";s:4:"feed";s:3:"mfr";s:2:"id";s:15:"polygon-snippet";s:3:"map";a:5:{s:9:"longitude";s:18:"-80.92673474586205";s:8:"latitude";s:17:"29.25970646126696";s:4:"zoom";s:2:"10";s:7:"polygon";s:178:"["29.511330027309146 -81.05712890625,29.43002940457176 -81.23291015625,28.94086176940557 -80.9747314453125,29.03696064855827 -80.716552734375,29.511330027309146 -81.05712890625"]";s:4:"open";s:1:"1";}s:13:"snippet_title";s:15:"Polygon Snippet";s:10:"snippet_id";s:15:"polygon-snippet";s:6:"panels";a:3:{s:7:"polygon";a:3:{s:6:"hidden";s:1:"0";s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";}s:5:"price";a:3:{s:6:"hidden";s:1:"0";s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";}s:5:"rooms";a:3:{s:6:"hidden";s:1:"0";s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";}}s:13:"minimum_price";s:6:"250000";s:13:"maximum_price";s:0:"";s:16:"minimum_bedrooms";s:1:"1";s:17:"minimum_bathrooms";s:1:"1";s:10:"page_limit";s:1:"8";s:7:"sort_by";s:17:"DESC-ListingPrice";s:4:"view";s:8:"detailed";s:12:"price_ranges";s:5:"false";s:9:"hide_tags";s:5:"false";}'),
  (1, 'idx', 'radius-snippet', 'a:16:{s:5:"agent";s:1:"1";s:4:"feed";s:3:"mfr";s:2:"id";s:14:"radius-snippet";s:3:"map";a:5:{s:9:"longitude";s:18:"-82.58266621864351";s:8:"latitude";s:17:"27.88099973717571";s:4:"zoom";s:1:"9";s:6:"radius";s:60:"["27.928900753321876,-82.46063232421875,14.891268651590307"]";s:4:"open";s:1:"1";}s:13:"snippet_title";s:14:"Radius Snippet";s:10:"snippet_id";s:14:"radius-snippet";s:6:"panels";a:3:{s:5:"price";a:3:{s:6:"hidden";s:1:"0";s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";}s:5:"rooms";a:3:{s:6:"hidden";s:1:"0";s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";}s:6:"radius";a:3:{s:6:"hidden";s:1:"0";s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";}}s:13:"minimum_price";s:6:"250000";s:13:"maximum_price";s:0:"";s:16:"minimum_bedrooms";s:1:"1";s:17:"minimum_bathrooms";s:1:"1";s:10:"page_limit";s:1:"8";s:7:"sort_by";s:17:"DESC-ListingPrice";s:4:"view";s:4:"grid";s:12:"price_ranges";s:5:"false";s:9:"hide_tags";s:5:"false";}'),
  (1, 'idx', 'idx-snippet', 'a:14:{s:5:"agent";s:1:"1";s:4:"feed";s:6:"carets";s:2:"id";s:11:"idx-snippet";s:3:"map";a:4:{s:9:"longitude";s:19:"-118.34095001220999";s:8:"latitude";s:17:"34.00373872275402";s:4:"zoom";s:2:"12";s:4:"open";s:1:"0";}s:13:"snippet_title";s:11:"IDX Snippet";s:10:"snippet_id";s:11:"idx-snippet";s:6:"panels";a:1:{s:5:"price";a:3:{s:6:"hidden";s:1:"0";s:7:"display";s:1:"1";s:9:"collapsed";s:1:"0";}}s:13:"minimum_price";s:6:"300000";s:13:"maximum_price";s:0:"";s:10:"page_limit";s:2:"12";s:7:"sort_by";s:17:"DESC-ListingPrice";s:4:"view";s:4:"grid";s:12:"price_ranges";s:4:"true";s:9:"hide_tags";s:5:"false";}')
;

-- ----------
-- Associates
-- ----------

-- Associate Auth
INSERT INTO `auth` (`type`, `username`, `password`, `last_logon`, `timestamp_created`, `timestamp_updated`) VALUES
  ('Associate', 'associate1', @password, NOW(), NOW(), NOW()),
  ('Associate', 'associate2', @password, NOW() - INTERVAL 1 HOUR, NOW(), NOW()),
  ('Associate', 'associate3', @password, NOW() - INTERVAL 24 HOUR, NOW() - INTERVAL 48 HOUR, NOW() - INTERVAL 48 HOUR),
  ('Associate', 'associate4', @password, NULL, NOW() - INTERVAL 72 HOUR, NOW() - INTERVAL 72 HOUR)
;
SET @auth_id = LAST_INSERT_ID();

-- Associate Rows
INSERT INTO `associates` (`auth`, `first_name` ,`last_name`, `email`, `office_phone`) VALUES
  (@auth_id + (@@auto_increment_increment * 0), 'Richard', 'Stewart', 'demoassociate1@rewdemo.com', '1-877-753-9893'),
  (@auth_id + (@@auto_increment_increment * 1), 'Kimberly', 'Steele', 'demoassociate2@rewdemo.com', '250.753.9893'),
  (@auth_id + (@@auto_increment_increment * 2), 'Brooke', 'Ripley', 'demoassociate3@rewdemo.com', ''),
  (@auth_id + (@@auto_increment_increment * 3), 'Candace', 'Walker', 'demoassociate4@rewdemo.com', '')
;

-- Associate Photos
SET @associate_id = LAST_INSERT_ID();
INSERT INTO `cms_uploads` (`type`, `row`, `file`, `size`, `order`, `timestamp`) VALUES
  ('associate', @associate_id + (@@auto_increment_increment * 0), 'associate-1.png', 908806, 1, NOW()),
  ('associate', @associate_id + (@@auto_increment_increment * 1), 'associate-2.png', 943686, 2, NOW()),
  ('associate', @associate_id + (@@auto_increment_increment * 2), 'associate-3.png', 958681, 3, NOW())
;

-- -------
-- Lenders
-- -------

-- Lender Auth
INSERT INTO `auth` (`type`, `username`, `password`, `last_logon`, `timestamp_created`, `timestamp_updated`) VALUES
  ('Lender', 'lender1', @LENDER_PASSWORD, NOW(), NOW(), NOW()),
  ('Lender', 'lender2', @LENDER_PASSWORD, NOW() - INTERVAL 1 HOUR, NOW(), NOW()),
  ('Lender', 'lender3', @LENDER_PASSWORD, NOW() - INTERVAL 24 HOUR, NOW() - INTERVAL 48 HOUR, NOW() - INTERVAL 48 HOUR),
  ('Lender', 'lender4', @LENDER_PASSWORD, NULL, NOW() - INTERVAL 72 HOUR, NOW() - INTERVAL 72 HOUR)
;
SET @auth_id = LAST_INSERT_ID();

-- Lender Rows
INSERT INTO `lenders` (`auth`, `first_name` ,`last_name`, `email`, `office_phone`) VALUES
  (@auth_id + (@@auto_increment_increment * 0), 'Allison', 'Jacobs', 'demolender1@rewdemo.com', '1-877-753-9893'),
  (@auth_id + (@@auto_increment_increment * 1), 'Jeremy', 'Reichman', 'demolender2@rewdemo.com', '250.753.9893'),
  (@auth_id + (@@auto_increment_increment * 2), 'Victoria', 'Lasserre', 'demolender3@rewdemo.com', ''),
  (@auth_id + (@@auto_increment_increment * 3), 'Rick', 'Williamson', 'demolender4@rewdemo.com', '')
;
SET @lender_id = LAST_INSERT_ID();

-- Lender Photos
INSERT INTO `cms_uploads` (`type`, `row`, `file`, `size`, `order`, `timestamp`) VALUES
  ('lender', @lender_id + (@@auto_increment_increment * 0), 'lender-1.png', 908806, 1, NOW()),
  ('lender', @lender_id + (@@auto_increment_increment * 1), 'lender-2.png', 943686, 2, NOW()),
  ('lender', @lender_id + (@@auto_increment_increment * 2), 'lender-3.png', 958681, 3, NOW())
;