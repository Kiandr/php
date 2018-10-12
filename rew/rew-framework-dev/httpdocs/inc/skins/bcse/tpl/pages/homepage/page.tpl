<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<?php if (in_array($this->variable('feature'), array('agent', 'search', 'photo', 'slide_photo', 'slide_search'))) { ?>

	<?php if (in_array($this->variable('feature'), array('photo', 'slide_photo'))) { ?>

		<?php if ($this->container('slideshow')->countModules() > 0) { ?>
			<div id="feature">
				<div class="module">
					<div id="slideshow">
						<?php $this->container('slideshow')->loadModules(); ?>
					</div>
				</div>
			</div>
		<?php } else if ($background = $this->variable('feature.background')) { ?>
			<div class="wrap groupshot-wrap">
				<div class="groupshot">
					<img src="/thumbs/1903x/r<?=$background; ?>" alt="" />
					<?php if ($this->variable('feature.linkUrl') && $this->variable('feature.linkText')) { ?>
						<a class="buttonstyle" href="<?=$this->variable('feature.linkUrl'); ?>"><?=$this->variable('feature.linkText'); ?></a>
					<?php } ?>
				</div>
			</div>
		<?php } ?>

	<?php } else { ?>

		<div id="feature" style="
			<?=($background = $this->variable('feature.background')) ? 'background-image: url(/thumbs/1903x/r' . $background . ') !important;' : ''; ?>
			<?=($position = $this->variable('feature.position')) ? 'background-position: ' . $position . ' !important;' : ''; ?>
		">

			<div class="module">

				<?php if ($this->container('slideshow')->countModules() > 0) { ?>
					<div id="slideshow">
						<?php $this->container('slideshow')->loadModules(); ?>
					</div>
				<?php } ?>

				<div class="wrap">

					<?php if (in_array($this->variable('feature'), array('search', 'slide_search'))) { ?>

						<div class="feature-quicksearch">
							<?php if ($heading = $this->variable('feature.heading')) { ?>
								<h2><?=Format::htmlspecialchars($heading); ?></h2>
							<?php } ?>
							<?=$this->container('sub-feature')->loadModules(); ?>
							<?php if ($this->variable('feature.searchUrl') && $this->variable('feature.searchText')) { ?>
								<a class="buttonstyle colored-bg2" href="<?=$this->variable('feature.searchUrl'); ?>"><?=$this->variable('feature.searchText'); ?></a>
							<?php } ?>
						</div>

					<?php } else if ($this->variable('feature') === 'agent') { ?>

						<div class="l-align section-photo">
							<img class="realtor-photo" src="<?=$this->getUrl() . '/img/agent.png'; ?>" alt="">
							<?php if ($this->variable('feature.linkUrl') && $this->variable('feature.linkText')) { ?>
								<a class="buttonstyle meet-agent" href="<?=$this->variable('feature.linkUrl'); ?>"><?=$this->variable('feature.linkText'); ?></a>
							<?php } ?>
						</div>
						<div class="r-align section-text">
							<img class="hidden-mobile" data-src="<?=$this->getSchemeUrl() . '/img/quote.png'; ?>" alt="">
						</div>

					<?php } ?>

				</div>
			</div>
		</div>

	<?php } ?>

<?php } ?>

<div id="sub-feature">

	<?php // Quick Search ?>
	<?php if (!in_array($this->variable('feature'), array('search', 'slide_search'))) { ?>
		<?php if ($this->container('sub-feature')->countModules() > 0) { ?>
			<?=$this->container('sub-feature')->loadModules(); ?>
		<?php } ?>
	<?php } ?>

	<?php // Featured Communities ?>
	<?php $communities = $this->container('communities')->loadModules(false); ?>
	<?php if (!empty($communities)) { ?>
		<div id="feature-deck" class="module">
			<div class="wrap">
				<?=$communities; ?>
			</div>
		</div>
	<?php } ?>

</div>

<?php

// Only show content if not empty
$content = $this->container('content')->loadModules(false);
if (!empty($content)) { ?>
	<section class="homepage-content">
		<div class="wrap">
			<?php echo $content; ?>
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
		</div>
	</section>
<?php } ?>


<?php if (($snippet = rew_snippet('cta-about', false)) && $snippet !== 'cta-about' && $this->variable('about_us_cta') ) { ?>
	<section>
		<div class="wrap">
			<div class="l-align section-photo">
				<?php if(!empty($this->variable('about_us_cta.image'))) { ?>
					<img class="hidden-tablet" data-src="<?=$this->variable('about_us_cta.image'); ?>" width="100%" alt=""/>
				<?php } else { ?>
					<img class="hidden-tablet" data-src="<?=$this->getUrl(); ?>/img/couple.png" alt="" style="margin-left: -230px;" />
				<?php } ?>
			</div>
			<div class="r-align section-text">
				<?=$snippet; ?>
			</div>
		</div>
	</section>
<?php } ?>

<?php if (($snippet = rew_snippet('cta-contact', false)) && $snippet !== 'cta-contact') { ?>
	<section class="dark padded">
		<div class="wrap">
			<div class="central">
				<?=$snippet; ?>
			</div>
		</div>
	</section>
<?php } ?>

<?php if (!empty(Settings::getInstance()->MODULES['REW_PROPERTY_VALUATION'])) { ?>
	<?php if (($snippet = rew_snippet('cta-cma', false)) && $snippet !== 'cta-cma' && $this->variable('cma_cta')) { ?>
		<section>
			<div class="wrap">
				<div class="l-align section-photo">
					<?php if(!empty($this->variable('cma_cta.image'))) { ?>
						<img class="hidden-tablet" data-src="<?= $this->variable('cma_cta.image'); ?>" width="100%" alt="">
					<?php } else { ?>
						<img class="hidden-tablet" data-src="<?=$this->getUrl(); ?>/img/ipad-hand.png" style="margin-left: -60%;" alt="">
					<?php } ?>
				</div>
				<div class="r-align section-text">
					<?=$snippet; ?>
				</div>
			</div>
		</section>
	<?php } ?>
<?php } ?>

<?php if (($snippet = rew_snippet('cta-search', false)) && $snippet !== 'cta-search' && $this->variable('search_cta')) { ?>
	<section class="light">
		<div class="wrap">
			<div class="l-align section-text">
				<?=$snippet; ?>
			</div>
			<div class="r-align section-photo">
				<?php if(!empty($this->variable('search_cta.image'))) { ?>
					<img class="hidden-tablet" data-src="<?=$this->variable('search_cta.image'); ?>" width="100%" alt="">
				<?php } else { ?>
					<img class="hidden-tablet" data-src="<?=$this->getSchemeUrl() . '/img/tech.png'; ?>" style="margin-right: -200px; padding: 30px 0;" alt="">
				<?php } ?>
			</div>
		</div>
	</section>
<?php } ?>

<section class="light logo-section">
	<div class="wrap">
		<?=$this->getPage()->info('logoMarkupFooter'); ?>
	</div>
</section>

<?php if (($snippet = rew_snippet('cta-address', false)) && $snippet !== 'cta-address') { ?>
	<section class="padded">
		<div class="wrap">
			<div class="central">
				<?=$snippet; ?>
			</div>
		</div>
	</section>
<?php } ?>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>