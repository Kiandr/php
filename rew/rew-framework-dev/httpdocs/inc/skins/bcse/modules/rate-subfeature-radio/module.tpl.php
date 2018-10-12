<h2><?=Format::htmlspecialchars($this->config('heading')); ?></h2>
<h3 class="small-caps"><?=Format::htmlspecialchars($this->config('subheading')); ?></h3>
<?php if ($this->config('linkUrl') && $this->config('linkText')) { ?>
	<a class="buttonstyle absolute-right view-communities-btn small-caps" href="<?=$this->config('linkUrl'); ?>">
		<?=Format::htmlspecialchars($this->config('linkText')); ?>
	</a>
<?php } ?>
<div class="articleset">
	<?php if (!empty($radio)) { ?>
		<?php foreach ($radio as $ad) { ?>
			<article>

				<div class="radio-image">
					<?php if (!empty($ad['image'])) { ?>
						<img src="/uploads/<?=$ad['image']; ?>" alt="">
					<?php } else { ?>
						<svg width="28" height="49" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 283.8 504" enable-background="new 0 0 283.8 504" xml:space="preserve">
							<g>
								<path d="M252.3,220.2v47.3c0,61-49.4,110.4-110.4,110.4c-61,0-110.4-49.4-110.4-110.4v-47.3H0v47.3
								c0,73,55.2,133.2,126.2,141v64H63.1V504h63.1h31.5h63.1v-31.5h-63.1v-64c71-7.8,126.2-68,126.2-141v-47.3H252.3z" />
								<path d="M178.4,88.5h42.4V78.3c0-25.9-12.5-48.9-31.8-63.3v35c0,5.3-4.4,9.7-9.7,9.7s-9.7-4.4-9.7-9.7V4.4
								c-5.6-2.1-11.5-3.6-17.6-4.3V50c0,5.3-4.4,9.7-9.7,9.7s-9.7-4.4-9.7-9.7V0c-6.3,0.8-12.4,2.2-18.2,4.4V50c0,5.3-4.4,9.7-9.7,9.7
								s-9.7-4.4-9.7-9.7V15C75.6,29.4,63.1,52.4,63.1,78.3v10.2h44.3c5.3,0,9.7,4.4,9.7,9.7s-4.4,9.7-9.7,9.7H63.1v17.6h44.3
								c5.3,0,9.7,4.4,9.7,9.7s-4.4,9.7-9.7,9.7H63.1v18.2h44.3c5.3,0,9.7,4.4,9.7,9.7s-4.4,9.7-9.7,9.7H63.1v16.8h44.3
								c5.3,0,9.7,4.4,9.7,9.7v0c0,5.3-4.4,9.7-9.7,9.7H63.1v48.8c0,43.5,35.3,78.8,78.8,78.8c43.5,0,78.8-35.3,78.8-78.8v-48.8h-42.4
								c-5.3,0-9.7-4.4-9.7-9.7v0c0-5.3,4.4-9.7,9.7-9.7h42.4v-16.8h-42.4c-5.3,0-9.7-4.4-9.7-9.7s4.4-9.7,9.7-9.7h42.4v-18.2h-42.4
								c-5.3,0-9.7-4.4-9.7-9.7s4.4-9.7,9.7-9.7h42.4v-17.6h-42.4c-5.3,0-9.7-4.4-9.7-9.7S173,88.5,178.4,88.5z" />
							</g>
						</svg>
					<?php } ?>
				</div>
				<div class="radio-title"><?=Format::htmlspecialchars($ad['title'] ?: '&nbsp;'); ?></div>
				<?php if (!empty($ad['audio'])) { ?>
					<audio src="/uploads/<?=rawurlencode($ad['audio']); ?>" preload="none"></audio>
				<?php } ?>
			</article>
		<?php } ?>
	<?php } ?>
</div>