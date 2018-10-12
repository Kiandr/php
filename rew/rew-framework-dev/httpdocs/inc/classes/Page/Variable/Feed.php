<?php

use REW\Core\Interfaces\PageInterface;

/**
 * Page_Variable_Feed
 */
class Page_Variable_Feed extends Page_Variable_Select
{

    /**
     * Use current feed by default
     * @see Page_Variable::__construct()
     */
    public function __construct($name, $options = [])
    {
        $this->setValue(Settings::getInstance()->IDX_FEED);
        parent::__construct($name, $options);
    }

    /**
     * Switch IDX Feed (if not set in $_GET)
     * @see Page_Variable::setValue
     */
    public function setValue($value)
    {
        if (!isset($_GET['feed'])) {
            Util_IDX::switchFeed($value);
        }
        return parent::setValue($value);
    }

    /**
     * Get Valid Options
     * @return array
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->options = [];
            if (!empty(Settings::getInstance()->IDX_FEEDS)) {
                foreach (Settings::getInstance()->IDX_FEEDS as $feed => $settings) {
                    $this->options[] = ['value' => $feed, 'title' => $settings['title']];
                }
            } else {
                $this->options[] = ['value' => Settings::getInstance()->IDX_FEED, 'title' => Settings::getInstance()->IDX_FEED];
            }
        }
        return $this->options;
    }

    /**
     * @see Page_Variable::display
     */
    public function display(PageInterface $page = null, $disabled = false)
    {

        // Default display
        echo '<div id="' . $this->getId() . '">';
        parent::display($page, $disabled);
        echo '</div>';

        // Extra JavaScript
        ob_start();

?>
/* <script> */
(function () {

    // Re-load IDX variables on change
    var $field = $('#<?=$this->getId(); ?>').on('change', 'select', function () {
        var $this = $(this), val = $this.val();

        // Dynamic IDX variables
        var data = {};
        $('.var-idx').each(function () {
            var $var = $(this)
                , id = $var.attr('id')
                , $opt = $var.find('select')
                , value = $opt.val()
            ;
            $opt.replaceWith('Loading...');
            data[id] = value;
        });

        // Update vars
        $.ajax({
            data: { feed: val },
            success: function (html) {
                $('.var-idx', html).each(function () {
                    var $var = $(this)
                        , id = $var.attr('id')
                        , $opt = $('#' + id)
                        , val = data[id]
                    ;
                    $opt.replaceWith($var);
                    if (val) $var.find('select').val(val);
                });
            }
        });

    });

    // Remove if no options available
    if ($field.find('option').length < 1) {
        $field.closest('fieldset').remove();
    }

})();
/* </script> */
<?php

        // Append JavaScript
        $page->writeJS(ob_get_clean());
    }
}