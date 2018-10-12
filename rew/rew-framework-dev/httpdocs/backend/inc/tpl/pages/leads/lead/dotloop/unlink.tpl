<?php

/**
 * @see REW\Backend\Controller\Leads\Lead\DotloopController
 *
 * @var \Backend_Lead $lead
 */

?>
<div class="block">
    <form method="post">
        <input type="hidden" name="unlink_lead" value="<?=$lead->getId(); ?>">
        <p>This lead will be unlinked from your DotLoop account. <b>Contact data in your DotLoop account will not be affected.</b></p>
        <div class="btns">
            <button type="submit" class="btn btn--negative">Unlink Lead</button>
            <a href="<?=sprintf('%sleads/lead/summary/?id=%s', URL_BACKEND, $lead->getId());?>" class="btn">Cancel</a>
        </div>
    </form>
</div>
