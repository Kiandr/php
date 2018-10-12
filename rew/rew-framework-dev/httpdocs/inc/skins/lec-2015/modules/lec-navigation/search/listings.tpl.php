<?php $placeholder = 'City, ' . Locale::spell('Neighborhood') . ', ' . Locale::spell('ZIP') . ' or ' . Lang::write('MLS') . ' Number'; ?>
<form action="/idx/" class="input">
	<input type="hidden" name="refine" value="true">
	<input name="search_location" class="autocomplete" placeholder="<?=$placeholder; ?>">
	<button type="submit" class="strong"><span>Search</span></button>
</form>