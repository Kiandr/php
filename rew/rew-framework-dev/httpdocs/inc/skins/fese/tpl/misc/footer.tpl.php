        <?php global $_COMPLIANCE; ?>
        <?php if (!isset($_GET['popup'])) { ?>
            <nav id="nav" class="inactive" aria-hidden="true">
                <div class="wrp S4">
                    <span class="phone"><?php rew_snippet('site-phone-number'); ?></span>
                    <a id="nav-close" class="btn TR">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12.531" height="12.531" viewBox="0 0 12.531 12.531">
                            <path d="M79.908,27.171l-5.091,5.091,5.069,5.069L78.7,38.517l-5.069-5.07-5.064,5.064-1.185-1.185,5.064-5.064L67.36,27.176l1.185-1.185,5.086,5.086,5.091-5.091Z" transform="translate(-67.375 -26)" fill="#fff" />
                        </svg>
                    </a>
                    <?php rew_snippet('site-navigation'); ?>
                    <?php $this->container('user-links')->loadModules(); ?>
                </div>
            </nav>
            <?php rew_snippet('site-contact-cta'); ?>
            <footer id="foot">
                <div id="foot--b">
                    <div class="wrp S4 foot-b-wrp">
                        <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimerFooter(); ?>
                        <div class="foot-links">
                            <?php rew_snippet('site-footer-links'); ?>
                            <span class="phone">
                                <?php rew_snippet('site-phone-number'); ?>
                            </span>
                        </div>
                        <?php if (Container::getInstance()->get(REW\Core\Interfaces\SettingsInterface::class)->SETTINGS['agent'] === 1) { ?>
                        <div class="foot-contact">
	                        <div class="foot-colset">
		                        <?php rew_snippet('site-footer-contact'); ?>
	                        </div>
	                       <div class="foot-colset">
		                        <?php rew_snippet('site-footer-broker'); ?>
	                        </div>
                        </div>
                        <?php } ?>
                        <div class="L">
                            <div class="foot-copy">
                                &copy; Copyright <?=date('Y'); ?>,
                                <a href="http://www.realestatewebmasters.com/" rel="nofollow" target="_blank">Real Estate Webmasters</a> All Rights Reserved.
                            </div>
                            <div class="cms">
                                <?=$this->page->info('footer'); ?>
                                <?php

                                    // Include link to IDX site map on main website
                                    if (Settings::getInstance()->MODULES['REW_IDX_SITEMAP']
                                        && Settings::getInstance()->SETTINGS['agent'] == 1) {
                                        echo '&nbsp;&bull;&nbsp;&nbsp;<a href="/idx/sitemap.html?p=' . rand(1, 75) . '">Listings Site Map</a> ';
                                    }

                                ?>
                                &nbsp;&bull;&nbsp;<a href="/privacy-policy.php">Privacy Policy</a>
                            </div>
                        </div>
                        <div class="R foot-r">
                            <a class="rew-logo" href="http://www.realestatewebmasters.com/" rel="nofollow" target="_blank">
                                <img src="<?=$this->getUrl(); ?>/img/rew-logo-white-small.png" width="150" alt="Real Estate Webmasters">
                            </a>
                        </div>						
                        <?=!empty($_COMPLIANCE['footer']) ? $_COMPLIANCE['footer'] : ''; ?>
                    </div>
                </div>
            </footer>
        <?php } ?>
        </div>
        <?php $this->includeFile('tpl/footer.tpl.php'); ?>
    </body>
</html>