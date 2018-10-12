<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>
<?php $content = $this->container('content')->loadModules(false); ?>
<?php $community = $this->container('community')->loadModules(false); ?>
<?=$this->container('hero')->loadModules(); ?>

<?php if (!empty($community) || !empty($content)) { ?>
	<div id="body">
		<div class="wrap">
			<?=$this->container('pre-content')->loadModules(); ?>
			<div id="content">
				<?php if (empty($community)) { ?>
					<?=$content; ?>
				<?php } else { ?>
					<div class="colset colset-1-md colset-2-lg colset-2-xl">
						<?php if (!empty($content)) { ?>
							<div class="col width-1/2-xl width-1/2-lg">
								<?=$content; ?>
							</div>
						<?php } ?>
						<?php if (!empty($community)) { ?>
							<div id="community-stats" class="col width-1-sm width-1-md width-1/2-xl width-1/2-lg" style="background: #eee; padding: 30px">
								<?=$community; ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
		</div>
	</div>
<?php } ?>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>