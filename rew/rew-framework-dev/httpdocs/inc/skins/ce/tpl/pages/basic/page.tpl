<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>
<?php $sidebar = $this->container('sidebar')->countModules(); ?>

<?php if ($this->container('quick-search')->countModules() > 0) { ?>
    <?=$this->container('quick-search')->loadModules(); ?>
<?php } ?>

<div id="body">
    <div class="container">
		<div class="columns">
			<div id="content" class="-pad-vertical-lg <?=$sidebar ? 'column -width-3/4 -width-1/1-sm -width-1/1@sm -width-1/1@xs -width-1/1@md' : 'column -width-1/1'; ?>">
                <?php $this->container('content')->loadModules(); ?>
                <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
			</div>
		    <?php if (!empty($sidebar)) { ?>
                <aside id="sidebar" class="column -pad-top-xs -width-1/4 -width-1/1@sm -width-1/1@md -width-1/1@xs -text-sm">
                    <div class="nav nav--stacked -pad-top">
                        <ul class="nav__list -text-xs">
                            <?=$this->container('sidebar')->loadModules(); ?>
                        </ul>
                    </div>
                </aside>
			<?php } ?>
		</div>
    </div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>