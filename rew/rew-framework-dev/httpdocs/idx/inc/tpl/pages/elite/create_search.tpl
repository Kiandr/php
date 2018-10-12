<?php if (!isset($_GET['popup'])) return; // This page is only for popups. ?>

<div class="modal-header">
    <h3>Save this search to receive updates of new listings.</h3>
</div>

<div class="modal-body">
    <form class="uk-form">
        <div class="uk-width-1-1 uk-margin-bottom">
            <label>Search Title</label>
            <input class="uk-form-large uk-width-1-1" name="search_title" required value="<?= Format::htmlspecialchars($_REQUEST['search_title']); ?>">
        </div>
        <div class="uk-width-1-1 uk-margin-bottom">
            <label class="uk-form-label uk-margin-right">Email Frequency</label>
            <select name="frequency" class="uk-form-large">
                <option value="never">Never</option>
                <option value="immediately">Immediately</option>
                <option value="daily">Daily</option>
                <option value="weekly" selected>Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>
        <div class="uk-width-1-1 uk-margin-bottom">
            <input type="checkbox" name="email_results_immediately" value="true"><label class="uk-form-label uk-margin-right"> Email Results Immediately</label>
        </div>
        <button class="uk-button create-saved-search">Save Search</button>
    </form>
</div>
