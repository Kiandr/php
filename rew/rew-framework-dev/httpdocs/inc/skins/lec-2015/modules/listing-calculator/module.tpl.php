<div id="dialog-calc" class="hidden">
	<header>
		<h4 class="text-center"><small>Estimate Your</small> Monthly Payment</h4>
		<a data-dialog-close class="action-close">Close</a>
	</header>
	<form id="<?=$this->getUID(); ?>" autocomplete="off">
		<div class="field x12">
			<label>Listing Price</label>
			<input class="text-center" name="price" value="<?=preg_replace('/[^0-9]/', '', $listing_price); ?>">
		</div>
		<div class="field x12">
			<label>Downpayment</label>
			<div class="inputset">
				<input class="text-center" name="downpayment" value="<?=preg_replace('/[^0-9]/', '', $down_payment); ?>">
				<input class="text-center" name="downpercent" value="<?=preg_replace('/[^0-9]/', '', $down_percent); ?>" style="text-align: right">
			</div>
		</div>
		<div class="field x6">
			<label>Interest Rate</label>
			<select name="interest" class="text-center">
				<?php foreach ($interest_rates as $rate) {
					$selected = ($this->config('interest_rate') == $rate) ? ' selected' : '';
					echo '<option value="' . $rate . '"' . $selected . '>' . $rate . '%</option>';
				} ?>
			</select>
		</div>
		<div class="field x6 last">
			<label>Amortization</label>
			<select name="amortization" class="text-center">
				<?php foreach ($mortgage_terms as $term) {
					$selected = ($this->config('mortgage_term') == $term) ? ' selected' : '';
					echo '<option value="' . $term . '"' . $selected . '>' . $term . ' Years</option>';
				} ?>
			</select>
		</div>
	</form>
	<?php rew_snippet('cta-calculator'); ?>
</div>