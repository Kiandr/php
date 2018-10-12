<?php $placeholder = Locale::spell('Neighborhood') . ' Name or Lifestyle'; ?>
<form action="/communities.php" class="input">
	<input name="search_keyword" placeholder="<?=$placeholder; ?>">
	<button type="submit" class="strong"><span>Search</span></button>
</form>