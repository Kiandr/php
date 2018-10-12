<?php

//	LANGUAGE FILE FOR REW BACKEND
//	NOTE TO WRITERS: ESCAPE ALL SINGLE QUOTES WITHIN STRINGS LIKE SO: \'
//	EXAMPLE 'This string\'s good. It won\'t break anything.';
//	-SOME- GLOBAL VARIABLES ARE ALLOWED, BUT MUST FOLLOW A SPECIFIC FORMAT;
//	E.G. 'There are {$gbl[STATS][CMS_PG_COUNT]} pages.'
function tpl_lang($key)
{

    // CMS PAGE TITLES
    $cfg['lang']['TTL_PAGE_EDIT_HOME']              = __('Edit Homepage');
    $cfg['lang']['TTL_PAGE_MANAGE_PAGES']           = __('Manage Pages');
    $cfg['lang']['TTL_PAGE_ADD_PAGE']               = __('Add Page');
    $cfg['lang']['TTL_PAGE_EDIT_PAGE']              = __('Edit Page');
    $cfg['lang']['TTL_PAGE_MANAGE_LISTINGS']        = __('Manage Listings');
    $cfg['lang']['TTL_PAGE_ADD_LISTING']            = __('Add Listing');
    $cfg['lang']['TTL_PAGE_EDIT_LISTING']           = __('Edit Listing');
    $cfg['lang']['TTL_PAGE_ADD_SNIPPET']            = __('Add Snippet');
    $cfg['lang']['TTL_PAGE_EDIT_SNIPPET']           = __('Edit Snippet');
    $cfg['lang']['TTL_PAGE_MANAGE_IDX_SNIPPETS']    = __('Manage IDX Snippets');
    $cfg['lang']['TTL_PAGE_TOOLS']                  = __('Tools');
    $cfg['lang']['TTL_PAGE_TOOLS_BACKUP']           = __('Backup');
    $cfg['lang']['TTL_PAGE_TOOLS_TRACKING']         = __('Tracking Codes');
    $cfg['lang']['TTL_PAGE_TOOLS_REDIRECTS']        = __('Manage Redirect Rules');
    $cfg['lang']['TTL_PAGE_TOOLS_TESTIMONIALS']     = __('Manage Testimonials');

    // CRM/LEADMANAGER PAGES TITLES
    $cfg['lang']['TTL_PAGE_CRM_SUMMARY']            = __('Leads Summary');
    $cfg['lang']['TTL_PAGE_CRM_NEW_LEADS']          = __('New Leads');
    $cfg['lang']['TTL_PAGE_CRM_RTN_LEADS']          = __('Returning Leads');
    $cfg['lang']['TTL_PAGE_CRM_SEARCH_LEADS']       = __('Search Leads');
    $cfg['lang']['TTL_PAGE_CRM_EMAIL_LEADS']        = __('Email Leads');
    $cfg['lang']['TTL_PAGE_CRM_ADD_LEAD']           = __('Add A Lead');
    $cfg['lang']['TTL_PAGE_CRM_MANAGE_AGENTS']      = __('Manage Agents');
    $cfg['lang']['TTL_PAGE_CRM_ADD_AGENT']          = __('Add An Agent');
    $cfg['lang']['TTL_PAGE_CRM_MANAGE_GROUPS']      = __('Manage Groups');
    $cfg['lang']['TTL_PAGE_CRM_ADD_GROUP']          = __('Add a Group');
    $cfg['lang']['TTL_PAGE_CRM_SYSTEM_SUMMARY']     = __('System Summary');

    // CMS PAGE DESCRIPTIONS
    $cfg['lang']['DESC_PAGE_EDIT_HOME']             = __('Update your website\'s homepage using the form below.');
    $cfg['lang']['DESC_PAGE_ADD_PAGE']              = __('Fill out the form below to add a new CMS page.');
    $cfg['lang']['DESC_PAGE_EDIT_PAGE']             = __('Updated this page\'s information using the form below.');
    $cfg['lang']['DESC_PAGE_ADD_LISTING']           = __('Add your own listing to your website.');
    $cfg['lang']['DESC_PAGE_EDIT_LISTING']          = __('Update your listing information using the form fields below.');
    $cfg['lang']['DESC_PAGE_ADD_SNIPPET']           = __('Add Snippet');
    $cfg['lang']['DESC_PAGE_EDIT_SNIPPET']          = __('Edit Snippet');

    // CRM PAGE DESCRIPTIONS
    $cfg['lang']['DESC_PAGE_CRM_SUMMARY']           = __('The Leads Summary is a quick overview of all your leads within your specified timerange.');
    $cfg['lang']['DESC_PAGE_CRM_NEW_LEADS']         = __('New Leads');
    $cfg['lang']['DESC_PAGE_CRM_RTN_LEADS']         = __('Returning Leads');
    $cfg['lang']['DESC_PAGE_CRM_SEARCH_LEADS']      = __('Search Leads');
    $cfg['lang']['DESC_PAGE_CRM_EMAIL_LEADS']       = __('Send an email to your leads.');
    $cfg['lang']['DESC_PAGE_CRM_ADD_LEAD']          = __('Add A Lead');
    $cfg['lang']['DESC_PAGE_CRM_MANAGE_AGENTS']     = __('Use the tools below to manage your agents.');
    $cfg['lang']['DESC_PAGE_CRM_ADD_AGENT']         = __('Add An Agent');
    $cfg['lang']['DESC_PAGE_CRM_MANAGE_GROUPS']     = __('Manage Groups');
    $cfg['lang']['DESC_PAGE_CRM_ADD_GROUP']         = __('Add a Group');
    $cfg['lang']['DESC_PAGE_CRM_SYSTEM_SUMMARY']    = __('The System Summary is a quick overview of all the leads within your specified timerange.');

    // TOOLS PAGE DESCRIPTIONS
    $cfg['lang']['DESC_PAGE_TOOLS_REWRITES']        = __('Use the form below to add a new redirect rule.');
    $cfg['lang']['DESC_PAGE_TOOLS_TESTIMONIALS']    = __('Manage your client testimonials using the tools below.');

    // CMS FORM LABELS
    $cfg['lang']['LBL_FORM_CMS_PAGE_TITLE']         = __('Page Title');
    $cfg['lang']['LBL_FORM_CMS_PAGE_DESCRIPTION']   = __('Description');
    $cfg['lang']['LBL_FORM_CMS_PAGE_CONTENT']       = __('Page Content');
    $cfg['lang']['LBL_FORM_CMS_PAGE_FOOTER']        = __('Page Footer');
    $cfg['lang']['LBL_FORM_CMS_PAGE_PARENT']        = __('Parent');
    $cfg['lang']['LBL_FORM_CMS_PAGE_ALIAS']         = __('Filename/Alias');
    $cfg['lang']['LBL_FORM_CMS_LINK_LABEL']         = __('Link Name');
    $cfg['lang']['LBL_FORM_CMS_LINK_TARGET']        = __('Target');
    $cfg['lang']['LBL_FORM_CMS_SNIP_NAME']          = __('Snippet Name');
    $cfg['lang']['LBL_FORM_CMS_SNIP_CODE']          = __('Snippet Markup');
    $cfg['lang']['LBL_FORM_LOGIN_USERNAME']         = __('Username');
    $cfg['lang']['LBL_FORM_LOGIN_PASSWORD']         = __('Password');
    $cfg['lang']['LBL_FORM_LOGIN_REMEMBER_ME']      = __('Stay signed in');
    $cfg['lang']['LBL_FORM_LISTING_TITLE']          = __('Listing Title ');
    $cfg['lang']['LBL_FORM_LISTING_PRICE']          = __('Price');
    $cfg['lang']['LBL_FORM_LISTING_TYPE']           = __('Type');
    $cfg['lang']['LBL_FORM_LISTING_NUMBER']         = __('MLS Number');
    $cfg['lang']['LBL_FORM_LISTING_BUILT']          = __('Year Built');
    $cfg['lang']['LBL_FORM_LISTING_DESCRIPTION']    = __('Description');
    $cfg['lang']['LBL_FORM_LISTING_STATUS']         = __('Status');
    $cfg['lang']['LBL_FORM_LISTING_BEDROOMS']       = __('Bedrooms');
    $cfg['lang']['LBL_FORM_LISTING_BATHROOMS']      = __('Bathrooms');
    $cfg['lang']['LBL_FORM_LISTING_HALFBATHS']      = __('Half Bathrooms');
    $cfg['lang']['LBL_FORM_LISTING_STORIES']        = __('Stories');
    $cfg['lang']['LBL_FORM_LISTING_SQ_FEET']        = __('Square Feet');
    $cfg['lang']['LBL_FORM_LISTING_LOT_SIZE']       = __('Lot Size');
    $cfg['lang']['LBL_FORM_LISTING_STREET']         = __('Street Address');
    $cfg['lang']['LBL_FORM_LISTING_STATE']          = __('State/Province');
    $cfg['lang']['LBL_FORM_LISTING_CITY']           = __('City/Town');
    $cfg['lang']['LBL_FORM_LISTING_ZIP']            = __('Zip/Postal');
    $cfg['lang']['LBL_FORM_LISTING_SUBDIVISION']    = __('Subdivision');
    $cfg['lang']['LBL_FORM_LISTING_ELEM_SCHOOL']    = __('Elementary School');
    $cfg['lang']['LBL_FORM_LISTING_MID_SCHOOL']     = __('Middle School');
    $cfg['lang']['LBL_FORM_LISTING_HIGH_SCHOOL']    = __('High School');
    $cfg['lang']['LBL_FORM_LISTING_LATITUDE']       = __('Latitude');
    $cfg['lang']['LBL_FORM_LISTING_LONGITUDE']      = __('Longitude');
    $cfg['lang']['LBL_FORM_LISTING_DIRECTIONS']     = __('Driving Directions');
    $cfg['lang']['LBL_FORM_LISTING_VIRTUAL_TOUR']   = __('Virtual Tour URL');
    $cfg['lang']['LBL_FORM_HIDE_SLIDESHOW']         = __('Hide Content Feature');

    // CRM FORM DESCRIPTIONS
    $cfg['lang']['DESC_FORM_AUTO_ASSIGN_AGENT']     = __('Toggle this setting to opt-out of "Lead Auto-Assignment" and "Lead Auto-Rotation".');
    $cfg['lang']['DESC_FORM_AUTO_ASSIGN_ADMIN']     = __('This allows you to control whether leads can be automatically assigned to this  agent in the "Lead Auto-Assignment" process. See %s for more information.', '<a href="{URL_BACKEND}settings/">' . __('Backend Settings') . '</a>');
    $cfg['lang']['DESC_FORM_AUTO_ROTATE']           = __('This allows you to control whether this Agent\'s pending leads will be automatically rotated in the "Lead Auto-Rotation" process. See %s for more information.', '<a href="{URL_BACKEND}settings/">' . __('Backend Settings') . '</a>');
    $cfg['lang']['DESC_FORM_AUTO_OPTOUT']           = __('This allows you to control whether this Agent should be included in "Automated Agent Opt-Out" process. See %s for more information.', '<a href="{URL_BACKEND}settings/">' . __('Backend Settings') . '</a>');
    $cfg['lang']['DESC_FORM_AUTO_SEARCH']           = __('This allows you to control whether this Agent\'s leads can have a saved search created automatically (based on the lead\'s listing views)');

    // CMS FORM DESCRIPTIONS
    $cfg['lang']['DESC_FORM_CMS_PAGE_TITLE']        = __('This field is for the page title as it will appear in the title bar at the top of the browser. Page titles are an essential element if you want your pages to be noticed by search engines.');
    $cfg['lang']['DESC_FORM_CMS_PAGE_DESCRIPTION']  = __('This field is for the meta description as it will be displayed in your site\'s header and in the search engine results pages.');
    $cfg['lang']['DESC_FORM_CMS_PAGE_CONTENT']      = __('Body HTML is the main HTML of the page - the page\'s content.');
    $cfg['lang']['DESC_FORM_CMS_PAGE_FOOTER']       = __('This field is for the text displayed at the bottom of this page - leave it blank if you wish to have the default footer displayed.');
    $cfg['lang']['DESC_FORM_CMS_PAGE_PARENT']       = __('Select if this is a main page or a sub-page of another.');
    $cfg['lang']['DESC_FORM_CMS_PAGE_ALIAS']        = __('This field is for the file name as it will appear in the address bar. Replace any spaces with hyphens. Please supply a page name with no spaces and no filename extension. If you want it to display "example-page.php" as the filename enter "example-page" into this field(without the quotes).');
    $cfg['lang']['DESC_FORM_CMS_LINK_PARENT']       = __('Select if this is a main link or a sub-link of a page.');
    $cfg['lang']['DESC_FORM_CMS_LINK_LABEL']        = __('The anchor text is a word or combination of words that are used to link to this HTML document. Ideally, this should be a very brief &amp; accurate description of the page. E.G. "About our Company"');
    $cfg['lang']['DESC_FORM_CMS_LINK_TARGET']       = __('This option allows you to have the link open in a new window, or in the current page.');
    $cfg['lang']['DESC_FORM_CMS_SNIP_NAME']         = __('The snippet name is a keyword that is used to include the snippet within your pages.');
    $cfg['lang']['DESC_FORM_CMS_SNIP_CODE']         = __('This is the HTML markup that will be included when using your snippet.');
    $cfg['lang']['DESC_FORM_LISTING_TITLE']         = __('E.G. "Beautiful Oceanview Condo", "Perfect Starter Home"');
    $cfg['lang']['DESC_FORM_LISTING_VIRTUAL_TOUR']  = __('The full URL of the page hosting this listings virtual tour.');

    // BLOG FORM DESCRIPTIONS
    $cfg['lang']['DESC_FORM_BLOG_TITLE']            = __('This field is for the page title as it will appear in the title bar at the top of the browser. Page titles are an essential element if you want your pages to be noticed by search engines.');
    $cfg['lang']['DESC_FORM_BLOG_DESCRIPTION']      = __('This field is for the meta description as it will be displayed in your site\'s header and in the search engine results pages.');
    $cfg['lang']['DESC_FORM_BLOG_RELATED_LINKS']    = __('Add links to relevant websites and provide resources related to the topic of your blog entry.');
    $cfg['lang']['DESC_FORM_BLOG_TAGS']             = __('List of topics related to the content of this blog entry. %s', '<em>' . __('Example: real estate, sellers, fsbo') .'</em>');

    // DIRECTORY FORM DESCRIPTIONS
    $cfg['lang']['DESC_FORM_DIRECTORY_TITLE']       = __('This field is for the page title as it will appear in the title bar at the top of the browser. Page titles are an essential element if you want your pages to be noticed by search engines.');
    $cfg['lang']['DESC_FORM_DIRECTORY_DESCRIPTION'] = __('This field is for the meta description as it will be displayed in your site\'s header and in the search engine results pages.');

    // AGENT DESCRIPTIONS
    $cfg['lang']['DESC_FORM_GOOGLE_CALENDAR']       = __('Once enabled, there will be a prompt requesting you to log in to your Google account and allow calendar access. This prompt will appear the first time you visit the calendar, lead summary, or lead reminder page.');
    $cfg['lang']['DESC_FORM_OUTLOOK_CALENDAR']      = __('Once enabled, there will be a prompt requesting you to log in to your Outlook account and allow calendar access. This prompt will appear the first time you visit the calendar, lead summary, or lead reminder page.');

    // OPEN GRAPH DATA
    $cfg['lang']['LBL_FORM_OG_IMAGE']               = __('Preview Image');
    $cfg['lang']['DESC_FORM_OG_IMAGE']              = '<a href="https://developers.facebook.com/docs/sharing/best-practices#images" target="_blank">' . __('Optimize your image sizes to generate great previews: ') . '</a>' . __('Use images that are at least 1200 x 630 pixels for the best display on high resolution devices. At the minimum, you should use images that are 600 x 315 pixels to display link page posts with larger images. If your image is smaller than 600 x 315 px, it will still display in the link page post, but the size will be much smaller. The minimum image size is 200 x 200 pixels.');

    // Use Variable Replacement
    $string = $cfg['lang'][$key];
    if (preg_match("/{([A-Za-z0-9\_\$\[\]]+)}/", $string, $matches)) {
        global $gbl;
        $string = str_replace("'", "\'", $string);
        $string = str_replace("{", "' . ", $string);
        $string = str_replace("}", " . '", $string);
        return eval("echo '" . $string . "';");
    // Normal String
    } else {
        return $string;
    }
}
