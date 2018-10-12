<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->load($result['idx']); ?>

<?php // If content cannot appear over listing images b/c compliance requirements, load this layout ?>
<?php if (isset($_COMPLIANCE['images']['no_overlay']) && $_COMPLIANCE['images']['no_overlay']) { ?>

 	<div class="alt-featured-listing">
	      <div class="alt-featured-listing--img__wrap">
			<a class="alt-featured-listing--url" href="<?=$result['url_details']; ?>">
			<?php if (!empty($result['ListingImage'])) { ?>
				<img class="alt-featured-listing--img" data-src="<?=$result['ListingImage']; ?>" alt="">
			<?php } else { ?>
				<img class="alt-featured-listing--img" data-src="/img/404.gif" alt="">
			<?php } ?>
			</a>
 	    </div>
	    <div class="alt-featured-listing--info__wrap">
 	        <div class="nd-txt BM">
        	   	<?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) { ?>
        	    <div class='mls-compliance'>
                    <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
        		</div>
        		<?php } ?>
	            <span class="nd-tag"><span>FEATURED LISTING</span></span>
	            <span class="nd-price">$<?=Format::number($result['ListingPrice']); ?></span>
	            <span class="title">
	                <?=Format::htmlspecialchars($result['Address']); ?> -
	                <span class="nd-nbh"><?=Format::htmlspecialchars($result['AddressCity']); ?></span>
	            </span>
	            <p><?=Format::truncate($result['ListingRemarks'], 200); ?></p>
	            <?php if( empty($_COMPLIANCE['results']['provider_first']) ) { ?>
	            <div class="mls-compliance">
                    <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
	                <?php if (!empty($_COMPLIANCE['results']['show_mls']) && !empty($result['ListingMLS'])) { ?>
	                    <span class="mls-num"><?=Lang::write('MLS_NUMBER'). ' ' . $result['ListingMLS']; ?></span>
	                <?php } ?>
	            </div>
	            <?php } ?>
	        </div>
	    </div>
 	</div>

<? } else { // Load original layout ?>

<a class="col stk w1/2 w1/1-sm" href="<?=$result['url_details']; ?>">
    <div>
        <div class="img wFill h4/3 fade img--cover">
            <?php if (!empty($result['ListingImage'])) { ?>
                <img data-src="<?=$result['ListingImage']; ?>" alt="">
            <?php } else { ?>
                <img data-src="/img/404.gif" alt="">
            <?php } ?>
        </div>
    </div>
    <div>
	    <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) { ?>
	    <div class='mls-compliance'>
            <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
		</div>
		<?php } ?>
        <span class="nd-price">$<?=Format::number($result['ListingPrice']); ?></span>
        <div class="nd-txt BM">
            <span class="nd-tag"><span>FEATURED LISTING</span></span>
            <span class="title">
                <?=Format::htmlspecialchars($result['Address']); ?> -
                <span class="nd-nbh"><?=Format::htmlspecialchars($result['AddressCity']); ?></span>
            </span>
            <p><?=Format::truncate($result['ListingRemarks'], 200); ?></p>
            <?php if( empty($_COMPLIANCE['results']['provider_first']) ) { ?>
            <div class="mls-compliance">
                <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
                <?php if (!empty($_COMPLIANCE['results']['show_mls']) && !empty($result['ListingMLS'])) { ?>
                    <span class="mls-num"><?=Lang::write('MLS_NUMBER'). ' ' . $result['ListingMLS']; ?></span>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
</a>

<? }  ?>