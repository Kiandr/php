<?php if (empty($similar)) return; ?>
<div id="<?=$this->getUID(); ?>" class="marT-md">
    <h3>Properties for Sale Similar to <?=implode(', ', array_filter(array($listing['Address'], $listing['AddressCity'], $listing['AddressState']))); ?></h3>
    <div class="listings cols">
        <?php foreach ($similar as $result) include $result_tpl; ?>
    </div>
</div>