<?php

/**
 * History timeline template
 * @var array $pagination
 * @var array $history
 * @var int|NULL $user
 * @var string $view
 */

?>
<?php if (!empty($history)) { ?>
    <section id="history-timeline" class="nodes">
        <?php foreach ($history as $date => $events) { ?>
            <ul class="nodes__list">
                <li class="nodes__branch">
                    <div class="nodes__wrap nodes__wrap--no-divider">
                        <div class="article">
                            <div class="article__body">
                                <div class="article__content">
                                    <div class="divider">
                                        <span class="divider__label divider__label--left text text--mute">
                                        <?php

                                            if (date('d-m-Y') == $date) {
                                                echo 'Today';
                                            } else if (date('d-m-Y', strtotime('-1 day')) == $date) {
                                                echo 'Yesterday';
                                            } else {
                                                echo date('D, M jS Y', strtotime($date));
                                            }

                                        ?>
                                        </span>
                                    </div>
                                    <ul class="nodes__list">
                                        <?php foreach ($events as $event) { ?>
                                            <li class="nodes__branch">
                                                <div class="nodes__wrap">
                                                <?php

                                                    // Render history event view
                                                    echo $this->render(__DIR__ . '/history-event.tpl.php', [
                                                        'event' => $event,
                                                        'view' => $view,
                                                        'user' => $user
                                                    ]);

                                                ?>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        <?php } ?>
        <?=$this->render(__DIR__ . '/pagination.tpl.php', $pagination); ?>
    </section>
<?php } ?>
