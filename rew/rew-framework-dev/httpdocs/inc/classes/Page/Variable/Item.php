<?php

use REW\Core\Interfaces\PageInterface;

/**
 * Page_Variable_Item
 *
 * "links" : {
 *  "type" : "item",
 *  "title" : "Call to Action",
 *  "createText" : "Add Call to Action",
 *  "updateText" : "Edit Call to Action",
 *  "previewText" : "{heading}",
 *  "default" : {
 *      "heading" : "Contact a Real Estate Professional!",
 *      "url" : "/contact.php"
 *  },
 *  "variables" : {
 *      "heading" : {
 *          "type" : "text"
 *          "title" : "Heading",
 *          "required" : true
 *      },
 *      "url" : {
 *          "type" : "text"
 *          "title" : "Link To",
 *          "placeholder" : "http://",
 *          "required" : true
 *      }
 *  }
 * }
 *
 */
class Page_Variable_Item extends Page_Variable
{

    /**
     * Item's variables
     * @var Page_Variable[]
     */
    protected $variables = array();

    /**
     * Preview text
     * @var string
     */
    protected $previewText;

    /**
     * Text for create action
     * @var string
     */
    protected $createText = 'Create Item';

    /**
     * Text for update action
     * @var string
     */
    protected $updateText = 'Update Item';

    /**
     * Text for revert action
     * @var string
     */
    protected $revertText = 'Revert to Default';

    /**
     * Able to revert
     * @var bool
     */
    protected $revert = true;

    /**
     * Setup variable instance
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options = array())
    {
        parent::__construct($name, $options);
        $this->setVariables($options['variables']);
        $this->setPreviewText($options['previewText']);
        if (!empty($options['createText'])) {
            $this->setCreateText($options['createText']);
        }
        if (!empty($options['updateText'])) {
            $this->setUpdateText($options['updateText']);
        }
        if (!empty($options['revertText'])) {
            $this->setRevertText($options['revertText']);
        }
        if (is_bool($options['revert'])) {
            $this->canRevert($options['revert']);
        }
    }

    /**
     * Get default values
     * @return array
     */
    public function getDefaults()
    {
        $defaults = [];
        $variables = $this->getVariables();
        foreach ($variables as $variable) {
            $defaults[$variable->getName()] = $variable->getDefault();
            if ($children = $variable->getChildren()) {
                foreach ($children as $child) {
                    $defaults[$child->getName()] = $child->getDefault();
                }
            }
        }
        return $defaults;
    }

    /**
     * Get item's variables
     * @return Page_Variable[]
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Get create text
     * @return string
     */
    public function getCreateText()
    {
        return $this->createText;
    }

    /**
     * Get update text
     * @return string
     */
    public function getUpdateText()
    {
        return $this->updateText;
    }

    /**
     * Get preview text
     * @return string
     */
    public function getPreviewText()
    {
        return $this->previewText;
    }

    /**
     * Get revert text
     * @return string
     */
    public function getRevertText()
    {
        return $this->revertText;
    }

    /**
     * Set/get revert toggle
     * @param bool $revert
     * @return bool
     */
    public function canRevert($canRevert = null)
    {
        if (!is_null($canRevert)) {
            $this->revert = (bool) $canRevert;
        }
        return $this->revert;
    }

    /**
     * Set item's variables
     * @param array $variables
     * @throws InvalidArgumentException
     * @return self
     */
    public function setVariables(array $variables)
    {
        if (empty($variables) || !is_array($variables)) {
            throw new InvalidArgumentException('variables must be defined');
        }
        foreach ($variables as $name => $options) {
            $this->variables[$name] = self::load($name, array_merge($options, array(
                'inputName' => $name
            )));
        }
        return $this;
    }

    /**
     * Set create text
     * @param string $createText
     * @throws InvalidArgumentException
     * @return self
     */
    public function setCreateText($createText)
    {
        if (empty($createText) || !is_string($createText)) {
            throw new InvalidArgumentException('createText must be a string and cannot be empty');
        }
        $this->createText = $createText;
        return $this;
    }

    /**
     * Set update text
     * @param string $updateText
     * @throws InvalidArgumentException
     * @return self
     */
    public function setUpdateText($updateText)
    {
        if (empty($updateText) || !is_string($updateText)) {
            throw new InvalidArgumentException('updateText must be a string and cannot be empty');
        }
        $this->updateText = $updateText;
        return $this;
    }

    /**
     * Set preview text
     * @param string $previewText
     * @throws InvalidArgumentException
     * @return self
     */
    public function setPreviewText($previewText)
    {
        if (empty($previewText) || !is_string($previewText)) {
            throw new InvalidArgumentException('previewText must be a string and cannot be empty');
        }
        $this->previewText = $previewText;
        return $this;
    }

    /**
     * Set revert text
     * @param string $revertText
     * @throws InvalidArgumentException
     * @return self
     */
    public function setRevertText($revertText)
    {
        if (empty($revertText) || !is_string($revertText)) {
            throw new InvalidArgumentException('revertText must be a string and cannot be empty');
        }
        $this->revertText = $revertText;
        return $this;
    }

    /**
     * Set template for variables
     * @param Page_Template $template
     * @return self
     */
    public function setTemplate($template)
    {
        parent::setTemplate($template);
        foreach ($this->variables as $variable) {
            $variable->setTemplate($template);
        }
        return $this;
    }

    /**
     * Get item value
     * @return array
     */
    public function getValue()
    {
        $item = $this->getItem();
        // Use default value for unset variables
        if ($variables = $this->getVariables()) {
            foreach ($variables as $name => $variable) {
                if (!array_key_exists($name, $item)) {
                    $item[$name] = $variable->getDefault();
                }
            }
        }
        // Load image URLs for items
        return $this->loadImageUrls($item);
    }

    /**
     * Get item data
     * @return array
     */
    public function getItem()
    {
        $value = parent::getValue();
        if (is_string($value)) {
            return json_decode($value, true);
        }
        return $value;
    }

    /**
     * @see Page_Variable::display
     */
    public function display(PageInterface $page = null, $disabled = false)
    {

        // Add resource resources
        $this->addJavascript($page);

        // Item details
        $item = $this->getItem();
        $name = $this->getField();

        // Render variable markup
        echo '<div id="' . $this->getId() . '">';
        $this->displayTitle();
        echo '<div class="marB8" data-item-list="' . $name . '" class="hidden">';
        if (!empty($item)) {
            $this->displayItem($name, $item);
        }
        echo '</div>';
        $this->displayForm($page);
        $this->displayButton();
        $this->displayRevert();
        echo '</div>';
    }

    /**
     * Render item
     * @param string $name Item name
     * @param array $item Item data
     * @param string|null $append HTML to append
     */
    public function displayItem($name, array $item, $append = null)
    {
        $json = json_encode($item);
        $item = $this->loadImageData($item);
        echo '<div class="slides__item" data-item-data="' . htmlspecialchars(json_encode($item)) . '" style="padding-bottom: 16px; border: 1px solid #ccc;">';
        echo '<a data-item-update style="position: relative; top: 6px;">';
        $this->displayPreview($item);
        echo '</a>';
        echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($json) . '">';
        if (!$this->isRequired()) {
            echo PHP_EOL . '<a data-item-delete class="hidden" style="position: relative; top: 6px;">&times;</a>';
        }
        if (!empty($append)) {
            echo PHP_EOL . $append;
        }
        echo '</div>';
    }

    /**
     * Render form used to add/edit items
     * @param PageInterface $page
     */
    public function displayForm(PageInterface $page)
    {
        $variables = $this->getVariables();
        echo '<div data-item-form class="hidden">';
        if (!empty($variables)) {
            echo '<div>';
            foreach ($variables as $variable) {
                echo '<div class="field">';
                $variable->display($page, true);
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
    }

    /**
     * Render button to add item
     */
    public function displayButton()
    {
        $createText = $this->getCreateText();
        if (!$this->isRequired() && !empty($createText)) {
            echo '<a data-item-create class="btn hidden">' . $createText . '</a>';
        }
    }

    /**
     * Render revert button
     */
    public function displayRevert()
    {
        if ($this->canRevert() && ($revertText = $this->getRevertText())) {
            $defaultItem = $this->getDefault();
            if ($defaultItem !== $this->getItem()) {
                echo '<a href="#revert-' . $this->getID() . '" data-item-revert="' . htmlspecialchars(json_encode($defaultItem)) . '">';
                echo Format::htmlspecialchars($revertText);
                echo '</a>';
            }
        }
    }

    /**
     * Generate item preview
     * @param array $item
     * @return string
     */
    private function displayPreview($item)
    {
        $fields = array_keys($item);
        $values = array_values($item);
        $previewText = $this->getPreviewText();
        echo str_replace(array_map(function ($field) {
            return '{' . $field . '}';
        }, $fields), $values, $previewText);
    }

    /**
     * Convert item's image variables to URLs for display
     * @param array $item
     * @return array
     */
    public function loadImageUrls($item)
    {
        if (!empty($item) && is_array($item)) {
            foreach ($item as $field => $value) {
                $variable = $this->variables[$field];
                if (empty($variable)) {
                    continue;
                }
                if ($variable->getType() === self::TYPE_IMAGE) {
                    if (is_numeric($value)) {
                        $variable->setValue($value);
                        $item[$field] = $variable->getValue();
                        $variable->setValue(null);
                    }
                }
            }
        }
        return $item;
    }

    /**
     * Fetch upload record for item's image variables
     * @param array $item
     * @return array
     */
    private function loadImageData($item)
    {
        if (!empty($item) && is_array($item)) {
            foreach ($item as $field => $value) {
                $variable = $this->variables[$field];
                if (empty($variable)) {
                    continue;
                }
                if ($variable->getType() === self::TYPE_IMAGE) {
                    if (is_numeric($value)) {
                        $variable->setValue($value);
                        $variable->setThumbnail(null);
                        if ($upload = $variable->getUpload()) {
                            $ext = substr($upload['file'], strrpos($upload['file'], '.') + 1);
                            $item[$field] = array(
                                'id'    => (int) $upload['id'],
                                'file'  => $upload['path'],
                                'name'  => $upload['file'],
                                'ext'   => $ext
                            );
                        }
                        $variable->setValue(null);
                    }
                }
            }
        }
        return $item;
    }

    /**
     * Add JavaScript to page
     * @param PageInterface $page
     */
    private function addJavascript(PageInterface $page)
    {

        // Extra JavaScript
        ob_start();

?>
/*
<script> */
(function () {
    'use strict';

    // Required script variables
    var $variable = $('#<?=$this->getId(); ?>')
        , $list = $variable.find('[data-item-list]')
        , $create = $variable.find('a[data-item-create]')
        , createText = <?=json_encode($this->getCreateText()); ?>
        , updateText = <?=json_encode($this->getUpdateText()); ?>
        , previewText = <?=json_encode($this->getPreviewText()); ?>
        , isRequired = <?=json_encode($this->isRequired()); ?>
        , field = $list.data('item-list')
    ;

    // Generate text for item preview
    var previewItem = function (item) {
        var replace = previewText.match(/{(.[^}]*)}/g);
        if (!replace) return previewText;
        var preview = previewText;
        $.each(replace, function () {
            var field = this.replace(/{|}/g, '');
            if (typeof item[field] === 'string') {
                preview = preview.replace(this, item[field]);
            }
        });
        return preview;
    };

    // Update list when change occurs
    var updateList = function () {
        var $items = $list.find('[data-item-data]')
            , $delete = $items.find('a[data-item-delete]')
            , numItems = $items.length
            , canDelete = !isRequired
            , canCreate = $items.length === 0
        ;
        $delete.toggleClass('hidden', !canDelete);
        $create.toggleClass('hidden', !canCreate);
        $list.toggleClass('hidden', numItems === 0);
        if (numItems === 0) {
            $('<input class="dummy" name="' + field + '" value="false" />').appendTo($list);
        } else {
            $list.find('input.dummy').remove();
        }
    };

    // Reset the form to all it's glory
    var resetForm = function ($form) {
        // Reset uploader instances
        var $uploaders = $form.find(':ui-rew_uploader');
        $uploaders.rew_uploader('reset');
        $uploaders.rew_uploader('enable');
        // Reset form state and enable fields
        $form.find(':input').prop('disabled', false);
        $form.trigger('reset');
    };

    // Fill form fields from object data
    var completeForm = function ($form, data) {
        var els = $form.find(':input').not(':button,[type="hidden"]').get();
        $.each(els, function () {
            if (this.name) {
                var value = data[this.name] || '';
                if (typeof value === 'string') {
                    if (this.type == 'checkbox' || this.type == 'radio') {
                        $(this).prop('checked', (value == $(this).val()));
                    } else {
                        $(this).val(value);
                        $(this).prop('value', value);
                    }
                }
            }
        });
        // Update uploader instances
        var $uploaders = $form.find(':ui-rew_uploader');
        $uploaders.each(function () {
            var $uploader = $(this)
                , inputName = $uploader.rew_uploader('option', 'inputName')
                , imageData = data[inputName]
            ;
            if (typeof imageData === 'object' && imageData) {
                $uploader.rew_uploader('addUpload', imageData);
            }
        });
    };

    // Serialize form data to object
    var serializeForm = function ($form) {
        var form = $form.serializeArray();
        var data = {};
        $.each(form, function() {
            if (data[this.name] !== undefined) {
                if (!data[this.name].push) {
                    data[this.name] = [data[this.name]];
                }
                data[this.name].push(this.value || '');
            } else {
                data[this.name] = this.value || '';
            }
        });
        return data;
    };

    // Setup item form
    var $form = $('<form />').append($variable.find('[data-item-form]').detach().removeClass('hidden'))
        , $submit = $('<button />', { type: 'submit', 'class': 'hidden' }).appendTo($form)
        , $dialog = $('<div />').append($form)
    ;

    // Setup dialog
    $dialog.dialog({
        autoOpen: false,
        modal: true,
        width: 450,
        height: 475,
        buttons: [{
            text: 'Save',
            click: function () {
                $submit.trigger('click');
            }
        }, {
            text: 'Cancel',
            click: function () {
                $(this).dialog('close');
            }
        }]
    });

    // Create item
    $create.on('click', function () {

        // Update form
        resetForm($form);
        completeForm($form, <?=json_encode($this->getDefaults()); ?>);

        // Update and show dialog
        $dialog.dialog('option', 'title', createText);
        $dialog.dialog('open');

        // Add new item to list when form is processed
        $form.off('submit').on('submit', function () {
            var itemData = serializeForm($(this));
            var jsonData = jQuery.extend(true, {}, itemData);

            // Get data from uploader instances
            var $uploaders = $form.find(':ui-rew_uploader');
            if ($uploaders.length > 0) {
                var required = false;
                var uploading = false;
                $uploaders.each(function () {
                    var $uploader = $(this);
                    if ($uploader.rew_uploader('isLoading') === true) {
                        uploading = true;
                    }
                    var uploads = $uploader.rew_uploader('getUploads')
                        , inputName = $uploader.rew_uploader('option', 'inputName')
                    ;
                    if (uploads && uploads.length) {
                        itemData[inputName] = uploads[0];
                        jsonData[inputName] = uploads[0].id;
                    } else {
                        required = !$uploader.rew_uploader('validate') || required;
                    }
                });
                // Stop the process!
                if (uploading || required) {
                    return false;
                }
            }

            // Append new item to list
            var $item = $('<div class="slides__item" data-item-data>\
                <a data-item-update>' + previewItem(itemData) + '</a>\
                <input type="hidden" name="' + field + '" value="">\
                <a data-item-delete class="hidden">&times;</a>\
            </div>').appendTo($list);

            // Update item's data
            var json = JSON.stringify(jsonData);
            $item.find(':input').val(json);
            $item.data('item-data', itemData);
            $item.attr('data-item-data', itemData);

            // Close dialog
            $dialog.dialog('close');
            updateList();
            return false;

        });

    });

    // Update item
    $list.on('click', 'a[data-item-update]', function () {
        var $link = $(this)
            , $item = $link.closest('[data-item-data]')
            , origItem = $item.data('item-data')
        ;

        // Update form
        resetForm($form);
        completeForm($form, origItem);

        // Update and show dialog
        $dialog.dialog('option', 'title', updateText);
        $dialog.dialog('open');

        // Update existing list item on form submit
        $form.off('submit').on('submit', function () {
            var itemData = serializeForm($(this));
            var jsonData = jQuery.extend(true, {}, itemData);

            // Get data from uploader instances
            var $uploaders = $form.find(':ui-rew_uploader');
            if ($uploaders.length > 0) {
                var required = false;
                var uploading = false;
                $uploaders.each(function () {
                    var $uploader = $(this);
                    if ($uploader.rew_uploader('isLoading') === true) {
                        uploading = true;
                    }
                    var uploads = $uploader.rew_uploader('getUploads')
                        , inputName = $uploader.rew_uploader('option', 'inputName')
                    ;
                    if (uploads && uploads.length) {
                        itemData[inputName] = uploads[0];
                        jsonData[inputName] = uploads[0].id;
                    } else if (typeof origItem[inputName] === 'string') {
                        itemData[inputName] = origItem[inputName];
                        jsonData[inputName] = origItem[inputName];
                    } else {
                        required = !$uploader.rew_uploader('validate') || required;
                        delete itemData[inputName];
                        delete jsonData[inputName];
                    }
                });
                // Stop the process!
                if (uploading || required) {
                    return false;
                }
            }

            // Update item preview
            var preview = previewItem(itemData);
            $link.html(preview);

            // Update item's data
            var json = JSON.stringify(jsonData);
            $item.find(':input').val(json);
            $item.data('item-data', itemData);
            $item.attr('data-item-data', itemData);

            // Close dialog
            $dialog.dialog('close');
            updateList();
            return false;

        });

    });

    // Delete item
    $list.on('click', 'a[data-item-delete]', function () {
        if (confirm('Are you sure you want to remove this item?')) {
            var $item = $(this).closest('[data-item-data]');
            $item.remove();
            updateList();
        }
    });

    // Revert item
    $variable.on('click', 'a[data-item-revert]', function () {
        var $link = $(this), item = $link.data('item-revert');

        // Confirm to revert
        if (!confirm('Are you sure you want to revert? [CHANGES WILL BE LOST]')) {
            return false;
        }

        // Replace list with default item
        var $item = $('<div class="slides__item" data-item-data>\
            <a data-item-update>' + previewItem(item) + '</a>\
            <input type="hidden" name="' + field + '" value="">\
            <a data-item-delete class="hidden">&times;</a>\
        </div>');

        // Item data
        var json = JSON.stringify(item);
        $item.find(':input').val(json);
        $item.data('item-data', item);
        $item.attr('data-item-data', item);
        $list.html($item);

        // Update list
        $link.remove();
        updateList();
        return false;

    });

    // Item list
    updateList();

})();
/* </script>
*/
<?php

        // Append JavaScript
        $page->addJavascript(ob_get_clean(), 'dynamic', false);
    }
}