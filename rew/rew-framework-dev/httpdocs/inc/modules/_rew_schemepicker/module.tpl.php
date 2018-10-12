<form action="?" method="get">
	<select name="skin-scheme" onchange="this.form.submit();">
	    <?php foreach ($skins as $skin) { ?>
	        <optgroup label="<?=$skin['title']; ?>">
	            <?php foreach ($skin['schemes'] as $scheme) { ?>
	                <?php $selected = (Settings::getInstance()->SKIN == $skin['value'] && Settings::getInstance()->SKIN_SCHEME == $scheme['value']) ? ' selected' : ''; ?>
	                <option value="<?=$skin['value']; ?>/<?=$scheme['value']; ?>"<?=$selected; ?>><?=($show_all_skins ? $skin['title'] . ' - ' : '') . $scheme['title']; ?></option>
	            <?php } ?>
	        </optgroup>
	    <?php } ?>
	</select>
</form>