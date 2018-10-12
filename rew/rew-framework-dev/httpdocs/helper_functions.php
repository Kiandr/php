<?php

use REW\Backend\Partner\Inrix\DriveTime;
use REW\Core\Interfaces\LogInterface;

/**
 * Get name of current page (either $_GET['load_page'] or $_GET['page'])
 * @deprecated
 * @return bool|false
 */
function rew_loadpage()
{

    if (!empty($_GET['load_page'])) {
        return $_GET['load_page'];
    } elseif ($_GET['app'] == 'blog' && $_GET['page'] == 'entry' &&  $_REQUEST['snippet'] == true) {
        return;
    } elseif (!empty($_GET['page']) && !is_numeric($_GET['page'])) {
        return $_GET['page'];
    } else {
        return false;
    }
}

/**
 * Load snippet
 * @param string $match
 * @param bool $print
 * @param int $agent_id
 * @param array $snippet row from the DB
 * @return string
 */
function rew_snippet($match, $print = true, $agent_id = null, $snippet = null)
{

    // Global Resources
    global $page, $row;

    // Profile start
    $timer = Profile::timer()->stopwatch(__FUNCTION__)->setDetails('<code>' . $match . '</code>')->start();

    // Snippet not prequeried
    if (!$snippet) {
        // DB Connection
        $db = DB::get('cms');

        // Find snippet from database
        $snippet = $db->prepare("SELECT `name`, `code`, `type` FROM `snippets` WHERE ((`agent` IS NULL AND `team` IS NULL) OR (`agent` <=> :agent AND `team` <=> :team)) AND `name` = :name LIMIT 1;");
        $snippet->execute(array(
            'agent' => (!empty($agent_id) ? $agent_id : Settings::getInstance()->SETTINGS['agent']),
            'team' => (!empty($agent_id) ? null : Settings::getInstance()->SETTINGS['team']),
            'name' => trim($match, '#')
        ));
        $snippet = $snippet->fetch();
    }
    if (!empty($snippet)) {
        // IDX Snippet
        if ($snippet['type'] == 'idx') {
            // Search Criteria
            if (!empty($snippet['code'])) {
                $criteria = unserialize($snippet['code']);
                if (!empty($criteria) && is_array($criteria)) {
                    $idx = Util_IDX::getIdx();

                    // Searchable Fields
                    $search_fields = search_fields($idx);
                    $search_fields = array_keys($search_fields);
                    $search_fields = array_merge(
                        DriveTime::IDX_FORM_FIELDS,
                        ['idx', 'map', 'view', 'search_location', 'snippet_title', 'page_limit', 'sort', 'order', 'view', 'feed'],
                        $search_fields
                    );

                    // Order / Sort
                    $criteria['order'] = '';
                    $criteria['sort']  = '';
                    if (!empty($criteria['sort_by'])) {
                        list ($criteria['sort'], $criteria['order']) = explode("-", $criteria['sort_by'], 2);
                    }

                    // Set $_REQUEST Data
                    if (!empty($search_fields)) {
                        foreach ($search_fields as $field) {
                            if (isset($criteria[$field])) {
                                if (!isset($_REQUEST[$field])) {
                                    $_REQUEST[$field] = $criteria[$field];
                                }
                            }
                        }
                    }
                }

                // Snippet Over-Rides Feed Defaults
                $_REQUEST['search_city'] = isset($_REQUEST['search_city']) ? $_REQUEST['search_city'] : false;
                $_REQUEST['search_type'] = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : false;
            }

            // Get List Of IDX Feeds Or The Main One If Just One.
            $idx_feeds = !empty(Settings::getInstance()->IDX_FEEDS) ? array_keys(Settings::getInstance()->IDX_FEEDS) : array(Settings::getInstance()->IDX_FEED);

            $requested_idx = Util_IDX::getIdx($_REQUEST['feed']);

            // Load IDX Search Page If Main Site OR If It's An Agent or Team Site, The Agent or Team Site Has Access To The Feed.
            if (Settings::getInstance()->SETTINGS['agent'] == 1
                || (
                    !empty(Settings::getInstance()->SETTINGS['agent_idxs'])
                    && (
                        (
                            in_array($_REQUEST['feed'], Settings::getInstance()->SETTINGS['agent_idxs'])
                            && in_array($_REQUEST['feed'], $idx_feeds)
                        )
                        || (
                            $requested_idx->isCommingled()
                            && array_intersect($requested_idx->getFeeds(), Settings::getInstance()->SETTINGS['agent_idxs']) != array()
                        )
                    )
                )
                || (
                    !empty(Settings::getInstance()->SETTINGS['team_idxs'])
                    && (
                        (
                            in_array($_REQUEST['feed'], Settings::getInstance()->SETTINGS['team_idxs'])
                            && in_array($_REQUEST['feed'], $idx_feeds)
                        )
                        || (
                            $requested_idx->isCommingled()
                            && array_intersect($requested_idx->getFeeds(), Settings::getInstance()->SETTINGS['team_idxs']) != array()
                        )
                    )
                )
            ) {
                // IDX Snippet
                $_REQUEST['snippet'] = true;
                $_REQUEST['search_by'] = 'snippet';
                $_REQUEST['snippet_price_table'] = ($criteria['price_ranges'] == 'true');
                $_REQUEST['hide_tags'] = ($criteria['hide_tags'] === 'true');

                // Snippet Title
                if (!empty($criteria['snippet_title'])) {
                    $_REQUEST['snippet_title'] = $criteria['snippet_title'];
                }

                // Snippet View
                $view = isset($_GET['view']) ? $_GET['view'] : $criteria['view'];

                // IDX Snippet Already Included
                global $idxSnippetAlreadyIncluded;
                if (!empty($idxSnippetAlreadyIncluded)) {
                    // Don't Show Price Ranges for 2nd Snippet
                    $_REQUEST['snippet_price_table'] = false;
                    if ((!empty($_REQUEST['p']) && ($_REQUEST['p'] > 1)) || preg_match('/((?:\d+|under|over)-\d+)/', $_REQUEST['price_range'])) {
                        $timer->stop();
                        return;
                    }
                }

                $container = \Container::getInstance();
                $application = $container->make(REW\Core\Application::class);
                $idxSnippetControllerName = $application->getRouteController($page, 'idx/snippet');

                if ($idxSnippetControllerName) {
                    ob_start();
                    $idxSnippetController = $container->make($idxSnippetControllerName);
                    call_user_func_array($idxSnippetController, []);
                    $details = ['category_html' => ob_get_clean()];
                } else {
                    $details = $page->load('idx', 'snippet', null);
                }

                $idxSnippetAlreadyIncluded = true;

                // Clean $_REQUEST Data
                if (!empty($search_fields) && !empty($criteria)) {
                    foreach ($search_fields as $field) {
                        if (isset($criteria[$field])) {
                            unset($_REQUEST[$field]);
                        }
                    }
                }

                // Build and Set DriveTime Polygon
                if (!empty(Settings::getInstance()->MODULES['REW_IDX_DRIVE_TIME']) && in_array('drivetime', Settings::getInstance()->ADDONS)) {
                    $container = \Container::getInstance();
                    $log = $container->make(LogInterface::class);
                    $drive_time = $container->make(DriveTime::class);
                    try {
                        $drive_time->modifyServerMapRequests(
                            $_REQUEST['dt_address'],
                            $_REQUEST['dt_direction'],
                            $_REQUEST['dt_travel_duration'],
                            $_REQUEST['dt_arrival_time'],
                            $_REQUEST['place_zoom'],
                            $_REQUEST['place_lat'],
                            $_REQUEST['place_lng']
                        );
                    } catch (Exception $e) {
                        $log->error($e->getMessage());
                    }
                }

                // Set Snippet
                $_REQUEST['snippet'] = true;

                // Return HTML
                $output = $details['category_html'];
            } else {
                // Return Nothing As This Agent Site Does Not Have Access To The IDX Snippet's Content
                $output = '';
            }

        // Directory Snippet
        } else if ($snippet['type'] == 'directory') {
            // Directory Snippet Criteria
            if (!empty($snippet['code'])) {
                $criteria = unserialize($snippet['code']);
                if (!empty($criteria) && is_array($criteria)) {
                    $search_fields = array('search_category', 'search_keyword', 'snippet_title', 'page_limit', 'sort_by');
                    if (!empty($search_fields)) {
                        foreach ($search_fields as $field) {
                            if (isset($criteria[$field]) && !isset($_GET[$field])) {
                                $_GET[$field] = $criteria[$field];
                            }
                        }
                    }
                }
            }

            // Snippet Title
            if (!empty($criteria['snippet_title'])) {
                $_GET['snippet_title'] = $criteria['snippet_title'];
            }

            // Load Directory Snippet
            $snippet = $page->load('directory', 'snippet');

            // Return HTML
            $output = $snippet['category_html'];

        // Module Snippet
        } else if ($snippet['type'] == 'module') {
            // Load Module
            ob_start();
            $module = json_decode($snippet['code'], true);
            if (!empty($module)) {
                $module = new Module($module['name'], $module['config']);
                $page->container('snippet')->addModule($module)->display();
            } else {
                // We were not able to json_decode, soft error
                echo 'Something went wrong when loading #' . $snippet['name'] . '#. Sorry for the inconvenience.';
            }

            // Return Module HTML
            $output = ob_get_clean();

        // Form Snippet
        } else if ($snippet['type'] == 'form') {
            // Start Buffer
            ob_start();

            // Load User Object
            $user = User_Session::get();

            // Submit Form
            $sent = false;
            if (isset($_GET['submit'])) {
                // Process Forms
                require_once Settings::getInstance()->DIRS['BACKEND'] . 'inc/php/functions/funcs.ContactSnippets.php';
                if (($snippet['name'] == 'form-approve') && !empty($_POST['approveform'])) {
                    $sent = contactForm(1, 'Approve Form', true);
                }
                if (($snippet['name'] == 'form-buyers')  && !empty($_POST['buyersform'])) {
                    $sent = contactForm(2, 'Buyer Form', true);
                }
                if (($snippet['name'] == 'form-contact') && !empty($_POST['contactform'])) {
                    $sent = contactForm(3, 'Contact Form');
                }
                if (($snippet['name'] == 'form-seller')  && !empty($_POST['sellerform'])) {
                    $sent = contactForm(4, 'Seller Form', true);
                }
                if (($snippet['name'] == 'form-cma')     && !empty($_POST['cmaform'])) {
                    $sent = contactForm(10, 'CMA Form');
                }
                if (($snippet['name'] == 'form-cma-capture') && !empty($_POST['cmaform'])) {
                    $sent = contactForm(10, 'CMA Form');
                }
                if (($snippet['name'] == 'form-testimonial') && !empty($_POST['testimonialform'])) {
                    $sent = contactForm(11, 'Testimonial Form');
                }
                if (($snippet['name'] == 'form-seller-radio-simple')  && !empty($_POST['sellerradiosimpleform'])) {
                    $sent = contactForm(4, 'Radio Seller Form', true);
                }
                if (($snippet['name'] == 'form-seller-radio-boxed')  && !empty($_POST['sellerradioboxedform'])) {
                    $sent = contactForm(4, 'Radio Seller Form', true);
                }
                if (($snippet['name'] == 'form-guaranteed')  && !empty($_POST['guaranteedsoldform'])) {
                    $sent = contactForm(13, 'Guaranteed Sold Form', false, false, false);
                }

                // Display Errors
                if (!empty($sent['errors'])) {
                    echo '<div class="notice notice--negative"><h5 class="title">Oops! Your Form Contains Errors.</h5><ul><li>' . implode('</li><li>', $sent['errors']) . '</li></ul></div>';
                }
                // Display Success
                if (!empty($sent['success'])) {
                    echo '<div class="notice notice--positive form_snippet"><p>Thanks for your interest! We\'ll get back to you as soon as possible.</p></div>';
                    // Conversion Tracking
                    $ppc = Util_CMS::getPPCSettings();
                    $snippet['name'] = ($snippet['name'] === 'form-cma-capture') ? 'form-cma' : $snippet['name'];
                    if (!empty($ppc) && $ppc['enabled'] === 'true' && !empty($ppc[$snippet['name']])) {
                        echo $ppc[$snippet['name']];
                    }
                }
            } else {
                // Auto-Fill Form Data
                if ($user instanceof User_Session && $user->isValid()) {
                    $_POST['onc5khko']      = $user->info('first_name');
                    $_POST['sk5tyelo']      = $user->info('last_name');
                    $_POST['mi0moecs']      = $user->info('email');
                    $_POST['telephone']     = $user->info('phone');
                    $_POST['phone_cell']    = $user->info('phone_cell');
                    $_POST['fm-addr']       = $user->info('address1');
                    $_POST['fm-town']       = $user->info('city');
                    $_POST['fm-state']      = $user->info('state');
                    $_POST['fm-postcode']   = $user->info('zip');
                }
            }

            // Give User the Option to Opt-In
            $opt_in = '';
            if ($user->info('opt_marketing') != 'in') {
                // Anti-Spam Settings
                $anti_spam = array(
                    'optin' => Format::htmlspecialchars(Settings::get('anti_spam.optin')),
                    'consent_text' => Format::htmlspecialchars(Settings::get('anti_spam.consent_text'))
                );

                // Build {opt_in} Tag Replacement
                $opt_in = '<label><input type="checkbox" name="opt_marketing" value="in"' . ($anti_spam['optin'] == 'in' ? ' checked' : '') . '> '
                    . ((!empty($anti_spam['consent_text'])
                        ? $anti_spam['consent_text']
                        : 'Please send me updates concerning this website and the real estate market.')
                    . '</label>');
            }

            // Replace {opt_in} Tag
            $snippet['code'] = str_replace('{opt_in}', $opt_in, $snippet['code']);

            // UTC-8 Fix
            $snippet['code'] = mb_convert_encoding($snippet['code'], 'html-entities', 'utf-8');

            // Parse Snippet as HTML Document
            $dom = new DOMDocument();
            $dom->loadHTML($snippet['code']);

            // Set Form Values
            if (empty($sent['success'])) {
                // Get All Form Fields
                $xpath = new DOMXpath($dom);
                $inputs = $xpath->query('//input | //select | //textarea');

                // Process Form Fields
                for ($i = 0; $i < $inputs->length; $i++) {
                    $input = $inputs->item($i);
                    $name  = $input->getAttribute('name');
                    $type  = $input->getAttribute('type');
                    if (isset($_POST[$name]) || (isset($_GET['submit']) && ($type == 'checkbox' || $type == 'radio'))) {
                        $value = $_POST[$name];
                        if ($input->nodeName == 'input') {
                            if ($type == 'checkbox' || $type == 'radio') {
                                if ($value == $input->getAttribute('value')) {
                                    $input->setAttribute('checked', 'checked');
                                } else {
                                    $input->removeAttribute('checked');
                                }
                            } else {
                                $input->setAttribute('value', $value);
                            }
                        }
                        if ($input->nodeName == 'textarea') {
                            $input->nodeValue = $value;
                        }
                        if ($input->nodeName == 'select') {
                            $options = $xpath->query('option', $input);
                            for ($j = 0; $j < $options->length; $j++) {
                                $option = $options->item($j);
                                if ($option->getAttribute('value') == $value) {
                                    $option->setAttribute('selected', 'selected');
                                }
                            }
                        }
                    }
                }
            }

            // Output HTML Form
            echo preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $dom->saveHTML()));

            // Return HTML Form
            $output = ob_get_clean();

        // BDX Snippet
        } else if ($snippet['type'] == 'bdx') {
            $criteria = unserialize($snippet['code']);
            if (!empty($criteria['state'])) {
                // Set BDX snippet number for multi snippet pagination
                if (empty($_REQUEST['bdx-snippet'])) {
                    $_REQUEST['bdx-snippet'] = 1;
                } else {
                    $_REQUEST['bdx-snippet']++;
                }

                // Build app object
                require __DIR__ . '/builders/appBuilder.php';

                // Snippet criteria
                $search_state = str_replace(' ', '-', strtolower($app->states[$criteria['state']]));
                $search_mode = (!empty($criteria['snippet_mode']) ? $criteria['snippet_mode'] : "homes");

                // Build Search Environment
                $env = $app->environment();

                // Incorporate Query String
                if (!empty($criteria['search'])) {
                    $search_criteria = http_build_query(array('search' => $criteria['search']));
                    $env['QUERY_STRING'] = $search_criteria;
                }

                // Reset Query
                $app->request()->resetQuery();

                $env['PATH_INFO'] = '/' . $search_state . '/' . $search_mode . '/';
                $env['REQUEST_METHOD'] = \Slim\Http\ModifiedRequest::METHOD_GET;

                // Over-Ride Page Content
                if (!empty($_REQUEST['bdx-p']) && ($_REQUEST['bdx-p'] > 1)) {
                    $row['page_title']  = $row['page_title'] . ' - Page #' . $_REQUEST['bdx-p'];
                    $row['category_html'] = $match;
                }

                // Generate snippet output
                $output = '<div class="bdx-snippet">';
                $output .= '<h2>' . $criteria['snippet_title'] . '</h2>';
                ob_start();
                $app->run();
                $output .= ob_get_clean();
                $output .= '</div>';
            }

        // Realty Trac Snippet
        } else if ($snippet['type'] == 'rt') {
            $criteria = unserialize($snippet['code']);
            if (!empty($criteria)) {
                // Set RT snippet number for multi snippet pagination
                if (empty($_REQUEST['rt-snippet'])) {
                    $_REQUEST['rt-snippet'] = 1;
                } else {
                    $_REQUEST['rt-snippet']++;
                }

                // Start a new app.
                $app = new RealtyTrac\App('snippet');

                // Build Search Environment
                $env = $app->environment();

                // Set the criteria for this in the environment
                $env['criteria'] = $criteria;

                // Over-Ride Page Content
                if (!empty($_REQUEST['rt-cursor'])) {
                    $row['category_html'] = $match;
                }

                // Generate snippet output
                $output = '<div class="rt-snippet">';
                $output .= '<h2>' . $criteria['snippet_title'] . '</h2>';
                ob_start();
                $app->run();
                $output .= ob_get_clean();
                $output .= '</div>';
            }
        } else if ($snippet['type'] === 'cms') {
            // CMS Snippet HTML
            $output = $snippet['code'];

        // Unknown Type
        } else {
            // Do Nothing
            $output = $match;
        }
    } else {
        // Trim Match
        if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) {
            // Find Featured Community by Snippet
            $community = $db->prepare("SELECT `id` FROM `featured_communities` WHERE `snippet` = :snippet LIMIT 1;");
            $community->execute(array('snippet' => trim($match, '#')));
            $community = $community->fetchColumn();
            if (!empty($community)) {
                // Community Module
                $output = $page->container('snippet')->addModule('communities', array(
                    'mode' => $community,
                    'html' => true
                ))->display(false);
            } else {
                // Snippet not Found
                $output = $match;
            }
        }
    }

    // Profile end
    $timer->stop();

    // Return Output
    if (empty($print)) {
        return $output;
    }

    // Display Output
    print $output;
}
