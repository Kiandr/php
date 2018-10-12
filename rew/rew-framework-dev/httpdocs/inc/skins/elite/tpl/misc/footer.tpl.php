<?php if (isset($_GET['popup'])) return; ?>
<?php global $_COMPLIANCE; ?>

<?php if (!isset($_GET['iframe'])) { ?>
    <div class="uk-container uk-container-center">
        <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
    </div>

    <footer class="fw fw-footer">
        <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimerFooter(); ?>
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-collapse">
                <div class="uk-width-small-1-1 uk-width-medium-2-6 uk-width-large-2-5">
                    <a href="/" class="logo">
                        <?=$this->getPage()->info('logoMarkupFooter'); ?>
                    </a>
                </div>
                <div class="uk-width-small-1-1 uk-width-medium-4-6 uk-width-large-3-5">
                    <div class="uk-grid footer-login">
                        <div class="uk-width-small-1-1 uk-width-medium-2-5 uk-width-large-2-5">
                            <?php rew_snippet('footer-quote'); ?>
                        </div>
                        <div
                            class="uk-width-small-1-1 uk-width-medium-3-5 uk-width-large-3-5 footer-login-btns">
                            <?php if (($user = User_Session::get()) && $user->isValid()) { ?>
                                <a href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_DASHBOARD']); ?>"
                                   class="btn uk-button" data-modal="dashboard">Dashboard</a>
                                <a href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_LOGOUT']); ?>"
                                   class="btn uk-button" data-modal="logout">Log Out</a>
                            <?php } else { ?>
                                <a href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']); ?>"
                                   class="btn uk-button" data-modal="login">Login</a>
                                <a href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_REGISTER']); ?>"
                                   class="btn uk-button" data-modal="register">Sign Up</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <hr class="uk-grid-divider">
            </div>
        </div>
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-small footer-links">
                <?php if (!empty(Settings::getInstance()->SETTINGS['cms']['office'])) { ?>
                    <div class="uk-width-small-1-1 uk-width-medium-2-6 uk-width-large-1-4">
                        <h5>Contact Us</h5>
                        <ul class="foot-contact uk-margin-large-bottom">
                            <li>
                                <a href="<?= sprintf(Settings::getInstance()->URLS['URL_OFFICE'], Settings::getInstance()->SETTINGS['cms']['office']['id']); ?>"><?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['cms']['office']['title']); ?></a>
                            </li>
                            <?php if (!empty(Settings::getInstance()->SETTINGS['cms']['office']['address'])) { ?>
                                <li><?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['cms']['office']['address']); ?></li>
                            <?php } ?>
                            <?php if (($location = implode(', ', array_filter(array(Settings::getInstance()->SETTINGS['cms']['office']['city'], Settings::getInstance()->SETTINGS['cms']['office']['state_abbrev'], Settings::getInstance()->SETTINGS['cms']['office']['country'], Settings::getInstance()->SETTINGS['cms']['office']['zip'])))) != '') { ?>
                                <li><?= Format::htmlspecialchars($location); ?></li>
                            <?php } ?>
                            <?php if (($phone = Settings::getInstance()->SETTINGS['cms']['agent']['office_phone'] ?: Settings::getInstance()->SETTINGS['cms']['office']['phone']) != '') { ?>
                                <li>Phone <?= Format::htmlspecialchars($phone); ?></li>
                            <?php } ?>
                            <?php if (($fax = Settings::getInstance()->SETTINGS['cms']['agent']['fax'] ?: Settings::getInstance()->SETTINGS['cms']['office']['fax']) != '') { ?>
                                <li>Fax <?= Format::htmlspecialchars($fax); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
                <div class="uk-width-small-1-1 uk-width-medium-4-6 uk-width-large-3-4">
                    <div class="uk-grid">
                        <div class="uk-width-1-1 uk-width-large-1-3 uk-margin-large-bottom">
                            <h5>Follow Us</h5>
                            <ul class="foot-social uk-clearfix">
                                <?php foreach ((new Backend_Agent(Settings::getInstance()->SETTINGS['cms']['agent']))->getSocialNetworks() as $network) { ?>
                                    <li>
                                        <a href="<?= Format::htmlspecialchars($network['url']); ?>" target="_blank" title="<?= ucfirst(Format::htmlspecialchars($network['slug'])); ?>">
                                            <i class="uk-icon uk-icon-<?= $network['slug']; ?> uk-icon-justify"></i> <span class="uk-hidden-small"> <?= Format::htmlspecialchars($network['name']); ?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php if (!in_array($popularPages = rew_snippet('popular-pages', false), array('', 'popular-pages'))) { ?>
                            <div class="uk-width-1-1 uk-width-large-1-3 uk-margin-large-bottom popular-pages-container">
                                <div class="toggle-container inactive">
                                    <h5>Popular Pages <span class="uk-visible-small"><i
                                                class="uk-icon uk-icon-open toggle"
                                                id="cta-toggle"></i></span></h5>
                                    <?= $popularPages; ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if (!in_array($usefulLinks = rew_snippet('useful-links', false), array('', 'useful-links'))) { ?>
                            <div class="uk-width-1-1 uk-width-large-1-3">
                                <div class="toggle-container inactive">
                                    <h5>Useful Links <span class="uk-visible-small"><i
                                                class="uk-icon uk-icon-open toggle"
                                                id="cta-toggle"></i></span></h5>
                                    <?= $usefulLinks; ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <section class="fw fw-copyright">
        <div class="uk-container uk-container-center">
            <div class="uk-grid">
                <div class="uk-width-1-1 uk-width-small-7-10">
                    <div class="copyright">
                        &copy; Copyright <?= date('Y'); ?>, <a
                            href="http://www.realestatewebmasters.com/" rel="nofollow"
                            target="_blank">Real Estate Webmasters</a>.
                        <?php if (Settings::getInstance()->SETTINGS['agent'] == 1 && !empty(Settings::getInstance()->MODULES['REW_IDX_SITEMAP'])) echo '<span><a href="/idx/sitemap.html?p=' . rand(1, 75) . '">Listings Site Map</a></span>'; ?>
                        <?= !empty($_COMPLIANCE['footer']) ? '<span class="footer-link-divider">&middot;</span><span>' . $_COMPLIANCE['footer'] . '</span>' : ''; ?>
                        <div class="cms"><?= $this->page->info('footer'); ?></div>
                    </div>
                </div>
                <div class="uk-width-1-1 uk-width-small-3-10">
                    <div class="rewcall">
                        <a id="rew-credit" href="http://www.realestatewebmasters.com/"
                           rel="nofollow" target="_blank"
                           title="Real Estate Web Design by Real Estate Webmasters">
                            <img src="<?= $this->getUrl(); ?>/img/rewlogo.png" width="150"
                                 alt="Real Estate Webmasters">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL -->
    <div class="uk-modal main-modal">
        <div class="uk-modal-dialog">
            <a class="uk-modal-close uk-close"></a>

            <div class="container">
            </div>
        </div>
    </div>
<?php } ?>

<?php // DYNAMIC VARIABLES ?>
<script type="text/javascript">
    window.REW = window.REW || {};
    <?php

    $base_url = $_SERVER['REQUEST_URI'];
    $query = array();
    if (($pos = strpos($_SERVER['REQUEST_URI'], '?')) !== false) {
        parse_str(substr($_SERVER['REQUEST_URI'], $pos + 1), $query);
        $base_url = substr($_SERVER['REQUEST_URI'], 0, $pos);
    }
    $settings = array(
        'ajax' => array(
            'urls' => array(

                // Generic
                'html' => Settings::getInstance()->SETTINGS['URL_IDX_AJAX'] . 'html.php',
                'json' => Settings::getInstance()->SETTINGS['URL_IDX_AJAX'] . 'json.php',
                'map' => Settings::getInstance()->SETTINGS['URL_IDX_AJAX'] . 'map.php',

                // Specific modules
                'bookmark' => Settings::getInstance()->SETTINGS['URL_IDX_AJAX'] . 'bookmark.php',
                'dismiss' => Settings::getInstance()->SETTINGS['URL_IDX_AJAX'] . 'dismiss.php',
                'saveSearch' => Settings::getInstance()->SETTINGS['URL_IDX_AJAX'] . 'json.php?saveSearch',
                'deleteSearch' => Settings::getInstance()->SETTINGS['URL_IDX_AJAX'] . 'json.php?deleteSearch',
                'editSearch' => Settings::getInstance()->SETTINGS['URL_IDX_AJAX'] . 'json.php?editSearch',

                // Dialogs
                'login' => Settings::getInstance()->SETTINGS['URL_IDX_LOGIN'],
                'connect' => Settings::getInstance()->SETTINGS['URL_IDX_CONNECT'],
                'register' => Settings::getInstance()->SETTINGS['URL_IDX_REGISTER'],
                'logout' => Settings::getInstance()->SETTINGS['URL_IDX_LOGOUT'],
                'dashboard' => Settings::getInstance()->SETTINGS['URL_IDX_DASHBOARD'],
                'create_search' => Settings::getInstance()->SETTINGS['URL_IDX'] . 'create_search.html',
                'verify' => sprintf(Settings::getInstance()->SETTINGS['URL_IDX_VERIFY'], '')
            ),
            'refresh_after' => array(
                'login', 'register', 'connect', 'connect-success', 'logout', 'login-success', 'register-success', 'verify', 'verify-success',
            )
        ),
        'urls' => array(
            'current' => Http_Uri::getFullUri(),
            'loader' => Http_Uri::getFullUri(),
            'search' => Settings::getInstance()->SETTINGS['URL_IDX_SEARCH'],
            'search_map' => Settings::getInstance()->URLS['URL_IDX_MAP'],
            'skin' => $this->getUrl(),
        ),
        'oauth' => array(
            'providers' => OAuth_Login::getProviders()
        ),
        'idx' => array(
            'feed' => Settings::getInstance()->IDX_FEED,
            'rentalTypes' => array_map('strtolower', IDX_Panel::get('price')->getRentalTypes()),
            'subTypes' => IDX_Panel_Subtype::getAllTypes(),
            'savedSearchMaxCount' => 500,
            'googleApiKey' => Settings::get('google.maps.api_key')
        ),
        'dialogs' => array(
            'dashboard' => array(
                'large' => true
            ),
            'dashboard-result' => array(
                'large' => true
            ),
            'map' => array(
                'large' => true
            ),
            'map-result' => array(
                'large' => true
            ),
            'contact' => array(
                'large' => true
            ),
        ),
        'qs_mirrored_fields' => $this->getPage()->getSkin()->getQsMirroredFields(),
        'app' => $this->getPage()->info('app'),
        'lang' => Settings::getInstance()->LANG,
    );
    if (!empty($_REQUEST['lead_id']))
        $settings['lead_id'] = (int)$_REQUEST['lead_id'];
    ?>
    window.REW.settings = <?= json_encode($settings); ?>;
    window.REW.url = <?= json_encode($base_url); ?>;
    window.REW.qs = <?= json_encode($query); ?>;
</script>

<?php $this->includeFile('tpl/footer.tpl.php'); ?>
</body>
</html>

