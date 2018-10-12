<?php if (empty($office)) { ?>

    <div class="notice notice--negative">
        <p>We're sorry, but the office you were looking for could not be found.</p>
    </div>

<?php } else { ?>

	<div class="office-details">
		<div class="columns -pad-bottom-lg">
			<div class="column -width-1/2 -width-1/2@lg -width-1/1@md -width-1/1@sm -width-1/1@xs">
				<div class="hero hero--landscape hero--portrait@sm hero--portrait@xs">
					<div class="hero__fg">
						<div class="hero__head">
							<div class="divider">
								<span class="divider__label -left -text-upper -text-xs"><?=Format::htmlspecialchars($office['city']); ?>, <?=Format::htmlspecialchars($office['state']); ?></span>
							</div>
						</div>
						<div class="hero__body -flex">
    						<div class="-bottom">
    							<h2 class="-text-upper -mar-0 -text-bold"><?=Format::htmlspecialchars($office['title']); ?></h2>
    							<?php if (!empty($office['location'])) { ?>
    							<p class="-mar-vertical-0"><?=Format::htmlspecialchars($office['location']); ?></p>
    							<?php } ?>
    						</div>
						</div>
					</div>
					<div class="hero__bg">
						<div class="cloak cloak--dusk"></div>
						<img class="hero__bg-content" data-src="<?=$office['image']; ?>" src="/img/util/35mm_landscape.gif" alt="Photo of <?=Format::htmlspecialchars($office['title']); ?> office">
					</div>
				</div>
			</div>
			<div class="column -width-1/2 -width-1/2@lg -width-1/1@md -width-1/1@sm -width-1/1@xs -pad@lg -pad@xl">
    			<h1><?=Format::htmlspecialchars($office['title']); ?></h1>
				<?php if (!empty($office['description'])) { ?>
					<p class="description -mar-bottom">
						<?=$office['description']; ?>
					</p>
				<?php } ?>
                <div class="keyvals">
					<?php if (!empty($office['phone'])) { ?>
					<div class="keyval">
						<span class="keyval__key">Phone:</span>
						<span class="keyval__val"><?=Format::htmlspecialchars($office['phone']); ?></span>
					</div>
					<?php } ?>
					<?php if (!empty($office['fax'])) { ?>
					<div class="keyval">
						<span class="keyval__key">Fax:</span>
						<span class="keyval__val"><?=Format::htmlspecialchars($office['fax']); ?></span>
					</div>
					<?php } ?>
					<?php if (!empty($office['email'])) { ?>
					<div class="keyval">
						<span class="keyval__key">Email:</span>
						<span class="keyval__val"><a href="mailto:<?=$office['email']; ?>"><?=$office['email']; ?></a></span>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		

    <?php if (!empty($office['agents'])) { ?>
        <div class="office__agents -mar-top">
			<div class="divider -pad-vertical">
				<span class="divider__label -left -text-upper -text-xs">Agents in this Office</span>
				<a class="divider__label -right -text-upper -text-xs" href="/agents.php">See All&hellip;</a>
			</div>

			<div class="columns">
            <?php foreach ($office['agents'] as $agent) { ?>
                <?php if(!empty($agent['link'])) { echo '<a href="/agents/' . $agent['link'] . '/" '; } else { echo '<div '; } ?>
                class="hero hero--portrait column -width-1/4 -width-1/2@md -width-1/1@sm">
                    <div class="hero__fg">
						<?php if (!empty($agent['title'])) { ?>
						<div class="hero__head">
							<div class="divider">
								<span class="divider__label -left -text-upper -text-xs"><?=Format::htmlspecialchars($agent['title']); ?></span>
							</div>
						</div>
						<?php } ?>
						<div class="hero__body -flex">
							<div class="-bottom">
							    <h2 class="-text-upper -mar-bottom-xs"><?=Format::htmlspecialchars($agent['name']); ?></h2>
                                <?php if($agent['remarks']) { ?>
                                <p class="-text-xs -text-upper"><?=Format::stripTags(Format::truncate($agent['remarks'],80)); ?></p>
                                <?php } ?>
							</div>
						</div>
					</div>
					<div class="hero__bg">
						<div class="cloak cloak--dusk"></div>
						<img class="agent__img hero__bg-content" data-src="<?=str_replace('275x275','428x428',$agent['image']); ?>" src="/img/util/35mm_landscape.gif" alt="<?=Format::htmlspecialchars($agent['name']); ?>">
					</div>
				<?php if(!empty($agent['link'])) { echo '</a>'; } else { echo '</div>'; } ?>
            <?php } ?>
        	</div>
		</div>
    <?php } ?>
	</div>
<?php } ?>
