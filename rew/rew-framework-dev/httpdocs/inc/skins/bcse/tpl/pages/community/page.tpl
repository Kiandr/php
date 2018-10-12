<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<div id="body">
	<div id="content">
		<?php if ((empty($_REQUEST['p']) || ($_REQUEST['p'] < 2)) && !preg_match('/((?:\d+|under|over)-\d+)/', $_REQUEST['price_range'])) { ?>
			<?=$this->container('community')->loadModules(); ?>
		<?php } else { ?>
			<div class="community"></div>
		<?php } ?>
		<?=$this->container('sub-feature')->loadModules(); ?>
		<div class="wrap">
			<?=$this->container('content')->loadModules(); ?>
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
		</div>
	</div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>