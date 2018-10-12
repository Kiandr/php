<div class="module">
	<div class="wrap">
		<div class="feature-video-main">
			<div class="l-align section-photo">
				<img class="realtor-photo" src="<?=$skin->getUrl(); ?>/img/agent.png" alt="">
			</div>
			<div class="r-align section-video">
				<div class="wrap">
					<span class="play-icon">
						<svg width="43" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 375.4 375.4" enable-background="new 0 0 375.4 375.4" xml:space="preserve">
							<g><path d="M134.6,93.6c3.7-9.5,12.5-8.8,20.5-3.3c18.6,12.7,110,74.2,115.9,78.5c5.8,4.2,11.1,9.1,11.1,17c0,6.4-4.3,10.6-8.7,14.3c-4.4,3.8-113.9,81.2-124.2,87.6c-6.6,4.1-15.9-2.7-16.1-11.5c-0.2-9-0.1-18-0.1-26.9C133.3,249.2,133.5,96.4,134.6,93.6z"/></g>
							<path d="M187.7,375.4C84.2,375.4,0,291.2,0,187.7S84.2,0,187.7,0c103.5,0,187.7,84.2,187.7,187.7S291.2,375.4,187.7,375.4zM187.7,24C97.4,24,24,97.4,24,187.7c0,90.3,73.4,163.7,163.7,163.7S351.4,278,351.4,187.7C351.4,97.4,278,24,187.7,24z"/>
						</svg>
					</span>
					<h3 class="small-caps"><?=Format::htmlspecialchars($heading); ?></h3>
					<h2><?=Format::htmlspecialchars($subheading ?: '&nbsp;'); ?></h2>
					<div class="video-container">
						<?php if (!empty($video_id)) { ?>
							<?php if (stripos($video_id, 'vimeo') === false) { ?>
								<iframe width="275" height="155" src="https://www.youtube.com/embed/<?=$video_id; ?>?rel=0&amp;controls=0&amp;showinfo=0&amp;modestbranding=1" frameborder="0" allowfullscreen></iframe>
							<?php } else { ?>
								<iframe width="275" height="155" src="<?=$video_id; ?>" frameborder="0" allowfullscreen></iframe>
							<?php } ?>
						<?php } ?>
					</div>
					<?php if ($linkUrl && $linkText) { ?>
						<a class="buttonstyle colored-bg2" href="<?=$linkUrl; ?>">
							<?=Format::htmlspecialchars($linkText); ?>
						</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>