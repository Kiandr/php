<?php

/**
 * @var Backend_Lead $lead
 * @var array $forms
 * @var int $formCount
 * @var array $categories
 * @var string $category
 * @var int|null $p
 * @var Pagination $pagination
 * @var bool $canEmail
 * @var bool $canDelete
 */

?>

<?php if (!empty($forms)) { ?>
    <div class="nodes">
    	<ul class="nodes__list">
    	<?php foreach ($forms as $form) { ?>
    		<li class="nodes__branch">
    		    <div class="nodes__wrap">
        			<div class="article">
        			    <div class="article__body">
                            <div class="article__content">
            					<div class="text text--strong"><a class="text text--strong" href="<?=URL_BACKEND ?>leads/lead/forms/view/?id=<?=$lead['id']; ?>&form=<?=$form['id']; ?>">
            						<?=Format::htmlspecialchars($form['name']) . (!empty($form['address']) ? ': ' . $form['address']: '' ); ?>
        						</a></div>
            					<?=Format::htmlspecialchars($form['comments']); ?>
            					<div class="text text--mute"><?=date('D, M. j, Y g:ia', $form['timestamp']); ?></div>
            					<p>
                					<?php if (!$form['reply']) { ?>
                						<a href="?id=<?=$lead['id']; ?>&toggle=<?=$form['id']; ?>" class="btn btn--positive check" title="Click to Mark as unreplied">Mark Replied</a>
                					<?php } else { ?>
                						<a href="?id=<?=$lead['id']; ?>&toggle=<?=$form['id']; ?>" class="btn btn--negative alert" title="Click to Mark as replied">Mark Unreplied</a>
                					<?php } ?>
            					</p>
                            </div>
        					<div class="nodes__actions">
    							<a class="btn btn--ghost edit" href="<?=URL_BACKEND ?>leads/lead/forms/view/?id=<?=$lead['id']; ?>&form=<?=$form['id']; ?>">View</a>
        						<?php if (!empty($canDelete)) { ?>
                                    <form method="post" action="?id=<?=$lead['id']; ?>">
                                        <input type="hidden" name="delete" value="<?=$form['id']; ?>" />
                                        <button onclick="return confirm('<?= __('Are you sure you would like to delete this form?'); ?>');" title="<?= __('Delete'); ?>" class="btn btn--ghost btn--ico">
                                            <icon name="icon--trash--row"></icon>
                                        </button>
                                    </form>
        						<?php } ?>
        					</div>
                        </div>
    				</div>
    			</li>
    		<?php } ?>
    	</ul>
    </div>
<?php } else { ?>
    <div class="block">
        <p id="reminder-message">
        	<p>This lead has not filled out any forms.
        </p>
    </div>
<?php } ?>

<?php if (!empty($forms)) { ?>
    <?php if (!empty($pagination['links'])) { ?>
        <div class="btns padV text--center">
            <?php if (!empty($pagination['prev'])) { ?>
                <a href="<?=$pagination['prev']['url']; ?>" class="btn prev">
                    <svg class="icon">
                        <use xlink:href="/backend/img/icos.svg#icon-left-a" xmlns:xlink="http://www.w3.org/1999/xlink"/>
                    </svg>
                    Prev
                </a>
            <?php } ?>
            <?php if (!empty($pagination['next'])) { ?>
                <a href="<?=$pagination['next']['url']; ?>" class="btn next">Next
                    <svg class="icon">
                        <use xlink:href="/backend/img/icos.svg#icon-right-a" xmlns:xlink="http://www.w3.org/1999/xlink"/>
                    </svg>
                </a>
            <?php } ?>
        </div>
    <?php } ?>
    </div>
<?php } ?>