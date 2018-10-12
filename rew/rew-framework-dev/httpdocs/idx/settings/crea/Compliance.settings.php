<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer_footer']   = array('');

// Disclaimer
$_COMPLIANCE['disclaimer_footer'][] = '<p class="disclaimer">';
$_COMPLIANCE['disclaimer_footer'][] = Lang::write('MLS') . ', REALTOR&reg;, and the associated logos are trademarks of The Canadian Real Estate Association. ';
$_COMPLIANCE['disclaimer_footer'][] = '</p>';

if (in_array($_GET['module'], array('feature'))) {
	// Quick Search, show disclaimer
	$_COMPLIANCE['results']['show_immediately_below_listings'] = true;
}

// Image rules
$_COMPLIANCE['images']['no_overlay'] = true;

// Logo rules
$url_img = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/';
$_COMPLIANCE['details']['logos'] = array(
    $url_img . 'crea_realtor.png',
    $url_img . 'crea_mls.png'
);

// Add same logos to brochure
$_COMPLIANCE['brochure']['logos'] = $_COMPLIANCE['details']['logos'];

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('details', 'brochure', 'birdseye', 'streetview')) ? true : false);

// Compliance Terms of Use is Required Before Viewing Website
if(in_array($_GET['load_page'], array('details', 'brochure', 'birdseye', 'streetview', 'tos'))){
    $_COMPLIANCE['terms_required'] = true;
}

// TOU Compliance
$_COMPLIANCE['pages']['tos'] = array();
$_COMPLIANCE['pages']['tos']['page_title']     = 'Terms &amp; Conditions Agreement';

ob_start();

?>

<style>
	#wtou_all p, #wtou_all li {
		margin: 10px;
	}
	.wtou_bold li {
		font-weight: bold;
	}
	.wtou_first_indent li {
		list-style-type: decimal;
	}
	.wtou_second_indent li {
		list-style-type: none;
	}
	.wtou_third_indent li {
		list-style-type: none;
	}
	.wtou_no_prefix li {
		list-style-type: none;
	}
	#wtou_h1 {
		text-decoration: underline;
		text-align: center;
	}
	.wtou_underline {
		text-decoration: underline;
	}
</style>

<div id="wtou_all">
<h1 id="wtou_h1">Website Terms of Use</h1>
<p class="wtou_bold">The listing content on this website is protected by copyright and other laws, and is intended solely for the private, non-commercial use by individuals. Any other reproduction, distribution or use of the content, in whole or in part, is specifically forbidden. The prohibited uses include commercial use, "screen scraping", "database scraping", and any other activity intended to collect, store, reorganize or manipulate data on the pages produced by or displayed on this website.</p>
</div>
<?php

$_COMPLIANCE['pages']['tos']['category_html'] = ob_get_clean();