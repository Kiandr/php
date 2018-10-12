<?php

/**
 * History event template
 * @var History_Event $event
 * @var int|NULL $user
 * @var string $view
 */

?>
<div class="article" data-event="<?=$event->getID(); ?>" data-event-type="<?=$event->getType(); ?>">
    <div class="article__body">
        <div class="article__content">
            <div class="text text--strong">
                <?=$event->getMessage(['view' => $view, 'user' => $user]); ?>
            </div>
            <?php if ($event instanceof History_IExpandable) { ?>
                <a href="javascript:void(0);" class="expand-event">+</a>
            <?php } ?>
            <div class="text text--mute">
                <time class="v marR" datetime="<?=date('c', $event->getTimestamp()); ?>" title="<?=date('l, F jS Y \@ g:ia', $event->getTimestamp()); ?>">
                    <?=date('g:i A', $event->getTimestamp()); ?>
                </time>
            </div>
            <div class="event-details hidden"></div>
        </div>
    </div>
</div>