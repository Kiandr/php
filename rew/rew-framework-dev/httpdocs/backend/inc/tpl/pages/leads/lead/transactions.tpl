<?php

// Display lead header
if (!empty($show_form)) {
    echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
        'title' => $edit ? 'Lead Transaction (Edit)' : 'Lead Transaction (Add)',
        'back' => sprintf('?id=%s', $lead['id']),
        'lead' => $lead,
        'leadAuth' => $leadAuth
    ]);

} else {
    echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
        'title' => 'Lead Transactions',
        'actions' => array_filter([
            ($can_create_transactions ? [
                'href' => sprintf('?id=%s&add', $lead['id']),
                'icon' => 'add'
            ] : NULL)
        ]),
        'lead' => $lead,
        'leadAuth' => $leadAuth
    ]);

}

?>
<?php if (!empty($show_form)) : ?>
    <form action="?submit" method="post" class="rew_check">
        <input type="hidden" name="id" value="<?=$lead['id']; ?>">

        <?php if (!empty($edit)) { ?>
            <input type="hidden" name="edit" value="<?=$edit['id']; ?>">
        <?php } else { ?>
            <input type="hidden" name="add" value="true">
        <?php } ?>

        <div class="btns btns--stickyB">
            <span class="R">
                <button type="submit" class="btn btn--positive">
                    <svg class="icon icon-check mar0">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use>
                    </svg> Save
                </button>
            </span>
        </div>

        <div class="block">
        <div class="field">
            <label class="field__label">Type of Transaction <em class="required">*</em></label>
            <select class="w1/1" name="type" required>
                <option value="">Choose Type...</option>
                <option value="Buy"<?=($_POST['type'] == 'Buy') ? ' selected' : ''; ?>>Buy</option>
                <option value="Sell"<?=($_POST['type'] == 'Sell') ? ' selected' : ''; ?>>Sell</option>
            </select>
        </div>

        <div class="field">
            <label class="field__label">List Price <em class="required">*</em></label>
            <input class="w1/1" data-currency name="list_price" value="<?=preg_replace('/[^0-9]/', '', $_POST['list_price']); ?>" required>
        </div>

        <div class="field">
            <label class="field__label">Sold Price <em class="required">*</em></label>
            <input class="w1/1" data-currency name="sold_price" value="<?=preg_replace('/[^0-9]/', '', $_POST['sold_price']); ?>" required>
        </div>

        <div class="field">
            <label class="field__label">MLS&reg; Number</label>
            <input class="w1/1" type="text" name="mls_number" value="<?=htmlspecialchars($_POST['mls_number']); ?>" required>
        </div>

        <div class="field">
            <label class="field__label">Transaction Details <em class="required">*</em></label>
            <textarea class="w1/1" name="details" placeholder="Transaction Details..." required><?=htmlspecialchars($_POST['details']); ?></textarea>
        </div>
        </div>
    </form>
<?php else : ?>


	<?php if (!empty($show_form)) : ?>
	<form action="?submit" method="post" class="rew_check">
		<input type="hidden" name="id" value="<?=$lead['id']; ?>">
		<?php if (!empty($edit)) : ?>
		    <input type="hidden" name="edit" value="<?=$edit['id']; ?>">
		<?php else : ?>
		    <input type="hidden" name="add" value="true">
		<?php endif; ?>

        <div class="btns btns--stickyB">
			<span class="R">
				<button type="submit" class="btn btn--positive"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Save</button>
			</span>
        </div>

        <div class="block">

    		<div class="field">
    			<label class="field__label">Type of Transaction <em class="required">*</em></label>
    			<select class="w1/1" name="type" required>
    				<option value="">Choose Type...</option>
    				<option value="Buy"<?=($_POST['type'] == 'Buy') ? ' selected' : ''; ?>>Buy</option>
    				<option value="Sell"<?=($_POST['type'] == 'Sell') ? ' selected' : ''; ?>>Sell</option>
    			</select>
    		</div>

    		<div class="field">
    			<label class="field__label">List Price <em class="required">*</em></label>
    			<input class="w1/1" data-currency name="list_price" value="<?=preg_replace('/[^0-9]/', '', $_POST['list_price']); ?>" required>
    		</div>

    		<div class="field">
    			<label class="field__label">Sold Price <em class="required">*</em></label>
    			<input class="w1/1" data-currency name="sold_price" value="<?=preg_replace('/[^0-9]/', '', $_POST['sold_price']); ?>" required>
    		</div>

    		<div class="field">
    			<label class="field__label">MLS&reg; Number </label>
    			<input class="w1/1" type="text" name="mls_number" value="<?=htmlspecialchars($_POST['mls_number']); ?>">
    		</div>

    		<div class="field">
    			<label class="field__label">Transaction Details <em class="required">*</em></label>
    			<textarea class="w1/1" name="details" placeholder="Transaction Details..." required><?=htmlspecialchars($_POST['details']); ?></textarea>
    		</div>

        </div>

	</form>
	<?php else : ?>

	<?php if (!empty($transactions)) : ?>

	<div class="nodes">
		<ul class="nodes__list">
			<?php foreach ($transactions as $transaction) : ?>
			<li class="nodes__branch">
			    <div class="nodes__wrap">
    				<div class="article">
        				<div class="article__body">
        				    <?php if(!empty($transaction['listing']['ListingImage'])) { ?>
                            <div class="article__thumb thumb ">
                                <img src="<?=Format::thumbUrl($transaction['listing']['ListingImage'], '60x60'); ?>" alt="">
                            </div>
                            <?php } ?>
                            <div class="article__content">
                                <a class="text text--strong" href="?id=<?=$lead['id']; ?>&edit=<?=$transaction['id']; ?>"><?=$transaction['type']; ?> $<?=Format::number($transaction['sold_price']); ?></a>
                                <div class="text text--mute">MLS&reg; #<?=$transaction['mls_number']; ?></div>
                            </div>
        				</div>
    				</div>
    				<div class="nodes__actions">
        				<a href="?id=<?=$lead['id']; ?>&delete=<?=$transaction['id']; ?>" class="btn btn--ghost btn--ico delete" onclick="return confirm('Are you sure you want to delete this transaction?');"><svg class="icon icon-trash mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
    				</div>
			    </div>
            </li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php else : ?>

	<div class="block">
        <p>There are currently no transactions for this lead.</p>
	</div>

	<?php endif; ?>

<?php endif; ?>

<?php endif; ?>
