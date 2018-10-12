<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<div id="body"<?=($this->page->variable('navPosition') == 'left' ? ' class="left-sidebar"' : ''); ?>>
	<div class="wrap">
		<?=$this->container('body')->loadModules(); ?>
		<?=$this->container('pre-content')->loadModules(); ?>
		<div id="content">
			<?=$this->container('content')->loadModules(); ?>
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
		</div>

		<?php if ($this->container('sidebar')->countModules() > 0) { ?>
			<aside id="sidebar">
				<?=$this->container('sidebar')->loadModules(); ?>
			</aside>
		<?php } ?>

	</div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>