<?=$view->render('::/partials/leads/leads-menu.tpl.php', [
    'leadsAuth' => $leadsAuth,
    'authuser' => $authuser,
    'isSharktankEnabled' => $isSharktankEnabled
]); ?>

<?php

/**
 * @see REW\Backend\Controller\Leads\SharktankController
 *
 * @var REW\Backend\Auth\LeadsAuth $leadsAuth
 * @var REW\Backend\View\Interfaces\FactoryInterface $view
 */

?>

<div class="bar">
    <a class="bar__title">
        Shark Tank
    </a>
    <div class="bar__actions">
        <?php if (!empty($_GET['tank_lead'])) { ?>
            <a class="bar__action" href="<?=Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/sharktank/'; ?>">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a" />
                </svg>
            </a>
        <?php } ?>
    </div>
</div>

<?php if (!empty($tank_leads)) { ?>
    <div class="nodes">
        <ul class="nodes__list" id="lead_list">
            <?php foreach ($tank_leads as $lead) { ?>
                <li class="nodes__branch">
                    <div class="nodes__wrap">
                        <div class="article">
                            <div class="article__body">

                                <div class="article__thumb thumb thumb--medium -bg-<?=strtolower($lead->info('last_name')[0]);?>">
                                    <?php if (empty($lead['image'])) { ?>
                                        <span class="thumb__label"><?=$lead->info('first_name')[0];?><?=$lead->info('last_name')[0];?></span>
                                    <?php } else { ?>
                                        <img src="uploads/leads/<?=$lead['image'];?>uploads/leads/na.png" alt="">
                                    <?php } ?>
                                </div>

                                <div class="article__content">
                                    <?php if ($lead['authCanViewLead']) { ?>
                                        <a class="text text--strong" href="<?=URL_BACKEND ; ?>leads/lead/summary/?id=<?=$lead['id']; ?>"><?=($lead->getName()) ? (strlen($lead->getName()) > 20) ? '<abbr title="' . $format->htmlspecialchars($lead->getName()) . '">' . $format->htmlspecialchars(substr($lead->getName(), 0, 20)) . '...</abbr>' : $format->htmlspecialchars($lead->getName()) : '<em class="no-name">(' . $format->htmlspecialchars($lead->getNameOrSubstitute()) . ')</em>'; ?></a>
                                    <?php } else { ?>
                                        <?=($lead->getName()) ? (strlen($lead->getName()) > 20) ? '<abbr title="' . $format->htmlspecialchars($lead->getName()) . '">' . $format->htmlspecialchars(substr($lead->getName(), 0, 20)) . '...</abbr>' : $format->htmlspecialchars($lead->getName()) : '<em class="no-name">(' . $format->htmlspecialchars($lead->getNameOrSubstitute()) . ')</em>'; ?>
                                    <?php } ?>
                                    <div class="text text--mute">
                                        Entered the tank
                                        <?php if (!empty($lead['timestamp_in_shark_tank'])) { ?>
                                            <b><?=$format->dateRelative($lead['timestamp_in_shark_tank']); ?></b>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div>

                            <?php if($lead['value'] > 0) { ?>
                                <div class="article__foot padT">
                                    <div class="keyvals keyvals--bordered">
                                        <div class="keyvals__row">
                                            <span class="keyvals__key text text--strong">Value</span>
                                            <span class="keyvals__val text text--mute">$<?=$format->shortNumber($lead['value']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="grid">
                                <div class="padT">
                                <form method="post" action="?claim">
                                    <a class="grid__col btn btn--positive -w1/2" href="?claim=<?=$lead->getId(); ?>">
                                        <svg class="icon">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use>
                                        </svg>
                                        Claim
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } else { ?>
    <div class="block">
        <?php if (!empty($_GET['tank_lead'])) { ?>
            <p>The requested lead is no longer available in the Shark Tank. It is possible that they have already been claimed by another agent.</p>
        <?php } else { ?>
            <p>The Shark Tank is currently empty</p>
        <?php } ?>
    </div>
<?php } ?>