            <?php if (!isset($_GET['popup'])) { ?>
                <?=$this->container('subscribe-cta')->loadModules(); ?>
                <footer id="foot" class="footer -pad-vertical-lg -text-center@md -text-center@sm -text-center@xs">
                    <div class="container -pad-0">
                        <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimerFooter(); ?>
                        <div class="columns">
                            <div class="column -width-1/4 -width-1/2@md -width-1/1@sm -width-1/1@xs">
                                <div class="footer__col -pad-horizontal">
                                    <h3 class="nav__heading -mar-bottom-xxl">
                                        <a class="footer__logo -mar-top-xs" href="/">
                                            <?=$this->getPage()->info('logoMarkupFooter'); ?>
                                        </a>
                                    </h3>
                                    <div class="footer__office -pad-bottom">
                                        <span class="footer__company"><?php rew_snippet('site-business-name'); ?></span>
                                        <p>
                                            <?php rew_snippet('site-address'); ?>
                                        </p>
                                        <?php rew_snippet('site-phone-number'); ?>
                                        <a href="mailto:<?php rew_snippet('site-email'); ?>">
                                            <?php rew_snippet('site-email'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="column -width-1/4 -width-1/2@md -width-1/1@sm -width-1/1@xs">
                                <?php rew_snippet('footer-column-2'); ?>
                            </div>
                            <div class="column -width-1/4 -width-1/2@md -width-1/1@sm -width-1/1@xs">
                                <?php rew_snippet('footer-column-3'); ?>
                            </div>
                            <div class="column -width-1/4 -width-1/2@md -width-1/1@sm -width-1/1@xs">
                                <div class="nav">
                                    <h3 class="nav__heading -text-invert">Follow Us</h3>
                                    <div class="nav__item -pad-horizontal">
                                        <?php rew_snippet('social-links'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="footer__bottom -pad-vertical -clear -text-xs">
                            <div class="-left@lg -left@xl -mar-vertical-sm -pad-horizontal-sm">
                                &copy; Copyright <?=date('Y'); ?>,
                                <a href="http://www.realestatewebmasters.com/" rel="nofollow" target="_blank">Real Estate Webmasters</a> All Rights Reserved.
                            </div>
                            <div class="-right@lg -right@xl -mar-bottom footer--links">
                                <a class="-right" href="http://www.realestatewebmasters.com/" target="_blank">
                                    <svg role="img" class="logo logo--rew">
                                        <title>Real Estate Webmasters Logo</title>
                                        <use xlink:href="/inc/skins/ce/img/assets.svg#logo--rew"/>
                                    </svg>
                                </a>
                                <div class="nav -inline">
                                    <?php

                                        // Include link to IDX site map on main website
                                        if (Settings::getInstance()->MODULES['REW_IDX_SITEMAP']) {
                                            echo '<a href="/idx/sitemap.html?p=' . rand(1, 75) . '">Listings Site Map</a>';
                                        }

                                    ?>
                                    <?=$this->page->info('footer'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            <?php } ?>
        </div>
        <?php $this->includeFile('tpl/footer.tpl.php'); ?>
    </body>
</html>
