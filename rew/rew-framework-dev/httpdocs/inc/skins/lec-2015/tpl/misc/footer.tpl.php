			<?php global $_COMPLIANCE; ?>
			<?php if (!isset($_GET['popup'])) { ?>
				<footer id="foot">
					<div class="wrap">
                        <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimerFooter(); ?>
						<?php if (Settings::getInstance()->SETTINGS['agent'] === 1) { ?>
							<div class="colset colset-1-sm colset-4" style="padding: 0 0 40px 0;">
								<?php rew_snippet('footer-links'); ?>
								<div class="col">
									<address>
										<?php rew_snippet('footer-contact'); ?>
									</address>
									<?=$this->container('social-share')->loadModules(); ?>
								</div>
							</div>
							<?php rew_snippet('footer-logos'); ?>
						<?php } ?>
						<div class="row">
							<div class="copyright">
							    <ul>
								<li>&copy; Copyright <?=date('Y'); ?>, <a href="http://www.realestatewebmasters.com/" rel="nofollow" target="_blank">Real Estate Webmasters</a>.</li>
								<?php if (Settings::getInstance()->SETTINGS['agent'] == 1 && !empty(Settings::getInstance()->MODULES['REW_IDX_SITEMAP'])) echo '<li><a href="/idx/sitemap.html?p=' . rand(1, 75) . '">Listings Site Map</a> </li>'; ?>
								<li><div class="cms"><?=$this->page->info('footer'); ?></div></li>
								<?=!empty($_COMPLIANCE['footer']) ? '<li><div class="compliance">' . $_COMPLIANCE['footer'] . '</div></li>' : ''; ?>
								</ul>
							</div>
							<div class="rewcall">
								<a id="rew-credit" href="http://www.realestatewebmasters.com/" rel="nofollow" target="_blank" title="Real Estate Web Design by Real Estate Webmasters">
									<img src="/img/logos/rew-logo-on-dark.svg" width="100" alt="Real Estate Webmasters">
								</a>
							</div>
						</div>
					</div>
				</footer>
			<?php } ?>
		</div>
		<?php $this->includeFile('tpl/footer.tpl.php'); ?>
	</body>
</html>