<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>
<?php $sidebar = $this->container('sidebar')->countModules(); ?>

<?php if ($this->container('cover-quick-search')->countModules() > 0) { ?>
    <div id="feature" class="cover">
        <div class="module">
            <?=$this->container('cover-quick-search')->loadModules(); ?>
        </div>
    </div>
<?php } else if ($this->container('quick-search')->countModules() > 0) { ?>
    <div class="block wrp S4">
        <div class="w1/1">
            <?=$this->container('quick-search')->loadModules(); ?>
        </div>
    </div>
<?php } ?>

<div id="body"<?=$sidebar ? ' class="block wrp S4"' : ''; ?>>
    <div id="content"<?=$sidebar ? ' class="col w3/4 w1/1-sm" ' : ''; ?>>
        <div class="wrp<?=(isset($_GET['popup']) ? '' : ' S4'); ?>">
            <?=$this->container('content')->loadModules(); ?>
            <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
        </div>
    </div>
    <?php if (!empty($sidebar)) { ?>
        <div id="sidebar"<?=$sidebar ? ' class="col block w1/4 w1/1-sm" ' : ''; ?>>
            <a id="sidebar-toggle" class="btn btn--fill">
                Navigation <i class="fa fa-caret-down R"></i>
            </a>
            <div id="sidebar-nav">
                <?=$this->container('sidebar')->loadModules(); ?>
            </div>
        </div>
    <?php } ?>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>