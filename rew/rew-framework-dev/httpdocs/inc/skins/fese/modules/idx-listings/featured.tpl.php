<?php if (!empty($results)) {

	// If content cannot appear over listing images b/c compliance requirements, load this layout
	if (isset($_COMPLIANCE['images']['no_overlay']) && $_COMPLIANCE['images']['no_overlay']) { ?>
		<div class="idx-featured-listing__container">
			<div class="idx-featured-listing__wrap idx-featured-listing__<?=count($results); ?>">
				<?php foreach ($results as $result) { ?>
					<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->load($result['idx']); ?>
					<div class="idx-featured-listing <?php if ((count($results)) <= 3) { ?>restrict-width<?php } ?>">
						<a href="<?=$result['url_details']; ?>" class="idx-featured-listing--url">
						    <div class="idx-featured-listing--img-wrap">
    							<div class="idx-featured-listing--img-inner-wrap">
    						        <img class="idx-featured-listing--img" src="<?=$placeholder; ?>" data-src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_MEDIUM); ?>" alt="<?=Format::htmlspecialchars($result['Address']); ?>">
    							</div>
    					    </div>
    					    <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) { ?>
    					    <div class='compliance'>
                                <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
            				</div>
            				<?php } ?>
    					    <div class="idx-featured-listing__info txtC">
    					        <h4><?=Format::htmlspecialchars($result['AddressSubdivision']); ?></h4>
    					        <p><?= '$' . Format::number($result['ListingPrice']); ?></p>
    					    </div>
    					    <?php if( empty($_COMPLIANCE['results']['provider_first']) ) { ?>
    					    <div class='compliance'>
                                <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
            				</div>
            				<?php } ?>
						</a>
					</div>
				<? } ?>
			</div>
		</div>

	<? } else {

		// Load original layout ?>
		<div class="cols">
			<?php foreach ($results as $result) { ?>
				<a href="<?=$result['url_details']; ?>" class="col stk w1/6 w1/4-md w1/2-sm h1/1">
				    <div class="img wFill h4/3 fade img--cover">
				        <img src="<?=$placeholder; ?>" data-src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_SMALL); ?>" alt="<?=Format::htmlspecialchars($result['Address']); ?>">
				    </div>
				    <div class="cpt">
					    <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) { ?>
					    <div class='compliance'>
                            <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
        				</div>
        				<?php } ?>
				        <div class="BM txtC pad">
				            <h4><?=Format::htmlspecialchars($result['AddressSubdivision']); ?></h4>
				            <p>$<?=Format::number($result['ListingPrice']); ?></p>
				        </div>
					    <?php if( empty($_COMPLIANCE['results']['provider_first']) ) { ?>
					    <div class='compliance'>
                            <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
        				</div>
        				<?php } ?>
				    </div>
				</a>
			<? } ?>
		</div>
	<? } ?>

<?php } ?>
