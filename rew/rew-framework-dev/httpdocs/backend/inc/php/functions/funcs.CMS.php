<?php

/**
 * Get list of snippets to exclude from display (require installed add-ons)
 *
 * @return array
 */
function getExcludedSnippets()
{

    // Excluded Snippets
    $exclude = array();

    // Blog RSS Reader
    if (empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'])) {
        $exclude[] = 'rss-reader';
    }

    // Agent Roster
    if (empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER'])) {
        $exclude[] = 'agents';
    }

    // Featured Offices
    if (empty(Settings::getInstance()->MODULES['REW_FEATURED_OFFICES'])) {
        $exclude[] = 'offices';
    }

    // Testimonials Snippet
    if (empty(Settings::getInstance()->MODULES['REW_TESTIMONIALS'])) {
        $exclude[] = 'testimonials';
    }

    // Featured Listings Snippets
    if (empty(Settings::getInstance()->MODULES['REW_FEATURED_LISTINGS'])) {
        $exclude[] = 'idx-featured-listings';
        $exclude[] = 'idx-featured-search';
    }

    // Directory Snippets
    if (empty(Settings::getInstance()->MODULES['REW_DIRECTORY'])) {
        $exclude[] = 'business-directory-add-listing-formatting';
        $exclude[] = 'business-directory-add-listing-page';
        $exclude[] = 'business-directory-intro';
    }

    // Developments Snippets
    if (empty(Settings::getInstance()->MODULES['REW_DEVELOPMENTS'])) {
        $exclude[] = 'developments';
    }

    // Radio Landing Page Snippets
    $exclude[] = 'form-seller-radio-boxed';
    $exclude[] = 'form-seller-radio-simple';
    if (empty(Settings::getInstance()->MODULES['REW_RADIO_LANDING_PAGE'])) {
        $exclude[] = 'radio-landing-page';
    }

    // Return Array
    return $exclude;
}

/**
 * Check Table to Make sure Link is Unique
 *
 * @param string $link
 * @param string $table
 * @param string $compare - table column that needs to be unique
 * @return string - unique key
 */
function uniqueLink($link, $table, $key = 'link')
{
    $check_link = null;
    $num = 0;
    do {
        if (!empty($check_link)) {
            $num++;
        }
        $unique = $link . (!empty($num) ? $num : '');
        // Check for Existing Row
        $query = "SELECT * FROM " . $table . " WHERE `" . $key . "` = '" . mysql_real_escape_string($unique) . "';";
        $check_link = mysql_query($query);
        $check_link = mysql_fetch_array($check_link);
        if (empty($check_link)) {
            $link = $unique;
        }
    } while (!empty($check_link));
    return $link;
}

/**
 * Create Agent CMS Site
 * @param array $agent
 * @param array $errors
 */
function agent_site($agent, array &$errors = array())
{

    if ($agent['cms'] == 'true') {
        subdomain_site($agent['id'], null, $errors);
    }
}

/**
 * Create Team CMS Site
 * @param array $team
 * @param array $errors
 */
function team_site($team, array &$errors = array())
{

    if ($team['subdomain'] == 'true') {
        subdomain_site(null, $team['id'], $errors);
    }
}

/**
 * Create Subdomain Site
 * @param int $agent_id
 * @param int $team_id
 * @param array $errors
 */
function subdomain_site($agent_id = null, $team_id = null, array &$errors)
{

    // DB connection
    $db = DB::get();

    // Insert `default_info` Row
    $insert_info = $db->prepare("INSERT INTO `default_info` SET "
        . "`agent`             = :agent, "
        . "`team`              = :team, "
        . "`uacct`             = '', "
        . "`verifyv1`          = '', "
        . "`msvalidate`        = '', "
        . "`hittail`           = '', "
        . "`page_title`        = 'Your Real Estate Website', "
        . "`meta_tag_desc`     = '', "
        . "`footer`            = '<a href=\"/sitemap.php\">Sitemap</a><a href=\"/privacy-policy.php\">Privacy Policy</a>', "
        . "`category_html`     = '<h1>Welcome to <em>Your </em>Real Estate Website</h1> <p>This is the most useful real estate website in the area, with advanced search technologies that allow you to view listings in the MLS, or to ensure your own home is sold in reasonable time and for top dollar.</p> <p>You''ll also find our web site to be a one-stop resource for your online real estate research. We''re eager to tell you what we know about the local market for condos or homes, or preconstruction, or whatever is most interesting at the moment - that''s where you''ll usually find us! We delight in helping people succeed in this market, and when our clients come out of the transaction breathing that big sigh of relief, we''re just as satisfied as they are. </p> <p>So please, have a look around, and <a href=\"/contact.php\">contact us</a> with any questions you might have about the market or our services. No question is too mundane - we want to help! </p>', "
        . "`feature_image`     = '', "
        . "`timestamp_created` = NOW(), "
        . "`timestamp_updated` = NOW()"
        . " ON DUPLICATE KEY UPDATE `timestamp_updated` = NOW();");

    // Execute Query
    if (!$insert_info->execute(['agent' => $agent_id, 'team' => $team_id])) {
        $errors[] = 'Error Updating Subdomain CMS Defaults';
    }

    // Insert `numlinks` Row
    $insert_numlink = $db->prepare("INSERT INTO `numlinks` SET "
    . "`agent`             = :agent, "
    . "`team`              = :team, "
    . "`num_links`         = 8,"
    . "`timestamp_created` = NOW(),"
    . "`timestamp_updated` = NOW()"
    . " ON DUPLICATE KEY UPDATE `timestamp_updated` = NOW();");

    // Execute Query
    if (!$insert_numlink->execute(['agent' => $agent_id, 'team' => $team_id])) {
        $errors[] = 'Error Updating Subdomain CMS Navigation';
    }

    $skin = Skin::load();

    // Check if Agent has Pages
    $query_pages = $db->prepare("SELECT COUNT(`page_id`) AS `total` FROM `pages` WHERE `agent` <=> :agent AND `team` <=> :team;");
    $query_pages->execute(['agent' => $agent_id, 'team' => $team_id]);
    if ($pages = $query_pages->fetch()) {
        $insertPages = true;
        try {
            if (empty($pages['total'])) {
                if (isset($agent_id)) {
                    $insertPages = $skin->insertAgentSitePages($agent_id);
                } else if (isset($team_id)) {
                    $insertPages = $skin->insertTeamSitePages($team_id);
                }
            }
        } catch (\Exception $e) {
            $errors[] = sprintf('Error occurred: %s', $e->getMessage());
        }

        // Check Count. Only insert pages if there were no errors and the skin method told us to.
        if (!$errors && $insertPages && empty($pages['total'])) {
            // Insert `pages` Rows
            $insert_pages = $db->prepare("INSERT IGNORE INTO `pages` (`page_id`, `agent`, `team`, `page_title`, `link_name`, `file_name`, `meta_tag_desc`, `footer`, `category_html`, `hide`, `hide_sitemap`, `subcategory_order`, `category`, `category_order`, `is_main_cat`, `is_link`) VALUES "
            . "(NULL, :agent, :team, '', 'Home', '/', '', '_self', '', 'f', 'f', 0, '/', 1, 't', 't'), "
            . "(NULL, :agent, :team, 'Buying', 'Buying', 'buying', '', '', '<h1>Information for Buyers </h1> <p>We''re glad that more people are becoming curious about this market! It''s an exciting and rewarding situation, with interesting old homes in good condition, new luxury developments, plucky condos downtown, and well-planned family residences with lasting financial and utilitarian appeal.</p> <h2>Searching for a home</h2> <p>Despite (and perhaps because of) the great selections in homes in this area, buyers are having a difficult time zeroing in on the home or property that is uniquely appropriate for <em>their</em> needs. </p> <blockquote><a href=\"/search.php\">Search for homes</a></blockquote> <p>Just remember: Browsing the listings for months is one way search out the deals, but weighing the investment potential of each property can be difficult, and many homes for sale are never listed publicly. </p> <h2>The Home Buying Process </h2> <p>As you begin to think about buying a home, you''re probably acutely aware that this is likely to be your greatest investment ever (short of children or a good marriage), and you might be wondering if there''s anything you <em>really</em> need to know as you go into this process. This is where your real estate professionals come in. With the help of a trained agent, you can come out on top of a closing that might otherwise have pushed your retirement ahead a decade! This happens all the time to buyers, who are left reflecting on the relatively small commission that would have brought a Realtor into the bargain to help them.</p> <p>If you''d like to know about the home buying process, we''d love to tell you all about it. <a href=\"/contact.php\">Contact us</a>!</p> <h2>Five Reasons to buy your home with a REALTOR&reg;</h2> <ol> <li>Get access to all the listings.</li> <li>Know all the facts on properties you''re interested in.</li> <li>Negotiate with the help of a real estate professional.</li> <li>Finance your home faster.</li> <li>Make a solid investment that pays off down the road.</li> </ol> <p>New homes for sale hit the market daily. As your representative, we''ll be in regular contact with sellers and other real estate agents to ensure you see all the newest listings right away, whether or not they''re made public. Often, our clients can put in offers with little or no competition and walk away with an impressive deal on the property of their choice. Your agent will also make sure you see only the properties that meet your needs and investment preferences, so that you don''t spend time on listings that aren''t of interest. As you compare the best deals, we''ll provide you with important information about the home, its neighborhood, zoning issues, utilities, and nearby plans for development.</p> <p>Many real estate transactions include an extensive negotiating process, and as your buyer''s Agency it will be Our job to represent you here and get the best deal possible. We''ll also attend property inspections, make sure any and all agreed-on repair work is carried out, and handle paperwork related to the sale. We can also help set up the financing for your invesment, and work directly with a broker to provide you with a seamless service package.</p> <p>Working with a dedicated agent on your next home purchase will save you time, and add professional assurance to every step of the process. With the guarantee of a solid investment, the decision could also make you more wealthy when it''s time to sell. To get in touch with us today, please <a href=\"/contact.php\">contact us whenever you''d like</a>. </p> ', 'f', 'f', 0, 'buying', 2, 't', 'f'), "
            . "(NULL, :agent, :team, 'Selling', 'Selling', 'selling', '', '', '<h1>Information for Sellers </h1> <p>Getting the most for your home or property means doing more than putting a &quot;For Sale&quot; sign out front. In a competitive market where new homes for sale are added daily, your listing needs to be seen in a variety of places in order to stand out. And sometimes the right buyer is waiting out of state, or even in another country. To ensure sure your home gets noticed and sells without a hitch for full market value, consider working with a top professional. It could mean the difference between no sale, and the payoff you''ve been waiting for.</p> <h2>Five Reasons to Sell your home with a REALTOR&reg;</h2> <ol> <li>Sell with a custom marketing plan.</li> <li>Get your home listed and talked about everywhere, not just in the local paper and on your street.</li> <li>Attract buyers via the Internet.</li> <li>Have all the paperwork taken care of for you.</li> <li>Negotiate with the help of a real estate professional.</li> </ol> <p>As your Seller''s Agency, we''ll quickly put together a custom marketing plan with an effective price. A well-priced home will often generate competing offers and drive up the final sale value. Our market analysis takes into account the most actively searched prices, and home values throughout your area, including expired listings, and properties still on the market. In marketing your home, we also develop a listing that emphasizes its unique and sellable aspects. We then put your home in front of thousands of buyers, establishing it on the local MLS as well as broader ones, new listings sheets, and Realtor publications. Our network of professional real estate contacts and buyers throughout the nation will also have the opportunity to check out your listing. In addition, we''ll use the Internet, and this professionally optimized website, to make your listing highly visible. With more than 80 per cent of buyers checking the web first when looking for a home, that''s a part of your marketing strategy you can''t afford to miss.</p> <p>Without the help of a Realtor, marketing your home can be time consuming and  difficult. Advertising in the media is often expensive, and many buyers are  reluctant to consider homes listed &quot;For Sale by Owner.&quot;</p> <p>When we list your home, we do so at no additional cost. When you start to get offers, we can represent you during the emotionally charged negotiating process and ensure you get the best price, with favorable closing terms that are clearly spelled out. As your professional aides, we also and take care of all paperwork related to the sale.</p> <p>For most of us, our home is our biggest investment. When it''s time to sell, get the value you deserve with the help of a professional Realtor. If you''d like to get in touch about your next home sale today, please <a href=\"/contact.php\">contact us whenever you''d like</a>.</p> ', 'f', 'f', 0, 'selling', 3, 't', 'f'), "
            . "(NULL, :agent, :team, '', 'Search Homes', '/idx/', '', '_self', '', 'f', 'f', 0, '/idx/', 4, 't', 't'), "
            . "(NULL, :agent, :team, 'About', 'About', 'about', '', '', '<h1>Your Local Real Estate Agent</h1> <p>Some people are very casual when it comes to signing papers that affect their finances for decades. But for those who recognize that small steps early in the game can affect major outcomes later, it''s fortunate that there are professionals who dedicate themselves to learning about the process and local market conditions, and who can turn a nightmare of stress and confusion into a relatively painless experience (let''s be honest). </p> <p>I''m an educated professional with a few years of experience in this market, and I think I''ve seen almost every trick and turn of the industry already. I''ve also had intimate experience with properties in most of these neighborhoods, making me an invaluable person to talk to when it comes to relocation to or within this area. I''m confident that I can save you not only tens of thousands of dollars, but a great deal of time and emotional energy as well. </p> <p>But I''m not simply a piece of hardware you can point at a house. I''m a human who laughs and smiles, and I''ll take the time to find out <em>what you really need most from me </em>as a professional real estate agent. If you would like to know anything about my services, or if you''d like to hear about the local market from a human voice, please <a href=\"/contact.php\">contact me</a> at your convenience. </p> <h3>Some words that might be used to describe me:</h3> <br /> <table width=\"98%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"> <tr> <td width=\"30%\"><ul> <li>Friendly</li> <li>Professional</li> <li>Enthusiastic</li> </ul></td> <td width=\"70%\"><ul> <li>Humble</li> <li>Thorough</li> <li>Attentive</li> </ul></td> </tr> </table><p>&nbsp;</p>', 'f', 'f', 0, 'about', 5, 't', 'f'), "
            . "(NULL, :agent, :team, 'Contact', 'Contact', 'contact', '', '', '#form-contact#', 'f', 'f', 0, 'contact', 6, 't', 'f'), "
            . "(NULL, :agent, :team, 'Form Snippets', 'Form Snippets', 'forms', '', '', '<h2>#<em>form-approve</em>#</h2>\r\n#form-approve#\r\n\r\n<h2>#<em>form-buyers</em>#</h2>\r\n#form-buyers#\r\n\r\n<h2>#<em>form-contact</em>#</h2>\r\n#form-contact#\r\n\r\n<h2>#<em>form-seller</em>#</h2>\r\n#form-seller#\r\n\r\n<h2>#<em>mortgage-calculator</em>#</h2>\r\n#mortgage-calculator#', 'f', 't', 0, 'forms', 7, 't', 'f'), "
            . "(NULL, :agent, :team, 'Content Formatting Test', 'Content Formatting Test', 'test', '', '', '<h1>Content Formatting Test</h1>\r\n<p>Lorem ipsum <strong>dolor sit amet</strong>, consectetuer adipiscing elit. Phasellus lorem. Aliquam erat volutpat. Aliquam <a href=\"javascript:void(0);\">sit amet massa</a>. Phasellus non risus ut felis tincidunt vehicula. Proin mattis accumsan dolor. Sed<em> tortor tortor, varius eu</em>, hendrerit in, vulputate eget, mauris. Mauris elit.</p>\r\n<h1>Heading 1</h1>\r\n<h2>Heading 2</h2>\r\n<h3>Heading 3</h3>\r\n<ul>\r\n<li>List Item 1</li>\r\n<li>List Item 2</li>\r\n<li>List Item 3</li>\r\n</ul><ol>\r\n<li>List Item 1</li>\r\n<li>List Item 2</li>\r\n<li>List Item 3</li>\r\n</ol>\r\n\r\n<blockquote><p><strong>Blockquote:</strong> Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus lorem. Aliquam erat volutpat. Aliquam sit amet massa. Phasellus non risus ut felis tincidunt vehicula. Proin mattis accumsan dolor.<br /><span class=\"byline\">~ Unknown</span></p></blockquote>\r\n\r\n<p class=\"highlight\">Paragraph with <strong>class=\"highlight\"</strong>. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus lorem. Aliquam erat volutpat. Aliquam sit amet massa. Phasellus non risus ut felis tincidunt vehicula. Proin mattis accumsan dolor.</p>\r\n\r\n<div class=\"highlight\"><p>You can also use a DIV with <strong>class=\"highlight\"</strong> and put multiple paragraphs in it. (Or images, or whatever...)</p> \r\n<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus lorem. Aliquam erat volutpat. Aliquam sit amet massa. Phasellus non risus ut felis tincidunt vehicula. Proin mattis accumsan dolor.</p>\r\n</div>\r\n\r\n<p class=\"important\">Paragraph with <strong>class=\"important\"</strong>. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus lorem. Aliquam erat volutpat. Aliquam sit amet massa. Phasellus non risus ut felis tincidunt vehicula. Proin mattis accumsan dolor.</p>\r\n\r\n<div class=\"important\"><p>You can also use a DIV with <strong>class=\"important\"</strong> and put multiple paragraphs in it. (Or images, or whatever...)</p> \r\n<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus lorem. Aliquam erat volutpat. Aliquam sit amet massa. Phasellus non risus ut felis tincidunt vehicula. Proin mattis accumsan dolor.</p>\r\n</div>\r\n\r\n<div class=\"footnote\"><strong>Footnote:</strong> Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus lorem.</div>\r\n\r\n<p>Some <a href=\"/\">sample link text</a>, within non-link text.</p>', 'f', 't', 0, 'test', 8, 't', 'f'), "
            . "(NULL, :agent, :team, 'Our Listings', 'Our Listings', 'listings', '', '', '<h1>Our Listings</h1>\r\n<p>Our office is pleased to highlight the following properties. If you''re a home buyer, simply click View Details to learn more about each property. If you''re selling your home with us, we''ll feature your property here too, so that it receives optimal exposure on our site.</p>\r\n<p class=\"highlight\">If you have any questions about the properties listed on this page, please feel free to <a href=\"/contact.php\">contact us</a>.</p>\r\n<p>#cms-listings#</p><p>&nbsp;</p>\r\n<p class=\"highlight\">If you''d like to expand your property search, see all <a href=\"/idx/\">homes for sale</a> in the local market. </p>\r\n', 'f', 'f', 0, 'listings', 9, 't', 'f'),"
            . "(NULL, :agent, :team, 'Area One', 'Area One', 'area-one', '', '', '<h1>[Area] Real Estate </h1> <p>Welcome to a world of convenience, recreation, and affordable real estate. Buying a home here means you''re purchasing a lifetime investment that will serve as both a sanctuary for you and your family, and as a solid asset base.</p> <p>The purchase of a home is a huge undertaking and something to be taken very seriously, because it will affect your future in such profound ways. But you already know this, so you''ll be pleased to hear that a future in this community is just plain <em>more likely</em> to turn out well, owing to the providential qualities that are relatively universal to both properties and homes here. Ask any resident! </p> <p>This city is widely known for its commitment to residents and visitors alike - we''re proud of our past and we look forward to a bright future. This region affords a scenic backdrop for the homes and properties that comprise the local real estate market, as well as a huge variety of amenities. These amenities are designed to ease the lifestyle of residents while providing those quality services at reasonable rates.</p> <p>&nbsp;</p>', 'f', 'f', 0, 'area-one', 10, 't', 'f'), "
            . "(NULL, :agent, :team, 'Area 1 - Sub 1', 'Area 1 - Sub 1', 'area1-sub1', '', '', '<h1>Area 1 - Sub 1</h1><p>This page is for testing purposes only.</p>', 'f', 'f', 1, 'area-one', 10, 'f', 'f'),"
            . "(NULL, :agent, :team, 'Area 1 - Sub 2', 'Area 1 - Sub 2', 'area1-sub2', '', '', '<h1>Area 1 - Sub 2</h1><p>This page is for testing purposes only.</p>', 'f', 'f', 2, 'area-one', 10, 'f', 'f'),"
            . "(NULL, :agent, :team, 'Area Two', 'Area Two', 'area-two', '', '', '<h1>[Area] Real Estate </h1> <p>Welcome to a world of convenience, recreation, and affordable real estate. Buying a home here means you''re purchasing a lifetime investment that will serve as both a sanctuary for you and your family, and as a solid asset base.</p> <p>The purchase of a home is a huge undertaking and something to be taken very seriously, because it will affect your future in such profound ways. But you already know this, so you''ll be pleased to hear that a future in this community is just plain <em>more likely</em> to turn out well, owing to the providential qualities that are relatively universal to both properties and homes here. Ask any resident! </p> <p>This city is widely known for its commitment to residents and visitors alike - we''re proud of our past and we look forward to a bright future. This region affords a scenic backdrop for the homes and properties that comprise the local real estate market, as well as a huge variety of amenities. These amenities are designed to ease the lifestyle of residents while providing those quality services at reasonable rates.</p> <p>&nbsp;</p>', 'f', 'f', 0, 'area-two', 11, 't', 'f'),"
            . "(NULL, :agent, :team, 'Site Map', 'Site Map', 'sitemap', '', '', '#sitemap#', 't', 'f', 0, 'sitemap', 12, 't', 'f'),"
            . "(NULL, :agent, :team, '404: Page not found', '404: Page not found', '404', '', '', '<h1>Error 404: Page not found</h1> <p>The page you were looking for was not found on the web site. It is possible the page has moved. Please try our home page.', 't', 't', 0, '404', 99, 't', 'f'),"
            . "(NULL, :agent, :team, 'Unsubscribe', 'Unsubscribe', 'unsubscribe', '', '', '#unsubscribe#', 't', 't', 0, 'unsubscribe', 100, 't', 'f'),"
            . "(NULL, :agent, :team, 'IDX Help', 'IDX Help', 'help', '', '', '<h1>IDX How-To &amp; Help</h1>\r\n<h2>Bookmarked Listings</h2>\r\n<h3>Overview</h3>\r\n<p>Bookmarked listings are listings that you want to save and view later.</p>\r\n<h3>Bookmark a Listing</h3>\r\n<p>To Bookmark a Listing click the star icon near each listing result or the Bookmark Listing button found on each listing''s details page.</p>\r\n<hr />\r\n<h2>Saved Searches</h2>\r\n<p>If you find yourself searching for the same types of properties all the time, you needn''t fill out the search form each time--create a saved search for one click access to search results.</p>\r\n<h3>Save a Search</h3>\r\n<p>To Save a Search Click the ''<strong>Save this Search!</strong>'' link found at the top of any listings results page.</p>\r\n<h3>Daily Listing Updates</h3>\r\n<p>New listings matching your saved search criteria will be sent to you each day. This will keep you informed on the latest properties available.</p>\r\n', 't', 'f', 0, 'help', 101, 't', 'f')"
            . ";");

            // Execute Query
            if (!$insert_pages->execute(['agent' => $agent_id, 'team' => $team_id])) {
                $errors[] = 'Error Updating Subdomain CMS Pages';
            }
        }

    // Query Error
    } else {
        $errors[] = 'Error Checking Agent CMS Pages';
    }

    $excludeSnippets = [];
    // Specific snippets should be copied from the main site for certain skins
    if (in_array(Settings::getInstance()->SKIN, ['REW\Theme\Enterprise\Theme', 'fese'])) {
        // Snippets to copy
        $snippets = [
            'REW\Theme\Enterprise\Theme' => [
                'site-logo',
                'site-business-name',
                'site-address',
                'site-phone-number',
                'site-email'
            ],
            'fese' => [
                'site-logo-link',
                'site-phone-number',
                'site-contact-cta'
            ]
        ];
        foreach ($snippets[Settings::getInstance()->SKIN] as $snippet) {
            // Check if main site has the snippet and it is populated
            $query =
                "SELECT " .
                    "* " .
                "FROM " .
                    "`snippets` " .
                "WHERE " .
                    "`agent` = 1 " .
                "AND " .
                    "`name` = :name;";
            $query_snippet = $db->prepare($query);
            $query_snippet->execute(['name' => $snippet]);
            $result_snippet = $query_snippet->fetch();
            if (!empty($result_snippet['code'])) {
                // Insert snippet
                $query =
                    "INSERT IGNORE INTO " .
                        "`snippets` " .
                        "(`agent`, `team`, `name`, `code`, `type`) " .
                    "VALUES " .
                        "(:agent, :team, :name, :code, :type)";
                $insert_snippet = $db->prepare($query);
                if (!$insert_snippet->execute([
                    'agent' => $agent_id,
                    'team' => $team_id,
                    'name' => $result_snippet['name'],
                    'code' => $result_snippet['code'],
                    'type' => $result_snippet['type']
                ])) {
                    $errors[] = 'Error Inserting Snippet: ' . $result_snippet['name'];
                } else {
                    // Snippet was inserted
                    // Add it to the exclusion list so the below functions do not attempt to insert it
                    $excludeSnippets[] = $snippet;
                }
            } else {
                $errors[] = $snippet . ' snippet not found or no content';
            }
        }
    }

    $insertSnippets = true;
    try {
        if (isset($agent_id)) {
            $insertSnippets = $skin->insertAgentSiteSnippets($agent_id, $excludeSnippets);
        } else if (isset($team_id)) {
            $insertSnippets = $skin->insertTeamSiteSnippets($team_id, $excludeSnippets);
        }
    } catch (\Exception $e) {
        $errors[] = sprintf('Error occurred: %s', $e->getMessage());
    }

    // The skin told us to not insert snippets, or there was an error.
    if ($errors || !$insertSnippets) {
    // Vision
    } else if (Settings::getInstance()->SKIN == 'fese') {
        // Insert #site-footer-links# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
            . "(:agent, :team, 'site-footer-links', '<a href=\"/\">Home</a><a href=\"/about.php\">About</a><a href=\"/idx/\">Search</a><a href=\"/listings.php\">Listings</a><a href=\"/contact.php\">Contact</a>', 'cms');");
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #site-footer-links#';
        }
        // Insert #site-navigation# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
            . "(:agent, :team, 'site-navigation', '<ul><li class=\"current\"><a href=\"/\">Home</a></li><li><a href=\"/about.php\">About Us<span>Meet the Team</span></a></li><li><a href=\"/idx/\">Search Homes<span>Find What You\'re Looking For</span></a></li><li><li><a href=\"/contact.php\">Contact</a></li></ul>', 'cms');");
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #site-navigation#';
        }
    } else if (Settings::getInstance()->SKIN == 'REW\Theme\Enterprise\Theme') {
        // Insert #footer-column-2# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
            . "(:agent, :team, 'footer-column-2', '<div class=\"nav nav--stacked\">\n\t<h3 class=\"nav__heading -text-invert\">Vision Real Estate</h3>\n\t<ul class=\"nav__list\">\n\t\t<li class=\"nav__item\">\n\t\t\t<a class=\"nav__link -text-invert\" href=\"/about.php\">About</a>\n\t\t</li>\n\t\t<li class=\"nav__item\">\n\t\t\t<a class=\"nav__link -text-invert\" href=\"/listings.php\">Listings</a>\n\t\t</li>\n\t\t<li class=\"nav__item\">\n\t\t\t<a class=\"nav__link -text-invert\" href=\"/contact.php\">Contact</a>\n\t\t</li>\n\t</ul>\n</div>', 'cms');");
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #footer-column-2#';
        }
        // Insert #footer-column-3# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
            . "(:agent, :team, 'footer-column-3', '<div class=\"nav nav--stacked\">\n\t<h3 class=\"nav__heading -text-invert\">Info &amp; Services</h3>\n\t<ul class=\"nav__list\">\n\t\t<li class=\"nav__item\">\n\t\t\t<a class=\"nav__link -text-invert\" href=\"/idx/\">Search Listings</a>\n\t\t</li>\n\t</ul>\n</div>', 'cms');");
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #footer-column-3#';
        }
        // Insert #social-links# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
            . "(:agent, :team, 'social-links', '<a class=\"social__link social--facebook\" href=\"https://www.facebook.com/\" target=\"_blank\">\n\t<svg role=\"img\">\n\t\t<title>Facebook icon will open a new window</title>\n\t\t<use xlink:href=\"/inc/skins/ce/img/assets.svg#icon--facebook\" />\n\t</svg>\n</a>\n<a class=\"social__link social--twitter\" href=\"https://twitter.com/\" target=\"_blank\">\n\t<svg role=\"img\">\n\t\t<title>Twitter icon will open a new window</title>\n\t\t<use xlink:href=\"/inc/skins/ce/img/assets.svg#icon--twitter\" />\n\t</svg>\n</a>\n<a class=\"social__link social--instagram\" href=\"http://instagram.com/\" target=\"_blank\">\n\t<svg role=\"img\">\n\t\t<title>Instagram icon will open a new window</title>\n\t\t<use xlink:href=\"/inc/skins/ce/img/assets.svg#icon--instagram\" />\n\t</svg>\n</a>\n<a class=\"social__link social--linkedin\" href=\"https://www.linkedin.com/\" target=\"_blank\">\n\t<svg role=\"img\">\n\t\t<title>LinkedIn icon will open a new window</title>\n\t\t<use xlink:href=\"/inc/skins/ce/img/assets.svg#icon--linked-in\" />\n\t</svg>\n</a>\n<a class=\"social__link social--pinterest\" href=\"https://www.pinterest.com/\" target=\"_blank\">\n\t<svg role=\"img\">\n\t\t<title>Pinterest icon will open a new window</title>\n\t\t<use xlink:href=\"/inc/skins/ce/img/assets.svg#icon--pinterest\" />\n\t</svg>\n</a>\n<a class=\"social__link social--youtube\" href=\"https://www.youtube.com/\" target=\"_blank\">\n\t<svg role=\"img\">\n\t\t<title>YouTube icon will open a new window</title>\n\t\t<use xlink:href=\"/inc/skins/ce/img/assets.svg#icon--youtube\" />\n\t</svg>\n</a>', 'cms');");
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #social-links#';
        }
        // Insert #site-navigation# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
            . "(:agent, :team, 'site-navigation', '<ul class=\"nav__list nav--main -text-xs -inline -pad-bottom-lg@md \">\n\t<li class=\"nav__item -text-upper\">\n\t\t<a class=\"nav__link\" href=\"/idx/\">Search Listings</a>\n\t</li>\n\t<li class=\"nav__item -text-upper\">\n\t\t<a class=\"nav__link\" href=\"/listings.php\">Listings</a>\n\t</li>\n\t<li class=\"nav__item -text-upper\">\n\t\t<a class=\"nav__link\" href=\"/buying.php\">Buying</a>\n\t</li>\n\t<li class=\"nav__item -text-upper\">\n\t\t<a class=\"nav__link\" href=\"/selling.php\">Selling</a>\n\t</li>\n\t<li class=\"nav__item -text-upper\">\n\t\t<a class=\"nav__link\" href=\"/about.php\">About</a>\n\t</li>\n\t<li class=\"nav__item -text-upper\">\n\t\t<a class=\"nav__link\" href=\"/contact.php\">Contact</a>\n\t</li>\n</ul>', 'cms');");
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #site-navigation#';
        }
    // "Barbara Corcoran Special Edition"
    } else if (Settings::getInstance()->SKIN == 'bcse') {
        // Insert #agent-links# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
            . "(:agent, :team, 'navigation', '<ul class=\"main-navigation\">\n\t<li><a href=\"/idx/\">Listings</a></li>\n\t<li><a href=\"/buying.php\">Buyers</a></li>\n\t<li><a href=\"/selling.php\">Sellers</a></li>\n\t<li><a href=\"/about.php\">About</a></li>\n\t<li><a href=\"/contact.php\">Contact</a></li>\n</ul>', 'cms');");
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #navigation#';
        }

        // Insert #agent-phone# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
            . "(:agent, :team, 'phone-number', '(555) 555-5555', 'cms');");
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #phone-number#';
        }

        // Insert #social-media# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
            . "(:agent, :team, 'social-media', '<h3>Find me on...</h3>\n<ul class=\"social-media-links\">\n\t<li><a href=\"https://www.facebook.com/\" target=\"_blank\" class=\"ico-facebook slideout-link\" title=\"Find me on Facebook\">Facebook</a></li>\n\t<li><a href=\"https://plus.google.com/\" target=\"_blank\" class=\"ico-google slideout-link\" title=\"Find me on Google+\">Google+</a></li>\n\t<li><a href=\"https://twitter.com/\" target=\"_blank\" class=\"ico-twitter slideout-link\" title=\"Find me on Twitter\">Twitter</a></li>\n\t<li><a href=\"https://www.linkedin.com/\" target=\"_blank\" class=\"ico-linkedin slideout-link\" title=\"Find me on LinkedIn\">LinkedIn</a></li>\n\t<li><a href=\"https://www.pinterest.com/\" target=\"_blank\" class=\"ico-pinterest slideout-link\" title=\"Find me on Pinterest\">Pinterest</a></li>\n</ul>', 'cms');");
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #social-media#';
        }

    // LEC-2015 Agent Site
    } else if (in_array(Settings::getInstance()->SKIN, ['lec2015', 'lec-2015'])) {
        // Insert #nav-primary# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
        . "(:agent, :team, 'nav-primary', '<ul>\n\t<li><a href=\"/\">Home</a></li>\n\t<li><a href=\"/idx/\">Search</a></li>\n\t<li><a href=\"/buying.php\">Buying</a>\n\t<li><a href=\"/selling.php\">Selling</a>\n\t<li><a href=\"/about.php\">About Me</a></li>\n\t<li><a href=\"/contact.php\">Contact Me</a></li>\n</ul>', 'cms');");
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #nav-primary#';
        }

        // Insert #phone-number# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
        . "(:agent, :team, 'phone-number', '<div class=\"phone\">\n\t<span class=\"light\">Call Now</span>\n\t(555) 555-5555\n</div>', 'cms');");
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #phone-number#';
        }

    // LEC-2013 Agent Site
    } else if (in_array(Settings::getInstance()->SKIN, ['lec2013', 'lec-2013'])) {
        // Insert #lec-nav# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
        . "(:agent, :team, 'lec-nav', '<ul>\n<li><a href=\"/\">Home</a></li>\n<li><a href=\"/idx/\">Search Homes</a></li>\n<li><a href=\"/about.php\">About Me</a></li>\n<li><a href=\"/contact.php\" class=\"popup\">Contact</a></li>\n</ul>', 'cms');");
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #lec-nav#';
        }
    } else {
        // Insert #nav-primary# Snippet
        $insert_snippet = $db->prepare("INSERT IGNORE INTO `snippets` (`agent`, `team`, `name`, `code`, `type`) VALUES "
        . "(:agent, :team, 'nav-primary', '<ul class=\"nav primary\"><li><a href=\"/\">Home</a></li><li><a href=\"/idx/\">Search Listings</a></li><li><a href=\"/about.php\">About Me</a></li><li class=\"navi-contact\"><a href=\"/contact.php\"><em>Contact Me</em> | <em>555-555-5555</em></a></li></ul>', 'cms');");

        // Execute Query
        if (!$insert_snippet->execute(['agent' => $agent_id, 'team' => $team_id])) {
            $errors[] = 'Error Inserting Snippet: #nav-primary#';
        }
    }
}
