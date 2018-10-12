<div class="bdx-slideshow">
	<div class="flexslider">
		<ul class="slides">
			<?php if (!empty($listing['Images']) && is_array($listing['Images'])) { ?>
				<?php for ($i = 0;  $i < count($listing['Images']); $i++) { ?>
					<?php $alt = "";
					if($i == 0) { 
						$alt = $listing['Alt'];
					} elseif($i == 1 || $i == 2) { 
						$alt = $listing['Alt' . $i];
					} ?>
					<li><img alt="<?=$alt;?>" data-src="<?=$listing['Images'][$i];?>" src="/builders/res/img/35mm_landscape.gif"></li>
				<?php } ?>
			<?php } else { ?>
				<li><img data-src="/builders/res/img/no-image.gif" src="/builders/res/img/35mm_landscape.gif"></li>
			<?php } ?>
		</ul>
	</div>
</div>
