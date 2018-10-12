<div class="bdx-sidebar_search_form">
	<div class="btnset close-search">
		<a href="#" class="btn"><i class="ico cross"></i></a>
	</div>

	<?php // State placeholder for autocomplete ?>
	<input class="hidden" name="state" value="<?=$app->state;?>">

	<form method="get" action="<?=$searchUrl;?>" id="search-form">

		<div class="btnset">
			<button type="submit" class="btn btn-block btn-primary"><?=($search === 'homes' ? 'Search New Homes' : 'Search Communities');?></button>
		</div>

		<?php foreach ($criteria as $name => $field) { ?>

			<?php $current = !empty($searchData[$name]) ? $searchData[$name] : false; ?>
			<div class="field">

				<?php if (!empty($field['title'])) { ?>
					<label><?=htmlspecialchars($field['title']);?></label>
				<?php } ?>

				<div class="details search-<?=$name;?>">
					<?php $options = $field['options']; ?>
					<?php if (!empty($options)) { ?>
						<?php if($field['multiple']) { ?>
							<?php if (is_array($options)) { ?>
								<?php foreach ($options as $option) { ?>
									<?php $checked = ($current == $option['value'] || is_array($current) && in_array($option['value'], $current)) ? ' checked' : ''; ?>
									<label><input type="checkbox" value="<?=$option['value'];?>" <?=$checked;?> name="search[<?=htmlspecialchars($name);?>][]"> <?=$option['title'];?></label>
								<?php } ?>
							<?php } elseif (is_string($options)) {
									$filter = is_callable($field['filter']) ? $field['filter'] : false;
									$options = $app->db_bdx->query($options);
									while ($option = $options->fetch()) {
										if (!empty($filter)) $option = $filter($option);
										$checked = ($current == $option['value'] || is_array($current) && in_array($option['value'], $current)) ? ' checked' : ''; ?>
										<label><input type="checkbox" value="<?=$option['value'];?>" <?=$checked;?> name="search[<?=htmlspecialchars($name);?>][]"> <?=$option['title'];?></label>
									<?php } ?>
								<?php } ?>
						<?php } else { ?>
							<select name="search[<?=htmlspecialchars($name);?>]">
								<?php $placeholder = $field['placeholder']; ?>
								<?php if (!empty($placeholder) && is_string($placeholder)) { ?>
									<option value=""><?=htmlspecialchars($placeholder);?></option>
								<?php } ?>
								<?php if (is_array($options)) { ?>
									<?php foreach ($options as $option) { ?>
										<option value="<?=htmlspecialchars($option['value']);?>"<?=($current == $option['value'] || is_array($current) && in_array($option['value'], $current)) ? ' selected' : '';?>><?=htmlspecialchars($option['title']);?></option>
									<?php } ?>
								<?php } elseif (is_string($options)) {
									$filter = is_callable($field['filter']) ? $field['filter'] : false;
									$options = $app->db_bdx->query($options);
									while ($option = $options->fetch()) {
										if (!empty($filter)) $option = $filter($option);
										$selected = ($current == $option['value'] || is_array($current) && in_array($option['value'], $current)) ? ' selected' : ''; ?>
										<option value="<?=htmlspecialchars($option['value']);?>"<?=$selected;?>><?=htmlspecialchars($option['title']);?></option>
									<?php } ?>
								<?php } ?>
							</select>
						<?php } ?>
					<?php } else {
						$placeholder = !empty($field['placeholder']) && is_string($field['placeholder']) ? ' placeholder="' . htmlspecialchars($field['placeholder']) . '"' : false; ?>
						<input type="text" <?=(!empty($field['autocomplete']) ? 'class="bdx-autocomplete"' : '');?> name="search[<?=htmlspecialchars($name);?>]" value="<?=is_array($current) ? htmlspecialchars(implode(',', $current)) : htmlspecialchars($current);?>" <?=$placeholder;?>>
					<?php } ?>
				</div>
			</div>
		<?php } ?>

		<div class="btnset">
			<button type="submit" class="btn btn-block btn-primary"><?=($search === 'homes' ? 'Search New Homes' : 'Search Communities');?></button>
		</div>
	</form>
</div>