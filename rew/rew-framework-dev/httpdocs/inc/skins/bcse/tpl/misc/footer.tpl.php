			<?php global $_COMPLIANCE; ?>
			<?php if (!isset($_GET['popup'])) { ?>
				<footer id="foot">
					<div class="wrap">
                        <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimerFooter(); ?>
						<div class="row">
							<div class="copyright">
								<ul>
									<li>&copy; Copyright <?=date('Y'); ?>, <a href="http://www.realestatewebmasters.com/" rel="nofollow" target="_blank">Real Estate Webmasters.</a></li>
									<?php if (Settings::getInstance()->SETTINGS['agent'] == 1 && !empty(Settings::getInstance()->MODULES['REW_IDX_SITEMAP'])) echo '<li><a href="/idx/sitemap.html?p=' . rand(1, 75) . '">Listings Site Map</a></li>'; ?>
									<?=!empty($_COMPLIANCE['footer']) ? '<li>' . $_COMPLIANCE['footer'] . '</li>' : ''; ?>
									<li><div class="cms"><?=$this->page->info('footer'); ?></div></li>
								</ul>
							</div>
							<div class="rewcall">
								<span>Site proudly designed by</span>
								<a id="rew-credit" href="http://www.realestatewebmasters.com/" rel="nofollow" target="_blank" title="Real Estate Web Design by Real Estate Webmasters">
									<img src="<?=$this->getUrl(); ?>/img/rewlogo.png" width="150" alt="Real Estate Webmasters">
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