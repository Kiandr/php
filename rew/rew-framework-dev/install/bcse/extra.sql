-- Insert BCSE Snippets
INSERT INTO `snippets` (`agent`, `name`, `code`, `type`) VALUES
	(1, 'communities', '', 'module'),
	(1, 'navigation', '', 'cms'),
	(1, 'phone-number', '', 'cms'),
	(1, 'social-media', '', 'cms'),
	(1, 'cta-about', '', 'cms'),
	(1, 'cta-address', '', 'cms'),
	(1, 'cta-contact', '', 'cms'),
	(1, 'cta-search', '', 'cms'),
	(1, 'cta-cma', '', 'cms')
;

-- Update Homepage Settings
UPDATE `default_info` SET `template` = 'homepage', `variables` = '{}', `category_html` = '' WHERE `agent` = 1;

-- Set 'detailed' as default search results view, and remove default registration page text
UPDATE `rewidx_defaults` SET `view` = 'detailed';
UPDATE `rewidx_system` SET `copy_register` = '';

-- Make IDX registration forced (Yes, Always)
UPDATE rewidx_system SET registration = 'true';

-- Add #communities# snippet to /communities.php
UPDATE `pages` SET `category_html` = CONCAT(REPLACE(`category_html`, '#cta-communities#', '#communities#'), '<p>#cta-communities#</p>') WHERE `file_name` = 'communities';

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

-- Insert rate-sellers form for RATE module Snippet (uses #form-rate-seller#)
INSERT INTO `snippets` (`agent`, `name`, `code`, `type`) VALUES
	(NULL, 'form-guaranteed', '<form action="?submit" method="post">\n\n	<h1>Guaranteed Sold</h1>\n	<h4 align="center">Fill out the form below to apply for our Guaranteed Sold program:</h4>\n\n    <input type="hidden" name="guaranteedsoldform" value="true">\n	<input class="hidden" name="email" value="" autocomplete="off">\n\n    <div class="step one">\n\n	    <fieldset>\n\n			<h4>Contact Information</h4>\n\n			<div class="field x6">\n				<label>First Name</label>\n				<input name="onc5khko" value="">\n			</div>\n\n			<div class="field x6 last">\n				<label>Last Name</label>\n				<input name="sk5tyelo" value="">\n			</div>\n\n			<div class="field x6">\n				<label>Email <small class="required">*</small></label>\n				<input type="email" name="mi0moecs" value="" required>\n				<small>Please provide a valid email address.</small>\n			</div>\n\n			<div class="field x6 last">\n				<label>Phone</label>\n				<input type="tel" name="telephone" value="">\n			</div>\n\n			<div class="field x6">\n				<label>Address</label>\n				<input name="fm-addr" value="">\n			</div>\n\n			<div class="field x6 last">\n				<label>City</label>\n				<input name="fm-town" value="">\n			</div>\n\n			<div class="field x6">\n				<label>State</label>\n				<input name="fm-state" value="">\n			</div>\n\n			<div class="field x6 last">\n				<label>Zip Code</label>\n				<input name="fm-postcode" value="">\n			</div>\n\n		</fieldset>\n\n        <fieldset class="last">\n		\n			<h4>Property Information</h4>\n        \n			<div class="field x6">\n                <label>Number of Bedrooms</label>\n                <select name="bedrooms">\n                    <option value="1">1</option>\n                    <option value="2">2</option>\n                    <option value="3">3</option>\n                    <option value="4">4</option>\n                    <option value="5">5</option>\n                    <option value="6">6</option>\n                    <option value="7+">7+</option>\n                </select>\n            </div>\n\n            <div class="field x6 last">\n                <label>Number of Bathrooms</label>\n                <select name="bathrooms">\n                    <option value="1">1</option>\n                    <option value="2">2</option>\n                    <option value="3">3</option>\n                    <option value="4">4</option>\n                    <option value="5">5</option>\n                    <option value="6">6</option>\n                    <option value="7+">7+</option>\n                </select>\n            </div>\n\n            <div class="field x6">\n                <label>Square Feet</label>\n                <select name="square_feet">\n                    <option value="Less than 1000">&lt; 1000</option>\n                    <option value="1000 - 1500">1000 - 1500</option>\n                    <option value="1500 - 2000">1500 - 2000</option>\n                    <option value="2000 - 2500">2000 - 2500</option>\n                    <option value="2500 - 3000">2500 - 3000</option>\n                    <option value="3000 - 3500">3000 - 3500</option>\n                    <option value="3500 - 4000">3500 - 4000</option>\n                    <option value="4000 - 4500">4000 - 4500</option>\n                    <option value="4500 - 5000">4500 - 5000</option>\n                    <option value="5000 - 6000">5000 - 6000</option>\n                    <option value="6000 - 7000">6000 - 7000</option>\n                    <option value="7000 - 8000">7000 - 8000</option>\n                    <option value="8000 - 9000">8000 - 9000</option>\n                    <option value="9000 - 10,000">9000 - 10,000</option>\n                    <option value="10,000 +">10,000 +</option>\n                </select>\n            </div>\n\n            <div class="field x6 last">\n                <label>Property Type</label>\n                <select name="type_of_property">\n                    <option value="house">House</option>\n                    <option value="condo">Condo</option>\n                    <option value="land">Land</option>\n                    <option value="townhome">Townhome</option>\n                </select>\n            </div>\n\n            <div class="field x6">\n                <label>Price Range</label>\n                <select name="price_range">\n                    <option value="Less than $300,000">Less than $300,000</option>\n                    <option value="$300,000 - $500,000">$300,000 - $500,000</option>\n                    <option value="$500,000 - $700,000">$500,000 - $700,000</option>\n                    <option value="$700,000 - $900,000">$700,000 - $900,000</option>\n                    <option value="$900,000 - $1,000,000">$900,000 - $1,000,000</option>\n                    <option value="$1,000,000 - $2,000,000">$1,000,000 - $2,000,000</option>\n                    <option value="$1,000,000 - $2,000,000">$1,000,000 - $2,000,000</option>\n                    <option value="$2,000,000 - $3,000,000">$2,000,000 - $3,000,000</option>\n                    <option value="$3,000,000 - $4,000,000">$3,000,000 - $4,000,000</option>\n                    <option value="$4,000,000 - $5,000,000">$4,000,000 - $5,000,000</option>\n                    <option value="Over $5,000,000">Over $5,000,000</option>\n                </select>\n            </div>\n\n            <div class="field x6 last">\n                <label>When do you plan to sell?</label>\n                <select name="when_do_you_plan_to_sell">\n                    <option value="3 Months">3 Months</option>\n                    <option value="6 Months">6 Months</option>\n                    <option value="9 Months">9 Months</option>\n                    <option value="1 Year">1 Year</option>\n                    <option value="1 Year+">1 Year+</option>\n                </select>\n            </div>\n\n        </fieldset>\n\n        <fieldset>\n\n            <h4>Additional Information</h4>\n\n            <div class="field x12">\n                <textarea cols="32" rows="4" name="Comments" placeholder="Please list the additional amenities of your house"></textarea>\n            </div>\n\n            <div class="field x12">\n                {opt_in}\n            </div>\n\n            <div>\n                <button type="submit" class="positive">Submit <i class="icon-chevron-sign-right"></i></button>\n            </div>\n\n        </fieldset>\n    </div>\n</form>', 'form')
;

-- Insert rate-sellers form autoresponder Autoresponder (uses id 13)
INSERT INTO `auto_responders` (`id`, `title`, `from`, `from_name`, `from_email`, `cc_email`, `bcc_email`, `subject`, `document`, `tempid`, `is_html`, `active`) VALUES
	(13, 'Guaranteed Sold Form', 'agent', '', '', '', '', 'Welcome to the site!', '<p>Hello</p>\n<p>Thank you for your interest in the Guaranteed Sold Program. We will review all of the details that you have submitted, and provide you further information regarding the program. This will help gauge your eligibility, and help you take your first steps in selling your home.</p>\n<p>We will be in contact very soon. Take care!</p>', NULL, 'true', 'Y')
;
