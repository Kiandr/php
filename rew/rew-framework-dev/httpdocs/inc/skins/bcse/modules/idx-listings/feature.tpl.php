<?php if (!empty($results)) { ?>
	<h2><?=Format::htmlspecialchars($this->config('heading')); ?></h2>
	<h3 class="small-caps"><?=Format::htmlspecialchars($this->config('subheading')); ?></h3>
	<?php if ($this->config('linkUrl') && $this->config('linkText')) { ?>
		<a class="buttonstyle absolute-right view-communities-btn small-caps" href="<?=$this->config('linkUrl'); ?>">
			<?=Format::htmlspecialchars($this->config('linkText')); ?>
		</a>
	<?php } ?>
	<div class="articleset">
		<?php foreach ($results as $result) { ?>
			<article>
				<a href="<?=$result['url_details']; ?>">
					<img src="<?=$placeholder; ?>" data-src="<?=$result['ListingImage']; ?>" alt="<?=Format::htmlspecialchars($result['Address']); ?>" />
					<div class="fl-content">
						<div class="wrapper">
							<span class="fl-price">$<?=Format::number($result['ListingPrice']); ?></span>
							<span class="fl-address"><?=Format::htmlspecialchars($result['Address']); ?></span>
						</div>
					</div>
				</a>
				<ul class="fl-statistics">
					<?php if ($result['NumberOfBedrooms'] > 0) { ?>
						<li><?=Format::htmlspecialchars($result['NumberOfBedrooms']); ?> Beds</li>
					<?php } ?>
					<?php if ($result['NumberOfBathrooms'] > 0) { ?>
						<li><?=Format::htmlspecialchars($result['NumberOfBathrooms']); ?> Baths</li>
					<?php } ?>
					<?php if ($result['NumberOfSqFt'] > 0) { ?>
						<li><?=Format::number($result['NumberOfSqFt']); ?> SF</li>
					<?php } ?>
				</ul>
			</article>
		<?php } ?>
	</div>
<?php } ?>