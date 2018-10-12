<?php global $_COMPLIANCE; ?>
			<?php if (!isset($_GET['popup'])) { ?>
				<footer id="foot">
					<div class="wrap">
                        <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimerFooter(); ?>
						<?php if (Settings::getInstance()->SETTINGS['agent'] === 1) { ?>
							<?=$this->container('footer')->loadModules(); ?>
							<div class="row">
								<?=$this->container('testimonial')->loadModules(); ?>
								<?php rew_snippet('lec-footer'); ?>
							</div>
						<?php } ?>
						
						<div class="copyright">							
							<a id="rew-credit" href="http://www.realestatewebmasters.com/" rel="nofollow" target="_blank" title="Real Estate Web Design By Real Estate Webmasters">Real Estate Webmasters</a>
							&copy; Copyright <?=date('Y'); ?>, <a href="http://www.realestatewebmasters.com/" rel="nofollow" target="_blank">Real Estate Webmasters</a>.
							<?php if (Settings::getInstance()->SETTINGS['agent'] == 1 && !empty(Settings::getInstance()->MODULES['REW_IDX_SITEMAP'])) echo '<a href="/idx/sitemap.html?p=' . rand(1, 75) . '">Listings Site Map</a>'; ?>
							<?=!empty($_COMPLIANCE['footer']) ? $_COMPLIANCE['footer'] : '';?>
							<?=$this->page->info('footer'); ?>
						</div>
					</div>
				</footer>
			<?php } ?>

		</div>

		<?php $this->includeFile('tpl/footer.tpl.php'); ?>

		<script>
			var MTIProjectId='b6fdb272-0697-40af-b7ff-cdca5150df04';
			(function() {
				var mtiTracking = document.createElement('script');
				mtiTracking.type='text/javascript';
				mtiTracking.async='true';
				mtiTracking.src='//fast.fonts.com/t/trackingCode.js';
				(document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild( mtiTracking );
			})();
		</script>

	</body>
</html>