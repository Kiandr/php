<div class="module">
	<div class="wrap">
		<div class="feature-radio-main">
			<div class="l-align section-photo">
				<img class="realtor-photo" src="<?=$skin->getUrl(); ?>/img/agent.png" alt="">
			</div>
			<div class="r-align section-guaranteed">
				<div class="wrap">
					<span class="tag-icon">
						<svg width="48" height="40" version="1.1" id="Icons" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="384.7 -381.5 33.8 27.8" enable-background="new 384.7 -381.5 33.8 27.8" xml:space="preserve">
							<path fill="#404040" d="M415.5-381.5h-7c-1.7,0-4,1-5.1,2.1l-11.8,11.8c-1.2,1.2-1.2,3.1,0,4.2l8.8,8.8c1.2,1.2,3.1,1.2,4.2,0
								l11.8-11.8c1.2-1.2,2.1-3.5,2.1-5.1v-7C418.5-380.2,417.2-381.5,415.5-381.5z M411.5-371.5c-1.7,0-3-1.3-3-3c0-1.7,1.3-3,3-3
								c1.7,0,3,1.3,3,3C414.5-372.8,413.2-371.5,411.5-371.5z M387.2-364.8l10.7,10.7c-1.1,0.6-2.6,0.4-3.5-0.5l-8.8-8.8
								c-1.2-1.2-1.2-3.1,0-4.2l11.8-11.8c1.2-1.2,3.5-2.1,5.1-2.1l-15.3,15.3C386.8-365.8,386.8-365.2,387.2-364.8z" />
						</svg>
					</span>
					<div class="guaranteed-content">
						<h3 class="small-caps"><?=Format::htmlspecialchars($heading); ?></h3>
						<h2><?=Format::htmlspecialchars($subheading); ?></h2>
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
</div>