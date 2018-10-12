<form id="SubdivisionsearchForm" action="<?=$communityUrl;?>" method="get">

    <input type="hidden" name="SubdivisionSearch" value="true">

	<div class="bdx-search-row">
	    <div class="bdx-search-input">
			<label>Find Your Community:</label>
		    <input type="text" name="search[Location]" placeholder="Enter a city, community, zip, or builder name">
	    </div>

		 <div class="bdx-search_price search_price2">
	  		<div class="bdx-slider-header">
		        <label>Price Range</label>
		        <small class="cur-price-range">Any Price</small>
			</div>
	       	<div class="bdx-noUiSlider"></div>
	        <?php if (!empty($priceOptions) && is_array($priceOptions)) { ?>
	            <select name="search[MinPrice]" class="minimum_price2 hidden">
	  				<option value="0">No Minimum</option>
	  				<?php foreach ($priceOptions as $priceOption) { ?>
	  					<option value="<?=$priceOption['value'];?>"><?=$priceOption['title'];?></option>
	  				<?php } ?>
	            </select>
	            <select name="search[MaxPrice]" class="maximum_price2 hidden">
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
	  	 	<div class="bdx-search-field">
		    	<label><input type="checkbox" name="search[NotPreSale]" value="Y">Exclude Pre-sale Subdivisions</label>
		    </div>

	        <div class="bdx-search-field">
		        <label><input type="checkbox" name="search[Pool]" value="Y">Has Pool</label>
	        </div>

	        <div class="bdx-search-field">
		        <label><input type="checkbox" name="search[GolfCourse]" value="Y">Has Golf Course</label>
			</div>
	   	</div>

	    <div class="btnset">
	    	<button type="submit" value="Search">Search</button>
	   	</div>
   	</div>

</form>

