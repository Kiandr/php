<?php

global $_COMPLIANCE;

// Listing details
$listing = $this->page->info('listing');

// Get skin's settings
$settings = $this->getSettings();

// Disable CTA if agent spotlight is disabled
if (empty(Settings::getInstance()->MODULES['REW_AGENT_SPOTLIGHT']))  {
	$settings['agent_id'] = false;
}

// Include site's header
$this->includeFile('tpl/misc/header.tpl.php');

$details = array();
if ($listing['NumberOfBedrooms'] > 0) {
	$details[] = '<b>' . $listing['NumberOfBedrooms'] . '</b> ' . Format::plural($listing['NumberOfBedrooms'], 'Beds', 'Bed');
}
if ($listing['NumberOfBathrooms'] > 0) {
	$details[] = '<b>' . Format::fraction($listing['NumberOfBathrooms']) . '</b> ' . Format::plural($listing['NumberOfBathrooms'], 'Baths', 'Bath');
}
if ($listing['NumberOfSqFt'] > 0) {
	$details[] = '<b>' . Format::number($listing['NumberOfSqFt']) . '</b> Sqft';
}
if ($listing['NumberOfAcres'] > 0) {
	$details[] = '<b>' . Format::fraction($listing['NumberOfAcres']) . '</b> Acres';
}
if ($listing['ListingDOM'] > 0) {
	$details[] = '<b>' . Format::fraction($listing['ListingDOM']) . '</b> DOM';
}

// Show up to 4 blocks
$details = array_slice($details, 0, 4);
if (count($details) < 4) {
	array_unshift($details, '<b>$' . Format::shortNumber($listing['ListingPrice']) . '</b> Price');
}

// Back URL
$url_back = (User_Session::get()->url_back() ? Settings::getInstance()->SETTINGS['URL_RAW'] . User_Session::get()->url_back() : Settings::getInstance()->SETTINGS['URL_IDX']);

?>
<?php if (!isset($_GET['popup'])) { ?>
	<div id="sticky-details"<?=empty($settings['agent_id']) ? ' class="no-agent"' : ''; ?>>

		<?php if (!empty($listing)) { ?>
			<div class="sticky-details-wrap">
				<?php

					/**
					 * Agent CTA
					 * This checks the 'bcse.agent' setting which stores an array containing following keys:
					 *  'display' (mixed)
					 *    - If "RAND", random agent will be selected and displayed (This is the default behavior)
					 *    - If FALSE, do not display this CTA
					 *    - If (int), select agent by id
					 *  'phone' (bool)
					 *    - If FALSE, do not display office phone number
					 *    - If TRUE, display agent's office phone number (This is default)
					 *  'cell' (bool)
					 *    - If FALSE, do not display office phone number
					 *    - If TRUE, display agent's office phone number (This is default)
					 */

					// Agent CTA
					if (!empty($settings['agent_id'])) {
						$this->container('listing-agent')->module('listing-agent', array(
							'listing'	=> $listing,
							'agent'		=> $settings['agent_id'],
							'phone'		=> !empty($settings['agent_phone']),
							'cell'		=> !empty($settings['agent_cell'])
						))->display();
					}

				?>
				<div class="top-menu">
					<div class="wrap">
						<div class="wrap-inner">
							<h4>
                                <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($listing)) { ?>
                                    <?= \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($listing); ?>
                                <?php } ?>	
								<?=implode(', ', array_filter(array($listing['Address'], $listing['AddressCity'], $listing['AddressState']))); ?>
								&#8211; <b>$<?=Format::number($listing['ListingPrice']); ?></b>
							</h4>
						</div>
					</div>
					<div class="middle-stats middle-stats-mobile">
						<div class="wrap">
							<div class="wrap-inner">
								<ul class="mediaBodyStats">
									<li><?=implode('</li><li>', $details); ?></li>
									<?php /*<li class="compliance">
										<?=$listing['ListingAgent']; ?>
										<br><?=$listing['ListingOffice']; ?>
									</li>*/ ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="middle-stats middle-stats-main">
					<div class="wrap">
						<div class="wrap-inner">
							<ul class="mediaBodyStats">
								<li><?=implode('</li><li>', $details); ?></li>
								<?php /*<li class="compliance">
									<?=$listing['ListingAgent']; ?>
									<br><?=$listing['ListingOffice']; ?>
								</li>*/?>
							</ul>
						</div>
					</div>
				</div>
				<div class="bottom-options">
					<div class="wrap">
						<div class="wrap-inner">
							<ul>
								<li class="details-search"><a class="icon-search search-toggle"></a></li>
								<li><a rel="nofollow" href="<?=$url_back ?>"><i class="icon-chevron-left"></i> <span class="hidden-tablet">Back</span></a></li>
								<li class="print-details"><a rel="nofollow" target="_blank" href="<?=$listing['url_brochure']; ?>"><i class="icon-print"></i> <span class="hidden-tablet">Print</span></a></li>
								<li><a rel="nofollow" href="<?=$listing['url_sendtofriend']; ?>" class="popup"><i class="icon-share"></i> <span>Share</span></a></li>
								<li><a id="action-favorite"<?=!empty($listing['saved']) ? ' class="saved"' : ''; ?>" data-save='<?=json_encode(array(
									'feed'		=> !empty($listing['idx']) ? $listing['idx'] : Settings::getInstance()->IDX_FEED,
									'mls'		=> $listing['ListingMLS'],
									'remove'	=> 'Remove <span class="hidden-tablet">' . Locale::spell('Favorite') . '</span>',
									'add'		=>'<span class="hidden-tablet">Add</span> ' . Locale::spell('Favorite')
								)); ?>'><i class="<?=!empty($listing['saved']) ? 'icon-star' : 'icon-star-empty'; ?>"></i> <span><?=!empty($listing['saved']) ? 'Remove <span class="hidden-tablet">' . Locale::spell('Favorite') .'</span>' : ' <span class="hidden-tablet">Add </span> ' . Locale::spell('Favorite'); ?></span></a></li>
								<li><a rel="nofollow" href="<?=$listing['url_inquire']; ?>?inquire_type=Property+Showing" class="popup"><i class="icon-comment"></i> <span>Request Showing</span></a></li>
								<?php if (!empty($listing['Latitude']) && !empty($listing['Longitude'])) { ?>
									<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
										<li>
											<a rel="nofollow" href="<?=$listing['url_map']; ?>" class="popup">
												<i class="icon-map-marker"></i>
												<span><?=(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW'] || Settings::getInstance()->MODULES['REW_IDX_BIRDSEYE'] || Settings::getInstance()->MODULES['REW_IDX_ONBOARD']) ? 'Map <span class="hidden-medium">&amp; More</span>': 'Map'; ?></span>
											</a>
										</li>
									<?php } ?>
								<?php } elseif (!empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD']) && !empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
									<li><a rel="nofollow" href="<?=$listing['url_onboard']; ?>" class="popup"><i class="icon-map-marker"></i> <span>Get Local</span></a></li>
								<?php } ?>
								<?php if (!empty($listing['AddressState'])) { ?>
									<?php if (!empty(Settings::getInstance()->MODULES['REW_RT'])) { ?>
										<?php $idx = Util_IDX::getIdx(); ?>
										<?php if ($rt_link = \RealtyTrac\Integration::get_rt_from_mls($idx->getName(), $listing['ListingMLS'], $listing['AddressState'])) { ?>
											<li><a rel="nofollow" href="<?=$rt_link; ?>"><i class="icon-home"></i> <span>View Public Record</span></a></li>
										<?php } ?>
									<?php } ?>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if ($this->container('sub-feature')->countModules() > 0) { ?>
			<div id="sub-feature">
				<?=$this->container('sub-feature')->loadModules(); ?>
			</div>
		<?php } ?>
	</div>

<?php } ?>

<div id="body">
	<div class="wrap">
		<?=$this->container('content')->loadModules(); ?>
		<?php if (empty($_COMPLIANCE['details']['show_below_remarks'])) {
			// Show MLS Office / Agent
			\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
		} ?>
		<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
	</div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>
