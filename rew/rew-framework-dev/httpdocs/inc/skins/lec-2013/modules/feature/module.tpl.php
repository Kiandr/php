<div id="feature">
	<div class="wrap">

		<h1><?=$this->config('heading'); ?></h1>
		<p><?=$this->config('subheading'); ?></p>
		<?php if ($search instanceof Module) { ?>
			<div class="quicksearch">
				<?php if (!empty(Settings::getInstance()->IDX_FEEDS)) { ?>
					<div class="tabset pills center">
						<ul>
							<?php foreach (Settings::getInstance()->IDX_FEEDS as $link => $feed) { ?>
								<li<?=(Settings::getInstance()->IDX_FEED === $link ? ' class="current"' : ''); ?>><a href="#" data-feed="<?=$link; ?>"><?=$feed['title']; ?></a></li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>
				<?php

					// Quick Search
					$search->display();

				?>
				<div class="nav">
					<a href="/idx/">Need More Options?  &nbsp; Try our Advanced Search</a>
					<span id="results-count" class="pright"></span>
				</div>
			</div>
		<?php } ?>
	</div>
	<div id="feature_cta">
		<div class="wrap">
			<?php rew_snippet('lec-message'); ?>
		</div>
	</div>
</div>