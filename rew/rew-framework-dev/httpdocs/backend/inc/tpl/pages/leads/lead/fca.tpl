<?php

/**
 * @var string $summaryMenu
 * @var array $leadSettings
 * @var array $lead
 */

echo $summaryMenu;

// First Call Agent details
$fca = \Container::getInstance()->get(\REW\Backend\Partner\Firstcallagent::class);


// FCA API key not entered in on backend
if (!$fca->hasAPIKey()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have the First Call Agent(FCA) API Key setup.'
    );
}

?>
<div class="block">
    <form method="post" class="rew_check">

        <input type="hidden" name="lead" value="<?=$lead['id']; ?>">

        <?php if ($leadSettings['sent'] == 'true') { ?>
            <p>This lead was sent to the FCA.</p>

            <?php if ($leadSettings['no_call'] == 'false') { ?>
                <button class="btn" type="submit" name="no_call">Request No Calling</button>
            <?php } ?>

        <?php } else { ?>
            <p>This lead has not been sent to the FCA.</p>

            <?php if ($leadSettings['sent'] == 'false' && $leadSettings['can_send']) { ?>
                <button class="btn" type="submit" name="send">Send to FCA</button>
            <?php } ?>

        <?php } ?>
        <?php if ($leadSettings['no_call'] == 'true') { ?>
            <p>A no calling request was sent for this lead.</p>
        <?php } ?>
    </form>
</div>