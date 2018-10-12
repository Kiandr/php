<?php namespace BDX; ?>

<?php require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/breadcrumbs.tpl'); ?>

<h1><?=$title;?></h1>

<div class="bdx-tabcontain">
	<ul>
		<li id="subdivision-search" class="selected"><a href="#">Community Search</a></li>
		<li id="homeplan-search"><a href="#">Home Plan Search</a></li>
	</ul>
</div>

<div class="search-wrapper">
	<div class="subdivision-search-container">
		<?php
			require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/forms/subdivision-search.tpl');
		?>
	</div>

	<div class="homeplan-search-container hidden">
		<?php
			require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/forms/homeplan-search.tpl');
		?>
	</div>
</div>

<div class="city-results-container bdx-listings-grid">
	<?php if (!empty($results) && is_array($results)) {
		foreach ($results as $result) {
			require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/results/city.tpl');
		}
	}else{
		echo '<div class="error-message">No Cities Found.</div>';
	} ?>
</div>

<div class="city-pagination-container">
	<?php
		require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/pagination.tpl');
	?>
</div>