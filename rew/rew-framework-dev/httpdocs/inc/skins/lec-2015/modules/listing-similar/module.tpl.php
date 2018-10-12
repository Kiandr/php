<?php

// Skip if no listings found
if (empty($similar)) return;

 ?>
<div id="<?=$this->getUID(); ?>" class="wrap">
	<h3>Properties for Sale Similar to <?=implode(', ', array_filter(array($listing['Address'], $listing['AddressCity'], $listing['AddressState']))); ?></h3>
	<div class="colset listings colset-1-sm colset-2-md colset-3-lg colset-3-xl">
		<?php foreach ($similar as $result) include $result_tpl; ?>
	</div>
</div>