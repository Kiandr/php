<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<div id="body">
	<div id="content">
		<div class="wrp<?=(isset($_GET['popup']) ? '' : ' S4'); ?>">
			<?=$this->container('content')->loadModules(); ?>
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
		</div>
	</div>

</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>