<form id="HomePlansearchForm" action="<?=$homeUrl;?>" method="get">

	<input type="hidden" name="search[ListingType]" value="P">

	<div class="bdx-search-row">

		<div class="bdx-search-input">
	    	<label>Or, find your ideal Home Plan:</label>
	    	<input type="text" name="search[Location]" placeholder="Enter a city, community, zip, or builder name">
	  	</div>

	  	<div class="bdx-search_price search_price1">

	  		<div class="bdx-slider-header">
		        <label>Price Range</label>
				<small class="cur-price-range">Any Price</small>
			</div>

	       	<div class="bdx-noUiSlider"></div>
	        <?php if (!empty($priceOptions) && is_array($priceOptions)) { ?>
	            <select name="search[MinPrice]" class="minimum_price1 hidden">
	  				<option value="0">No Minimum</option>
	  				<?php foreach ($priceOptions as $priceOption) { ?>
	  					<option value="<?=$priceOption['value'];?>"><?=$priceOption['title'];?></option>
	  				<?php } ?>
	            </select>
	            <select name="search[MaxPrice]" class="maximum_price1 hidden">
	            	<option value="0">No Minimum</option>
	            	<?php foreach ($priceOptions as $priceOption) { ?>
	  					<option value="<?=$priceOption['value'];?>"><?=$priceOption['title'];?></option>
	  				<?php } ?>
	            </select>
	        <?php } ?>

	  	</div>
	</div>


	<div class="bdx-search-row">
		<div class="bdx-search-options">
			<?php if (!empty($criteria['MinBeds']['options']) && is_array($criteria['MinBeds']['options'])) { ?>
				<div class="bdx-search-field">
					<label>Bedrooms:</label>
					<select name="search[MinBeds]">
						<?php foreach ($criteria['MinBeds']['options'] as $option) { ?>
							<option value="<?=$option['value'];?>"><?=$option['title'];?></option>
						<?php } ?>
					</select>
				</div>
			<?php } ?>

			<?php if (!empty($criteria['MinBaths']['options']) && is_array($criteria['MinBaths']['options'])) { ?>
				<div class="bdx-search-field">
					<label>Bathrooms:</label>
					<select name="search[MinBaths]">
					<?php foreach ($criteria['MinBaths']['options'] as $option) { ?>
						<option value="<?=$option['value'];?>"><?=$option['title'];?></option>
					<?php } ?>
					</select>
				</div>
			<?php } ?>

			<div class="bdx-search-field">
				<label><input type="checkbox" name="search[ListingType]" value="S">Exclude pre-construction homes</label>
			</div>
			<div class="bdx-search-field">
				<label><input type="checkbox" name="search[PlanType]" value="SingleFamily">Exclude multi-family homes</label>
			</div>
		</div>

	    <div class="btnset">
	    	<button type="submit" name="search_submit" value="Search">Search</button>
	   	</div>

	</div>


</form>
