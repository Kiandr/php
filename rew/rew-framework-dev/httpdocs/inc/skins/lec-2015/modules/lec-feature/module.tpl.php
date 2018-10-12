<?php $schemeUrl = $this->getContainer()->getPage()->getSkin()->getSchemeUrl(); ?>
<div id="<?=$this->getUID(); ?>" class="wrap">
	<div class="search">
		<?php if (!empty($sell_tab)) { ?>
			<ul>
				<li class="current"><a data-target="#find-a-home">Find a Home</a></li>
				<li><a data-target="#sell-my-home">Sell my Home</a></li>
			</ul>
		<?php } ?>
		<div class="search-body">
			<?php

				// IDX feed switcher
				$this->getPage()->addContainer('idx-feeds')->addModule('idx-feeds', array(
					'template' => 'lec-feature.tpl.php'
				))->display();

			?>
			<form id="find-a-home" action="/idx/" method="get">
				<input type="hidden" name="feed" value="<?=Settings::getInstance()->IDX_FEED; ?>">
				<span class="input">
					<input name="search_location" class="x12 autocomplete location" placeholder="<?=Format::htmlspecialchars($search_placeholder); ?>">
					<button type="submit" class="strong"><i class="icon-searchGlass"></i></button>
				</span>
			</form>
			<?php if (!empty($sell_tab)) { ?>
				<form id="sell-my-home" action="/cma.php" class="hidden">
					<input class="hidden" name="email" value="" autocomplete="off">
					<input type="hidden" name="feed" value="<?=Settings::getInstance()->IDX_FEED; ?>">
					<span class="input">
						<input name="adr" placeholder="<?=Format::htmlspecialchars($address_placeholder); ?>" value="<?=Format::htmlspecialchars($address_value); ?>" required>
						<input type="email" name="mi0moecs" value="<?=Format::htmlspecialchars($email_value); ?>" placeholder="<?=Format::htmlspecialchars($email_placeholder); ?>" required>
						<button type="submit" class="strong"><i class="icon-searchGlass"></i></button>
					</span>
				</form>
			<?php } ?>
		</div>
	</div>
</div>