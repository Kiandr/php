<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<div id="body">
	<?php if ($this->page->info('name') === 'details') { ?>
		<?=$this->container('content')->loadModules(); ?>
		<div class="wrap">
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
		</div>
	<?php } else { ?>
		<div class="wrap">
			<?=$this->container('content')->loadModules(); ?>
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
		</div>
	<?php } ?>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>