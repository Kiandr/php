<?php namespace BDX; ?>

<h1>States</h1>
<div class="state-results-container bdx-listings-grid">

	<?php if (!empty($results) && is_array($results)) {
		foreach ($results as $result) {
			require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/results/state.tpl');
		}
	}else{
		echo '<div class="error-message">No States Found.</div>';
	} ?>
</div>

<div class="state-pagination-container">
	<?php
		require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/pagination.tpl');
	?>
</div>