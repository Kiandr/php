<?php

// Include skin header template (required)
$this->includeFile('tpl/misc/header.tpl.php');

?>

<?php if ($this->container('feature')->countModules() > 0) { ?>
	<div id="feature" style="position: relative;">
		<?=$this->container('feature')->loadModules(); ?>
	</div>
<?php } ?>

<?php if (Settings::getInstance()->SETTINGS['agent'] === 1) { ?>
	<div id="body">
		<div class="wrap">
			<?php

				// Display call to actions
				$thumbnails = '/thumbs/360x242/f';
				$thumbnails_retina = '/thumbs/720x484/f';
				$placeholder = $this->getUrl() . '/img/placeholder.png';
				$callToActions = $this->variable('ctas');
				if (!empty($callToActions) && is_array($callToActions)) {
					$numCols = count($callToActions);
					$classList = array();
					$classList[] = 'colset-' . ($numCols === 4 ? 2 : $numCols) . '-md';
					$classList[] = 'colset-' . $numCols . '-lg';
					$classList[] = 'colset-' . $numCols . '-xl';
					echo '<div class="colset ctas ' . implode(' ', $classList) . '">';
					foreach ($callToActions as $callToAction) {
						$link = $callToAction['link'];
						$image = $callToAction['image'] ?: $placeholder;
						$heading = $callToAction['heading'];
						$content = $callToAction['content'];
						echo '<div class="col media">';
						if (!empty($link)) echo '<a href="' . Format::htmlspecialchars($link) . '">';
						echo '<div class="photo"><img src="' . $thumbnails . $image . '" srcset="' . $thumbnails_retina . $image . ' 2x" alt=""></div>';
						echo '<div class="body">';
						echo '<h3>' . Format::htmlspecialchars($heading) . '</h3>';
						echo '<p>' . nl2br(Format::htmlspecialchars($content)) . '</p>';
						echo '</div>';
						if (!empty($link)) echo '</a>';
						echo '</div>';
					}
					echo '</div>';
				}

				// Display feature blocks
				$thumbnails = '/thumbs/347x76/f';
				$thumbnails_retina = '/thumbs/694x152/f';
				$placeholder = $this->getUrl() . '/img/placeholder.png';
				$featureActions = $this->variable('features');
				if (!empty($featureActions) && is_array($featureActions)) {
					echo '<div class="colset colset-1-sm colset-3-md colset-3-lg colset-3-xl text-center extended-ctas">';
					$featureClasses = array('extended-left', 'extended-center', 'extended-right');
					foreach ($featureActions as $featureAction) {
						$heading = $featureAction['heading'];
						$subtext = $featureAction['subtext'];
						$content = $featureAction['content'];
						$image = $featureAction['image'] ?: $placeholder;
						$link = $featureAction['link'];
						echo '<div class="col ' . array_shift($featureClasses) . '">';
						if (!empty($link)) echo '<a href="' . $link . '">';
						echo '<header>';
						echo '<h6 class="kicker"><span>' . Format::htmlspecialchars($heading) . '</span></h6>';
						echo '<h4>' . Format::htmlspecialchars($subtext) . '</h4>';
						echo '<h5>' . Format::htmlspecialchars($content) . '</h5>';
						echo '</header>';
						echo '<img src="' . $thumbnails . $image . '" srcset="' . $thumbnails_retina . $image . ' 2x" alt="">';
						if (!empty($link)) echo '</a>';
						echo '</div>';
					}
					echo '</div>';
				}

				// Company call to action
				$thumbnails = '/thumbs/560x420/f';
				$thumbnails_retina = '/thumbs/1120x840/f';
				$placeholder = $this->getUrl() . '/img/placeholder.png';
				$company = $this->variable('company');
				if (!empty($company) && is_array($company)) {
					$heading = $company['heading'];
					$subtext = $company['subtext'];
					$content = $company['content'];
					$linkText = $company['linkText'];
					$linkUrl = $company['linkUrl'];
					$image = $company['image'] ?: $placeholder;
					echo '<div class="colset colset-2-lg colset-2-xl">';
					echo '<div class="col">';
					echo '<h1>' . Format::htmlspecialchars($heading) . '</h1>';
					echo '<p class="strong">' . Format::htmlspecialchars($subtext) . '</p>';
					echo '<p>' . nl2br(Format::htmlspecialchars($content)) . '</p>';
					echo '</div>';
					echo '<div class="col">';
					echo '<img class="bordered defer" src="' . $thumbnails . Format::htmlspecialchars($image) . '" srcset="' . $thumbnails_retina . Format::htmlspecialchars($image) . ' 2x" alt="">';
					echo '</div>';
					echo '<div class="buttonset text-center">';
					echo '<a href="' . Format::htmlspecialchars($linkUrl) . '" class="button full-width">' . Format::htmlspecialchars($linkText) . '.</a>';
					echo '</div>';
					echo '</div>';
				}

			?>
		</div>
	</div>
<?php

}

?>
<section class="homepage-content">
	<div class="wrap">
		<?php $this->container('content')->loadModules(); ?>
		<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
	</div>
</section>
<?php

// Include skin footer template (required)
$this->includeFile('tpl/misc/footer.tpl.php');