<?php

/**
 * @var AuthInterface $auth
 * @var Backend_Lead $lead
 * @var array $form
 * @var bool $canEmail
 */

?>

<div class="block">
    <div class="keyvals keyvals--bordered -marB">
        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="keyvals__key text text--strong -padB0@sm">Form Name</span>
            <span class="keyvals__val text text--mute -padT0@sm">
                <?=Format::htmlspecialchars($form['name']); ?>
            </span>
        </div>

        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="keyvals__key text text--strong -padB0@sm">Sent</span>
            <span class="keyvals__val text text--mute -padT0@sm">
                <?=date('D, M. j, Y g:ia', $form['timestamp']); ?>
            </span>
        </div>

        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="keyvals__key text text--strong -padB0@sm">Page</span>
            <span class="keyvals__val text text--mute -padT0@sm">
                <?php if (isset($form['page'])) { ?>
                    <a href="<?=Format::htmlspecialchars($form['page']); ?>"><?=isset($form['page']) ? Format::htmlspecialchars($form['page']) : '-'; ?></a>
                <?php } else { ?>
                    -
                <?php } ?>
            </span>
        </div>

        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="keyvals__key text text--strong -padB0@sm">Replied</span>
            <span class="keyvals__val text text--mute -padT0@sm">
                <?=isset($form['reply']) ? 'Yes' : 'No'; ?>
            </span>
        </div>

        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="keyvals__key text text--strong -padB0@sm">First Read</span>
            <span class="keyvals__val text text--mute -padT0@sm">
                <?=isset($form['read']) && !empty($form['read']) ? date('D, M. j, Y g:ia', $form['read']) : '-'; ?>
            </span>
        </div>
        <?php if (!empty($form['listing'])) { ?>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Listing</span>
                <span class="keyvals__val text text--mute -padT0@sm">
                    <a class="article" href="<?=$form['listing']['url_details']; ?>">
                        <div class="article__body lead-details">
                            <div class="article__thumb thumb thumb--large">
                                <img src="<?=$form['listing']['ListingImage']; ?>">
                            </div>
                            <div class="article__content">
                                #<?= !empty($form['listing']['ListingMLS']) ? $form['listing']['ListingMLS'] : $form['listing']['ListingMLSNumber']; ?> - <?= !empty($form['listing']['Address']) ? $form['listing']['Address'] : 'No Address'; ?>
                            </div>
                        </div>
                    </a>

                </span>
            </div>
        <?php } ?>
    </div>

    <?php if (!empty($form['data'])) { ?>
        <h3>Form Data</h3>
        <div class="keyvals keyvals--bordered -marB">
            <?php foreach ($form['data'] AS $title => $value) { ?>
            <div class="keyvals__row keyvals__row--rows@sm">
                <?php
                    // rename some fields
                    $title = preg_replace('/Telephone/', 'Primary Number', $title);
                    $title = preg_replace('/Fm Mobile/', 'Secondary Number', $title);
                ?>

                <span class="keyvals__key text text--strong -padB0@sm"><?=htmlspecialchars($title); ?></span>
                <?php if (is_array($value)) { ?>
                    <?php foreach ($value as $field => $data) { ?>
                        <strong><?=htmlspecialchars($field); ?>:</strong>
                        <?=htmlspecialchars($data); ?>
                        <br />
                    <?php } ?>
                <?php } else { ?>
                    <span class="keyvals__val text -padT0@sm"><?=htmlspecialchars($value); ?></span>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<?php if ($canEmail) {?>

    <div class="block">

        <form action="?submit" method="post" class="rew_check">
            <input type="hidden" name="id" value="<?=$lead['id']; ?>">
            <input type="hidden" name="form" value="<?=$form['id']; ?>">

            <h3>Send Quick Reply</h3>

            <div class="field">
                <input class="w1/1" type="text" name="email_subject" value="<?=htmlspecialchars(!empty($_POST['email_subject']) ? $_POST['email_subject'] : $form['subject']); ?>" placeholder="Subject" required>
            </div>

            <div class="field">
                <textarea class="tinymce w1/1 email" id="email_message" name="email_message" rows="20" cols="85"><?=Format::htmlspecialchars($_POST['email_message']); ?></textarea>
                <label class="hint">Tags: {first_name}, {last_name}, {email}
                    <?=($auth->isAgent() || $auth->isAssociate() ? ', {signature}' : ''); ?>
                </label>
            </div>

            <div class="btns btns--stickyB">
                <span class="R">
                    <button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Send</button>
                </span>
            </div>

            </div>
        </form>

    </div>
<?php } ?>
