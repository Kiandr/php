<?php

use REW\Core\Interfaces\PageInterface;

/**
 * Page_Variable_List
 *
 * "links" : {
 *  "type" : "list",
 *  "title" : "Featured Sites",
 *  "createText" : "Add Link",
 *  "updateText" : "Edit Link",
 *  "previewText" : "{name}",
 *  "minItems" : 0,
 *  "maxItems" : 1,
 *  "default" : [{
 *      "name" : "Item #1",
 *      "link" : "/"
 *  }, {
 *      "name" : "Item #2",
 *      "link" : "/"
 *  }],
 *  "variables" : {
 *      "name" : {
 *          "type" : "text"
 *          "title" : "Link Name",
 *          "required" : true
 *      },
 *      "link" : {
 *          "type" : "text"
 *          "title" : "Link URL",
 *          "placeholder" : "http://",
 *          "required" : true
 *      }
 *  }
 * }
 *
 */
class Page_Variable_List extends Page_Variable
{

    /**
     * Sortable list
     * @var bool
     */
    protected $sortable = true;

    /**
     * Min # of items
     * @var int|NULL
     */
    protected $minItems;

    /**
     * Max # of items
     * @var int|NULL
     */
    protected $maxItems;

    /**
     * @var Page_Variable_Item
     */
    protected $dummyItem;

    /**
     * Setup variable instance
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options = array())
    {
        parent::__construct($name, $options);
        $this->setMinItems($options['minItems']);
        $this->setMaxItems($options['maxItems']);
        if (is_bool($options['sortable'])) {
            $this->setSortable($options['sortable']);
        }

        // Setup dummy item to use
        $this->dummyItem = new Page_Variable_Item($name, $options);
    }

    /**
     * Get # of min items allowed
     * @return int|NULL
     */
    public function getMinItems()
    {
        return $this->minItems;
    }

    /**
     * Get # of max items allowed
     * @return int|NULL
     */
    public function getMaxItems()
    {
        return $this->maxItems;
    }

    /**
     * Set minimum # of allowed items
     * @param int|NULL $minItems
     * @throws InvalidArgumentException
     * @return self
     */
    public function setMinItems($minItems)
    {
        if (!is_null($minItems) && !is_numeric($minItems)) {
            throw new InvalidArgumentException('minItems must be a valid number or NULL');
        }
        $this->minItems = $minItems ? (int) $minItems : null;
        return $this;
    }

    /**
     * Set maximum # of allowed items
     * @param int|NULL $maxItems
     * @throws InvalidArgumentException
     * @return self
     */
    public function setMaxItems($maxItems)
    {
        if (!is_null($maxItems) && !is_numeric($maxItems)) {
            throw new InvalidArgumentException('maxItems must be a valid number or NULL');
        }
        $this->maxItems = $maxItems ? (int) $maxItems : null;
        return $this;
    }

    /**
     * Toggle sortable list
     * @param bool $sortable
     * @return self
     */
    public function setSortable($sortable)
    {
        $this->sortable = (bool) $sortable;
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
        $this->dummyItem->setTemplate($template);
        return $this;
    }

    /**
     * Check if list is sortable
     * @return bool
     */
    public function isSortable()
    {
        return !empty($this->sortable);
    }

    /**
     * Get values for list items
     * @return array
     */
    public function getValue()
    {
        $items = parent::getValue();
        if (!is_array($items)) {
            return array();
        }
        $items = $this->cutItems($items);
        return array_map(function ($value) {
            $this->dummyItem->setValue($value);
            return $this->dummyItem->getValue();
        }, $items);
    }

    /**
     * Get data for list items
     */
    public function getItems()
    {
        $items = parent::getValue();
        if (!is_array($items)) {
            return array();
        }
        $items = $this->cutItems($items);
        return array_map(function ($value) {
            $this->dummyItem->setValue($value);
            return $this->dummyItem->getItem();
        }, $items);
    }

    /**
     * @see Page_Variable::display
     */
    public function display(PageInterface $page = null, $disabled = false)
    {

        // Add resource resources
        $this->addJavascript($page);

        // Wrap variable
        echo '<div id="' . $this->getId() . '">';

        // Display variable title
        $this->dummyItem->displayTitle();

        // Display list of items
        $name = $this->getField() . '[]';
        $sortable = $this->isSortable();
        echo '<div data-item-list="' . $name . '" class="hidden">';
        if ($items = $this->getItems()) {
            if (!empty($items) && is_array($items)) {
                $count = 0;
                $append = $sortable ? '<a class="handle"></a>' : '';
                foreach ($items as $item) {
                    $item['#'] = ++$count;
                    $this->dummyItem->isRequired($this->isRequired());
                    $this->dummyItem->displayItem($name, $item, $append);
                }
            }
        }
        echo '</div>';

        // Display item form
        $this->dummyItem->displayform($page);

        // Display add button
        if ($this->getMaxItems() > 1) {
            $this->dummyItem->displayButton();
        }

        // Display revert button
        $this->displayRevert();

        // Close wrap
        echo '</div>';
    }

    /**
     * Render revert button
     */
    public function displayRevert()
    {
        $canRevert = $this->dummyItem->canRevert();
        $revertText = $this->dummyItem->getRevertText();
        if ($canRevert && $revertText) {
            $defaultItems = $this->getDefault();
            if ($defaultItems !== $this->getItems()) {
                echo '<a href="#revert-' . $this->getID() . '" class="btn btn--negative" data-list-revert="' . htmlspecialchars(json_encode($defaultItems)) . '">';
                echo Format::htmlspecialchars($revertText);
                echo '</a>';
            }
        }
    }

    /**
     * Cut down items if over allowed max
     * @param array $items
     * @return array
     */
    private function cutItems(array $items)
    {
        $maxItems = $this->getMaxItems();
        if ($maxItems < count($items)) {
            return array_slice($items, 0, $maxItems);
        }
        return $items;
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
        , sortable = <?=json_encode($this->isSortable()); ?>
        , minItems = <?=json_encode($this->getMinItems()); ?>
        , maxItems = <?=json_encode($this->getMaxItems()); ?>
        , createText = <?=json_encode($this->dummyItem->getCreateText()); ?>
        , updateText = <?=json_encode($this->dummyItem->getUpdateText()); ?>
        , previewText = <?=json_encode($this->dummyItem->getPreviewText()); ?>
        , defaultValues = <?=json_encode($this->dummyItem->getDefaults()); ?>
        , isRequired = <?=json_encode($this->isRequired()); ?>
        , field = $list.data('item-list')
    ;

    // Generate text for item preview
    var previewItem = function (item, i) {
        var replace = previewText.match(/{(.[^}]*)}/g);
        if (!replace) return previewText;
        var preview = previewText;
        $.each(replace, function () {
            var field = this.replace(/{|}/g, '');
            // Replace item data
            if (typeof item[field] === 'string') {
                preview = preview.replace(this, item[field]);
            // Show item number
            } else if (field === '#') {
                preview = preview.replace(this, i);
            }
        });
        return preview;
    };

    // Update list when change occurs
    var updateList = function () {
        var $items = $list.find('[data-item-data]')
            , $delete = $items.find('a[data-item-delete]')
            , numItems = $items.length
            , canDelete = !isRequired && (!minItems || $items.length > minItems)
            , canCreate = !maxItems || $items.length < maxItems
        ;
        $delete.toggleClass('hidden', !canDelete);
        $create.toggleClass('hidden', !canCreate);
        $list.toggleClass('hidden', numItems === 0);
        if (numItems === 0) {
            var dummy = field.substr(0, field.length - 2);
            $('<input class="dummy" name="' + dummy + '" value="false" />').appendTo($list);
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
                        if (value === '') value = false;
                        $(this).prop('checked', value == this.value);
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

    // Allow D&D sorting of items
    if (sortable) {
        $list.sortable({
            cursor: 'move',
            handle: '.handle',
            forceHelperSize: true,
            forcePlaceholderSize: true,
            helper: function(e, tr) {
                var $helper = tr.clone();
                var $originals = tr.children();
                $helper.children().each(function(index) {
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            }
        });
    }

    // Create item
    $create.on('click', function () {

        // Update form
        resetForm($form);
        completeForm($form, defaultValues);

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

            // Get # of newly added item
            var $items = $list.find('[data-item-data]');
            var itemNumber = ($items.length || 0) + 1;

            // Append new item to list
            var $item = $('<div class="slides__item" data-item-data>\
                ' + (sortable ? '<a class="handle"></a>' : '') + '\
                <a data-item-update>' + previewItem(itemData, itemNumber) + '</a>\
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

        // Get item number
        var itemNumber = $list.find('a[data-item-update]').index($link);
        if (itemNumber < 0) itemNumber = 0;
        itemNumber++;

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
            var preview = previewItem(itemData, itemNumber);
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

    // Delete item from list
    $list.on('click', 'a[data-item-delete]', function () {
        var $items = $list.find('[data-item-data]');
        if (minItems && $items.length <= minItems) return;
        if (confirm('Are you sure you want to remove this item?')) {
            $(this).closest('[data-item-data]').remove();
            updateList();
        }
    });

    // Revert list items
    $variable.on('click', 'a[data-list-revert]', function () {
        var $link = $(this)
            , items = $link.data('list-revert')
        ;

        // Confirm to revert
        if (!confirm('Are you sure you want to revert? [CHANGES WILL BE LOST]')) {
            return false;
        }

        $list.html('');
        $.each(items, function (i, item) {

            // Replace list with default item
            var $item = $('<div class="slides__item" data-item-data>\
                ' + (sortable ? '<a class="handle"></a>' : '') + '\
                <a data-item-update>' + previewItem(item, i + 1) + '</a>\
                <input type="hidden" name="' + field + '" value="">\
                <a data-item-delete class="hidden">&times;</a>\
            </div>').appendTo($list);

            // Item data
            var json = JSON.stringify(item);
            $item.find(':input').val(json);
            $item.data('item-data', item);
            $item.attr('data-item-data', item);

        });

        // Update list
        $link.remove();
        updateList();
        return false;
    });

    // Update list
    updateList();

})();
/* </script>
*/
<?php

        // Append JavaScript
        $page->addJavascript(ob_get_clean(), 'dynamic', false);
    }
}