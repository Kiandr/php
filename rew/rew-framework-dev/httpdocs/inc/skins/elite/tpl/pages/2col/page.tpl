<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<?php if (!isset($_GET['popup'])) { ?>
    <?php if ($this->container('sub-feature')->countModules() > 0) { ?>
        <div id="sub-feature">
            <?=$this->container('sub-feature')->loadModules(); ?>
        </div>
    <?php } ?>
<?php } ?>

<div id="body">
    <div class="uk-container uk-container-center uk-margin-large-top uk-margin-large-bottom">
        <?= $this->container('pre-content')->loadModules(); ?>
        <div class="uk-grid">
            <div class="uk-width-xsmall-1-1 uk-width-large-3-4<?= $this->page->variable('navPosition') == 'left' ? ' uk-push-1-4' : ''; ?> uk-margin-large-bottom">
                <?= $this->container('content')->loadModules(); ?>
                <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
            </div>
        <?php if ($this->container('sidebar')->countModules() > 0) { ?>
            <div class="uk-width-xsmall-1-1 uk-width-large-1-4<?= $this->page->variable('navPosition') == 'left' ? ' uk-pull-3-4' : ''; ?>">
                <aside class="sidebar">
                    <?= $this->container('sidebar')->loadModules(); ?>
                </aside>
            </div>
        <?php } ?>
        </div>
    </div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>
