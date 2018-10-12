<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

if (!Skin::hasFeature(Skin::INSTALL_GUARANTEED_SNIPPET) && !Skin::hasFeature(Skin::INSTALL_RADIO_LANDING_PAGE)) {
	echo 'This patch is only needed to install the Guaranteed Sold form and Radio Landing Page';
    return;
}

// DB connection
$db = DB::get('cms');

// Snippet name
$snippet_name = 'form-guaranteed';
$snippet_code =
'<form action="?submit" method="post">

    <input type="hidden" name="guaranteedsoldform" value="true">
	<input class="hidden" name="email" value="" autocomplete="off">

    <div class="step one">

		<fieldset>

			<h4>Your Contact Information</h4>

			<div class="field x6">
				<label>First Name</label>
				<input name="onc5khko" value="">
			</div>

			<div class="field x6 last">
				<label>Last Name</label>
				<input name="sk5tyelo" value="">
			</div>

			<div class="field x6">
				<label>Email <small class="required">*</small></label>
				<input type="email" name="mi0moecs" value="" required>
				<small>Please provide a valid email address.</small>
			</div>

			<div class="field x6 last">
				<label>Phone</label>
				<input type="tel" name="telephone" value="">
			</div>

			<div class="field x6">
				<label>Address</label>
				<input name="fm-addr" value="">
			</div>

			<div class="field x6 last">
				<label>City</label>
				<input name="fm-town" value="">
			</div>

			<div class="field x6">
				<label>State</label>
				<input name="fm-state" value="">
			</div>

			<div class="field x6 last">
				<label>Zip Code</label>
				<input name="fm-postcode" value="">
			</div>

		</fieldset>

        <fieldset class="last">

            <div class="field x6">
                <label>Number of Bedrooms</label>
                <select name="bedrooms">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7+">7+</option>
                </select>
            </div>

            <div class="field x6 last">
                <label>Number of Bathrooms</label>
                <select name="bathrooms">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7+">7+</option>
                </select>
            </div>

            <div class="field x6">
                <label>Square Feet</label>
                <select name="square_feet">
                    <option value="Less than 1000">&lt; 1000</option>
                    <option value="1000 - 1500">1000 - 1500</option>
                    <option value="1500 - 2000">1500 - 2000</option>
                    <option value="2000 - 2500">2000 - 2500</option>
                    <option value="2500 - 3000">2500 - 3000</option>
                    <option value="3000 - 3500">3000 - 3500</option>
                    <option value="3500 - 4000">3500 - 4000</option>
                    <option value="4000 - 4500">4000 - 4500</option>
                    <option value="4500 - 5000">4500 - 5000</option>
                    <option value="5000 - 6000">5000 - 6000</option>
                    <option value="6000 - 7000">6000 - 7000</option>
                    <option value="7000 - 8000">7000 - 8000</option>
                    <option value="8000 - 9000">8000 - 9000</option>
                    <option value="9000 - 10,000">9000 - 10,000</option>
                    <option value="10,000 +">10,000 +</option>
                </select>
            </div>

            <div class="field x6 last">
                <label>Property Type</label>
                <select name="type_of_property">
                    <option value="house">House</option>
                    <option value="condo">Condo</option>
                    <option value="land">Land</option>
                    <option value="townhome">Townhome</option>
                </select>
            </div>

            <div class="field x6">
                <label>Price Range</label>
                <select name="price_range">
                    <option value="Less than $300,000">Less than $300,000</option>
                    <option value="$300,000 - $500,000">$300,000 - $500,000</option>
                    <option value="$500,000 - $700,000">$500,000 - $700,000</option>
                    <option value="$700,000 - $900,000">$700,000 - $900,000</option>
                    <option value="$900,000 - $1,000,000">$900,000 - $1,000,000</option>
                    <option value="$1,000,000 - $2,000,000">$1,000,000 - $2,000,000</option>
                    <option value="$1,000,000 - $2,000,000">$1,000,000 - $2,000,000</option>
                    <option value="$2,000,000 - $3,000,000">$2,000,000 - $3,000,000</option>
                    <option value="$3,000,000 - $4,000,000">$3,000,000 - $4,000,000</option>
                    <option value="$4,000,000 - $5,000,000">$4,000,000 - $5,000,000</option>
                    <option value="Over $5,000,000">Over $5,000,000</option>
                </select>
            </div>

            <div class="field x6 last">
                <label>When do you plan to sell?</label>
                <select name="when_do_you_plan_to_sell">
                    <option value="3 Months">3 Months</option>
                    <option value="6 Months">6 Months</option>
                    <option value="9 Months">9 Months</option>
                    <option value="1 Year">1 Year</option>
                    <option value="1 Year+">1 Year+</option>
                </select>
            </div>

        </fieldset>

    </div>

    <div class="step two">

        <fieldset>

            <h4>Comments</h4>

            <div class="field x12">
                <textarea cols="32" rows="4" name="Comments" placeholder="Please list the additional amenities of your house"></textarea>
            </div>

        </fieldset>

        <div class="formEnd">

            <div class="field x12">
                {opt_in}
            </div>

            <div>
                <button type="submit" class="positive">Submit <i class="icon-chevron-sign-right"></i></button>
            </div>

        </div>

    </div>
</form>';
$snippet_type = 'form';

if (Skin::hasFeature(Skin::INSTALL_GUARANTEED_SNIPPET)) {
	// Output
	echo 'Processing #' . $snippet_name . '# update for guaranteed sold form' . PHP_EOL;

	// Check if snippet exists
	$select = $db->prepare("SELECT `id` FROM `snippets` WHERE `name` = :name AND `agent` IS NULL;");
	$select->execute(array('name' => $snippet_name));
	$snippet = $select->fetch();

	if (empty($snippet)) {
		// Add snippet to database
		$insert = $db->prepare("INSERT INTO `snippets` SET `name` = :name, `code` = :code, `type` = :type, `agent` = NULL;");
		$insert->execute(array('name' => $snippet_name, 'code' => $snippet_code, 'type' => $snippet_type));

		// Success
		echo 'Added #' . $snippet_name . '#' . PHP_EOL;

	} else {
		// Error
		throw new Exception ('#' . $snippet_name . '# snippet already exists');

	}

	// Autoresponder name
	$autoresponder_name = 'Guaranteed Sold Form';
	$autoresponder_id = 13;
	$autoresponder_subject = 'Welcome to the site!';
	$autoresponder_body = '<p>Hi,</p><br><p>Thank you for your interest in the Guaranteed Sold Program. We will review all of the details that you have provided, and provide you further details regarding the program. This will help gauge your eligibility, and help you take your make the first steps necessary to sell your home.</p><br><p>We will be in contact very soon. Take care!</p>';
	$autoresponder_active = 'Y';

	// Output
	echo 'Processing Guaranteed Sold Form autoresponder' . PHP_EOL;

	// Check if snippet exists
	$select = $db->prepare("SELECT `id` FROM `auto_responders` WHERE `id` = :id;");
	$select->execute(array('id' => $autoresponder_id));
	$auto_responder = $select->fetch();
	if (empty($auto_responder)) {

		// Add snippet to database
		$insert = $db->prepare("INSERT INTO `auto_responders` SET `id` = :id, `title` = :title, `subject` = :subject, `document` = :document, `active`=:active;");
		$insert->execute(array('id' => $autoresponder_id, 'title' => $autoresponder_name, 'subject' => $autoresponder_subject, 'document' => $autoresponder_body, 'active' => $autoresponder_active));

		// Success
		echo 'Added #' . $snippet_name . '#' . PHP_EOL;

	} else {
		// Error
		throw new Exception ('#' . $autoresponder_name . '# autoresponder already exists');
	}
}

if (Skin::hasFeature(Skin::INSTALL_RADIO_LANDING_PAGE) && !empty(Settings::getInstance()->MODULES['REW_RADIO_LANDING_PAGE'])) {

	// Output
	echo 'Processing Radio page' . PHP_EOL;

	// Check if snippet exists
	$select = $db->prepare("SELECT `file_name` FROM `pages` WHERE `file_name` = :file_name;");
	$select->execute(array('file_name' => 'radio'));
	$auto_responder = $select->fetch();
	if (empty($page)) {

		//Insert radio page Page (filename = radio)
		$insert_page = $db->prepare("INSERT INTO `pages` SET `agent` = :agent, `file_name` = :file_name, `page_title` = :page_title, `link_name` = :link_name, `category_html` = :category_html, `template` = :template, `hide` = :hide, `hide_sitemap` = :hide_sitemap, `hide_slideshow` = :hide_slideshow, `category` = :category, `is_main_cat` = :is_main_cat, `is_link` = :is_link;");
		$insert_page->execute(array('agent' => 1, 'file_name' => 'radio', 'page_title' => 'Radio', 'link_name' => 'Radio Landing Page', 'category_html' => '<p>#radio-landing-page#</p>', 'template' => '1col', 'hide' => 't', 'hide_sitemap' => 't', 'hide_slideshow' => 'f', 'category' => 'radio', 'is_main_cat' => 't', 'is_link' => 'f'));

		// Success
		echo 'Added #' . $snippet_name . '#' . PHP_EOL;

	} else {
		// Error
		throw new Exception ('#' . $autoresponder_name . '# autoresponder already exists');
	}
}
