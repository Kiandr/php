<?php

/**
 * @var string $feed
 * @var array $system
 * @var array $super_admin
 * @var array $responsive_preview
 * @var \REW\Core\Interfaces\SkinInterface $skin
 * @var \REW\Core\Interfaces\Page\BackendInterface $page
 * @var \REW\Core\Interfaces\SettingsInterface $settings
 */

?>
<form action="?submit" method="post" class="rew_check">
    <input type="hidden" name="feed" value="<?=$feed; ?>">

    <div class="bar">
        <div class="bar__title"><?= __('Saved Searches Email Settings'); ?></div>
    </div>

    <div class="block">
        <ul class="tabs">
            <li><a href="<?=URL_BACKEND; ?>settings/idx/?feed=<?=$feed; ?>"><?= __('General'); ?></a></li>
            <li><a href="<?=URL_BACKEND; ?>settings/idx/meta/?feed=<?=$feed; ?>"><?= __('Meta'); ?></a></li>
            <?php if ($saved_search_email_responsive_template_exists) { ?>
            <li class="current"><a href="<?=URL_BACKEND; ?>settings/idx/savedsearches/?feed=<?=$feed; ?>"><?= __('Saved Searches Email'); ?></a></li>
            <?php } ?>
        </ul>
    </div>
    <div class="block">
        <div class="field<?=Skin::hasFeature(Skin::SAVED_SEARCHES_RESPONSIVE) && !empty(Settings::getInstance()->MODULES['REW_SAVED_SEARCHES_RESPONSIVE']) && $system['force_savedsearches_responsive'] !== 'true' ? "" : " hidden";?>">
            <label class="field__label"><?= __('Use Saved Searches Responsive Template'); ?></label>
            <input type="radio" name="savedsearches_responsive" id="savedsearches_responsive_true" value="true"<?=($system['savedsearches_responsive'] === 'true' || $system['force_savedsearches_responsive'] === 'true') ? ' checked' : ''; ?>>
            <label class="toggle__label" for="savedsearches_responsive_true"><?= __('Yes'); ?></label>
            <input type="radio" name="savedsearches_responsive" id="savedsearches_responsive_false" value="false"<?=($system['savedsearches_responsive'] === 'false' && $system['force_savedsearches_responsive'] === 'false') ? ' checked' : ''; ?>>
            <label class="toggle__label" for="savedsearches_responsive_false"><?= __('No'); ?></label>
            <p class="text--mute"><strong><?= __('Yes'); ?>:</strong> <?= __('Saved Searches e-mail will be dispatched using the new responsive template'); ?>, <br/><strong><?= __('No'); ?>:</strong> <?= __('Saved Searches e-mail will be dispatched using legacy template.'); ?> </p>
        </div>
    </div>
    <div class="block<?=($system['savedsearches_responsive'] === 'true' || $system['force_savedsearches_responsive'] === 'true') ? '' : ' hidden'; ?>" id="responsive_message">
        <h3 class="panel__hd"><?= __('Sender Information'); ?></h3>
        <div class="field">
            <div class="toggle--stacked">
                <input id="sender_admin" type="radio" name="params[sender][from]" value="admin"<?=($params['sender']['from'] == 'admin') ? ' checked' : ''; ?>>
                <label  class="toggle__label" for="sender_admin">
                    <?=Format::htmlspecialchars($super_admin['first_name']); ?>
                    <?=Format::htmlspecialchars($super_admin['last_name']); ?>
                </label>
            </div>
            <div class="toggle--stacked">
                <input id="sender_agent" type="radio" name="params[sender][from]" value="agent"<?=($params['sender']['from'] == 'agent') ? ' checked' : ''; ?>>
                <label class="toggle__label" for="sender_agent"><?= __('Assigned Agent'); ?></label>
            </div>
            <div class="toggle--stacked">
                <input id="sender_custom" type="radio" name="params[sender][from]" value="custom"<?=($params['sender']['from'] == 'custom') ? ' checked' : ''; ?>>
                <label class="toggle__label" for="sender_custom"><?= __('Custom'); ?></label>
            </div>
        </div>
        <div id="responsive-from"<?=($params['sender']['from'] == 'custom') ? '' : ' class="hidden"'; ?>>
            <div class="field">
                <label class="field__label"><?= __('Sender Name'); ?> <em class="required">*</em></label>
                <input class="w1/1" name="params[sender][name]" value="<?=htmlspecialchars($params['sender']['name']); ?>">
            </div>
            <div class="field">
                <label class="field__label"><?= __('Sender Email'); ?> <em class="required">*</em></label>
                <input class="w1/1" type="email" name="params[sender][email]" value="<?=htmlspecialchars($params['sender']['email']); ?>">
            </div>
        </div>

        <div class="field">
            <h3 class="panel__hd"><?= __('Logo'); ?></h3>
            <div class="field">
                <label class="field__label"><?= __('Max. file size: 10mb'); ?></label>
                <div data-uploader='<?=json_encode([
                    'multiple' => false,
                    'inputName' => 'params[logo][id]',
                    'extraParams' => [
                        'type' => 'template',
                        'name' => 'email.template.logo'
                    ],
                    'onDelete' => 'function() { return false; }'
                ]); ?>'>
                    <?php if (!empty($params['logo']['file'])) : ?>
                        <div class="file-manager">
                            <ul>
                                <li>
                                    <div class="wrap"> <img src="/thumbs/95x95/uploads/<?=$params['logo']['file']; ?>" border="0">
                                        <input type="hidden" name="params[logo][id]" value="<?=$params['logo']['id']; ?>">
                                        <input type="hidden" name="params[logo][file]" value="<?=$params['logo']['file']; ?>">
                                    </div>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <h3 class="panel__hd"><?= __('Message'); ?></h3>
            <div class="field">
                <label class="field__label"><?= __('Display Message'); ?></label>
                <div class="toggle">
                    <input id="message_true" type="radio" name="params[message][display]" value="true"<?=($params['message']['display'] == 'true') ? ' checked': ''; ?>>
                    <label class="toggle__label" for="message_true"><?= __('Yes'); ?></label>
                    <input id="message_false" type="radio" name="params[message][display]" value="false"<?=($params['message']['display'] != 'true') ? ' checked': ''; ?>>
                    <label class="toggle__label" for="message_false"><?= __('No'); ?></label>
                </div>
            </div>

            <div class="field">
                <label class="field__label"><?= __('Subject'); ?> <em class="required">*</em></label>
                <input class="w1/1" type="text" name="params[message][subject]" value="<?=htmlspecialchars($params['message']['subject']); ?>"<?=($system['savedsearches_responsive'] === 'true') ? ' required' : ''; ?>>
                <label class="hint"><?= __('Tags'); ?>: {first_name}, {last_name}, {date}, {search_title}, {site_name}</label>
            </div>

            <div class="field" id="message_block">
                <label class="field__label"><?= __('Message'); ?> <em class="required">*</em></label>
                <textarea class="w1/1 tinymce super simple email" name="params[message][body]" rows="15" cols="80"><?=htmlspecialchars($params['message']['body']); ?></textarea>
                <label class="hint"><?= __('Tags'); ?>: {first_name}, {last_name}, {email}, {result_count}, {search_title}, {site_link}</label>
            </div>

            <h3 class="panel__hd"><?= __('Listings'); ?></h3>
            <div class="field">
                <label class="field__label"><?= __('Num. Rows'); ?></label>
                <select class="w1/1" id="num_rows" name="params[listings][num_rows]">
                    <?php foreach (range(1, 5) as $val) :?>
                        <option value="<?= $val; ?>"<?=(($params['listings']['num_rows'] == $val) ? ' selected="selected"' : ''); ?>>
                            <?= $val; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <h3 class="panel__hd"><?= __('Agent'); ?></h3>
            <div class="field">
                <label class="field__label"><?= __('Display Agent'); ?></label>
                <div class="toggle">
                    <input id="agent_true" type="radio" name="params[agent][display]" value="true"<?=($params['agent']['display'] == 'true') ? ' checked': ''; ?>>
                    <label class="toggle__label" for="agent_true"><?= __('Yes'); ?></label>
                    <input id="agent_false" type="radio" name="params[agent][display]" value="false"<?=($params['agent']['display'] != 'true') ? ' checked': ''; ?>>
                    <label class="toggle__label" for="agent_false"><?= __('No'); ?></label>
                </div>
            </div>

            <h3 class="panel__hd"><?= __('Social Media'); ?></h3>
            <div class="field">
                <div class="toggle--stacked">
                    <input id="sm_admin" type="radio" name="params[social_media][from]" value="admin"<?=($params['social_media']["from"] == 'admin') ? ' checked' : ''; ?>>
                    <label  class="toggle__label" for="sm_admin">
                        <?=Format::htmlspecialchars($super_admin['first_name']); ?>
                        <?=Format::htmlspecialchars($super_admin['last_name']); ?>
                    </label>
                </div>
                <div class="toggle--stacked">
                    <input id="sm_agent" type="radio" name="params[social_media][from]" value="agent"<?=($params['social_media']["from"] == 'agent') ? ' checked' : ''; ?>>
                    <label class="toggle__label" for="sm_agent"><?= __('Assigned Agent'); ?></label>
                </div>
                <div class="toggle--stacked">
                    <input id="sm_none" type="radio" name="params[social_media][from]" value=""<?=empty($params['social_media']["from"]) ? ' checked' : ''; ?>>
                    <label class="toggle__label" for="sm_none"><?= __('None'); ?></label>
                </div>
            </div>

            <h3 class="panel__hd"><?= __('Mailing Address'); ?></h3>
            <div class="field">
                <label class="field__label"><?= __('Default Office'); ?></label>
                <select class="w1/1" id="office_id" name="params[mailing_address][office_id]">
                    <option value=""><?= __('No Mailing Address'); ?></option>
                    <?php foreach ($offices as $office) :?>
                        <option value="<?=$office['id']; ?>"<?=(($params['mailing_address']['office_id'] == $office['id']) ? ' selected="selected"' : ''); ?>>
                            <?=$office['title']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <div class="toggle--stacked">
                    <input id="from_admin" type="radio" name="params[mailing_address][from]" value="admin"<?=($params['mailing_address']["from"] == 'admin') ? ' checked' : ''; ?>>
                    <label  class="toggle__label" for="from_admin">
                        <?=Format::htmlspecialchars($super_admin['first_name']); ?>
                        <?=Format::htmlspecialchars($super_admin['last_name']); ?>
                    </label>
                </div>
                <div class="toggle--stacked">
                    <input id="from_agent" type="radio" name="params[mailing_address][from]" value="agent"<?=($params['mailing_address']["from"] == 'agent') ? ' checked' : ''; ?>>
                    <label class="toggle__label" for="from_agent"><?= __('Assigned Agent'); ?></label>
                </div>
                <div class="toggle--stacked">
                    <input id="from_default" type="radio" name="params[mailing_address][from]" value="default"<?=($params['mailing_address']["from"] == 'default') ? ' checked' : ''; ?>>
                    <label class="toggle__label" for="from_default"><?= __('Default'); ?></label>
                </div>
                <div class="toggle--stacked">
                    <input id="from_none" type="radio" name="params[mailing_address][from]" value=""<?=empty($params['mailing_address']["from"]) ? ' checked' : ''; ?>>
                    <label class="toggle__label" for="from_none"><?= __('None'); ?></label>
                </div>
                <label class="hint">
                    <?= __(
                        'If %s %s or Assigned Agent has been chosen and no office is assigned to them, the default office will be used instead.',
                        Format::htmlspecialchars($super_admin['first_name']),
                        Format::htmlspecialchars($super_admin['last_name'])
                    ); ?>
                </label>
            </div>


        </div>

        <div class="field" id="preview_block">
            <label class="field__label"><?= __('Preview'); ?></label>
            <textarea class="w1/1 tinymce simple" name="preview" rows="15" cols="80" readonly data-no-link><?=htmlspecialchars($responsive_preview); ?></textarea>
        </div>
    </div>
    <div class="btns btns--stickyB">
        <div class="R">
            <button class="btn btn--positive" type="submit">
                <svg class="icon icon-check mar0">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use>
                </svg>
                <?= __('Save'); ?>
            </button>
        </div>
    </div>

</form>
