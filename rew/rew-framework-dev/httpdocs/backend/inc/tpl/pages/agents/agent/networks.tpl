<?php
// Render agent summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/agent/summary.tpl.php', [
    'title' => __('Network Accounts'),
    'agent' => $agent,
    'agentAuth' => $agentAuth
]);
?>
<div class="block">
    <p><?= __('Please be aware that these features are dependent on the operation and cooperation of third party services. As such, Real Estate Webmasters can\'t guarantee continuous availability of these features.'); ?></p>
    <?php if (!empty($networks) && is_array($networks)) : ?>
        <?php foreach ($networks as $key => $network) : ?>
            <h3><?=$network['title']; ?></h3>
            <?php if (!empty($network['account'])) : ?>
                <button type="button" class="btn btn--positive"><?= __('Connected to'); ?> <?=$network['title']; ?></button>
                <a href="<?=$network['disconnect']; ?>" onclick="return confirm('<?= __('Are you sure you want to disconnect this account?'); ?>');" class="btn negative"><?= __('Disconnect'); ?></a>
                <h5><?=$network['account']['name']; ?></h5>
                <?php if (!empty($network['account']['image'])) : ?>
                    <img src="<?=$network['account']['image']; ?>" alt="">
                <?php endif; ?>
            <?php else : ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="connect" value="<?=$key; ?>">
                    <div class="field w1/1">
                        <label class="fld__label" for="service_account_key">Service Account Key</label>
                        <input type="file" required class="w1/1" name="service_account_key" id="service_account_key">
                        <p class="text--mute">
                            This <strong>must</strong> be a Service Account key in JSON format.
                        </p>
                        <div class="text--mute">
                            To generate a service account key:
                            <ol>
                                <li>Go to <a href="https://console.developers.google.com/apis/credentials">https://console.developers.google.com/apis/credentials</a>. If you do not have a project already, you will have to create one.</li>
                                <li>Click Create Credentials and choose Service Account Key. Choose a Service account or create a new one. You do not need to select a role.</li>
                                <li>Choose JSON for the key type and click create. This will download a file to your computer, and this is the file you need to upload here.</li>
                                <li>Click Manage Service Account after selecting the correct service account and you will see a Service account ID field. This ID (example: wmttest@xxxx-185301.iam.gserviceaccount.com) will need to be added as an authorized user on any WMT or GA accounts you want it to access. These do not all have to be in the same account.</li>
                            </ol>
                        </div>
                    </div>
                    <button type="submit" class="btn<?=!empty($network['disabled']) ? ' disabled' : ''; ?>">Connect to <?=$network['title']; ?></button>

                </form>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>