<?php if (empty($authorized)) { ?>

    <?=errorMsg('You do not have permission to access this page.', '<img src="/backend/img/ills/security.png" width=200/> Authorization Error'); ?>

<?php } else { ?>

	<form action="?submit" method="post" class="rew_check">

		<input type="hidden" name="state" value="<?=$state;?>">

        <section>

        	<header>
                <h1>BDX State Settings</h1>
                <div class="app_actions">
                	<a class="btn" href="../">Return to BDX Settings</a>
                    <button class="btn btn--positive" type="submit">Save</button>
                </div>
            </header>

			<section class="col w8 p1">
				<fieldset class="grid">

					<div class="select_all_toggle toggle_off">Select All</div>

					<?php if (!empty($cities) && is_array($cities)) { ?>
							<?php foreach ($cities as $city) { ?>
								<label><input type="checkbox" class="city toggleable" name="cities[]" value="<?=$city;?>" <?=is_array($settings['states'][$state]['cities']) && in_array($city, $settings['states'][$state]['cities']) ? 'checked="checked"' : '';?>> <?=$city; ?></label>
							<?php } ?>
						<?php } ?>
				</fieldset>
			</section>

   		</section>
	</form>

<?php } ?>