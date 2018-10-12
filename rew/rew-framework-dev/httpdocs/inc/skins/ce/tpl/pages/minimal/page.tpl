<?php $this->includeFile('tpl/header.tpl.php'); ?>

<div id="body" class="<?=$this->getPage()->info('class'); ?>">
    <?php $this->container('content')->loadModules(); ?>
</div>

<?php $this->includeFile('tpl/footer.tpl.php'); ?>