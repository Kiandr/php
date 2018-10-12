<?php
if (!empty($team_subdomain['agents'])) {
    $team_subdomain_agent_count = count($team_subdomain['agents']);
    if ($team_subdomain_agent_count > 1) {
        $slides_markup = [];
?>
        <?php if (!empty($team_subdomain['title'])) { ?>
            <h3><?=$team_subdomain['title']; ?></h3>
        <?php } ?>
        <?php
        foreach($team_subdomain['agents'] as $subdomain_agent) {
            ob_start();
        ?>
            <div class="article">
                <a class="popup" href="<?=htmlspecialchars($listing['url_inquire']); ?>?$subdomain_agent=<?=$subdomain_agent['id']; ?>">
                    <div class="thumb thumb--top -left -mar-right-sm">
                        <?php if(!empty($subdomain_agent['image'])) { ?>
                            <img data-src="<?=str_replace('/uploads/', '/thumbs/71x71/uploads/', $subdomain_agent['image']); ?>" data-srcset="<?=str_replace('/uploads/', '/thumbs/142x142/uploads/', $subdomain_agent['image']); ?> 2x" alt="">
                        <?php } ?>
                    </div>
                    <div class="article__body">
                        <h4 class="-mar-bottom-xs"><?=htmlspecialchars($subdomain_agent['name']); ?></h4>
                        <?php if (!empty($subdomain_agent['office_phone'])) { ?>
                            <div class="-text-xs -mar-bottom-xs">
                                Office: <?=htmlspecialchars($subdomain_agent['office_phone']); ?>
                            </div>
                        <?php } ?>
                        <?php if (!empty($subdomain_agent['cell_phone'])) { ?>
                            <div class="-text-xs">
                                Cell: <?=htmlspecialchars($subdomain_agent['cell_phone']); ?>
                            </div>
                        <?php } ?>
                    </div>
                </a>
            </div>
        <?php
            $slides_markup[] = ob_get_clean();
        }
        // Display Agents as Slideshow
        $this->getPage()->container('gallery')->module('slideshow', [
            'controller' => 'module_agent.php',
            'template' => 'module_agent.tpl.php',
            'stylesheet' => 'module_agent.css.php',
            'javascript' => 'module_agent.js.php',
            'slides' => $slides_markup,
        ])->display();
        ?>
    <?php
    } else if ($team_subdomain_agent_count === 1) {
        $agent = $team_subdomain['agents'][0];
    }
    ?>
<?php } ?>
<?php if (!empty($agent)) { ?>
    <div class="article">
        <a class="popup" href="<?=htmlspecialchars($listing['url_inquire']); ?>?agent=<?=$agent['id']; ?>">
            <div class="thumb thumb--top -left -mar-right-sm">
                <?php if(!empty($agent['image'])) { ?>
                    <img data-src="<?=str_replace('/uploads/', '/thumbs/71x71/uploads/', $agent['image']); ?>" data-srcset="<?=str_replace('/uploads/', '/thumbs/142x142/uploads/', $agent['image']); ?> 2x" alt="">
                <?php } ?>
            </div>
            <div class="article__body">
                <h4 class="-mar-bottom-xs"><?=htmlspecialchars($agent['name']); ?></h4>
                <?php if (!empty($agent['office_phone'])) { ?>
                    <div class="-text-xs -mar-bottom-xs">
                        Office: <?=htmlspecialchars($agent['office_phone']); ?>
                    </div>
                <?php } ?>
                <?php if (!empty($agent['cell_phone'])) { ?>
                    <div class="-text-xs">
                        Cell: <?=htmlspecialchars($agent['cell_phone']); ?>
                    </div>
                <?php } ?>
            </div>
        </a>
    </div>
<?php } ?>
