<?php

use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\Page\BackendInterface;
use REW\Core\Interfaces\Page\Template\EditorInterface;

class Page_Template_Editor implements EditorInterface
{
    /**
     * @var BackendInterface
     */
    private $page;

    /**
     * @var SkinInterface
     */
    private $skin;

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * Page_Template_Editor constructor.
     * @param BackendInterface $page
     * @param SkinInterface $skin
     * @param SettingsInterface $settings
     */
    public function __construct(BackendInterface $page, SkinInterface $skin, SettingsInterface $settings)
    {
        $this->page = $page;
        $this->skin = $skin;
        $this->settings = $settings;
    }

    /**
     * Display Template Picker Form
     * @param string|bool $pageTemplate
     * @param array $pageVariables
     * @return void
     */
    public function displayForm($pageTemplate = false, $pageVariables = array())
    {
        // Available Templates
        $templates = $this->skin->getSelectableTemplates();

        // Use Default Template
        $pageTemplate = !empty($pageTemplate) ? $pageTemplate : $this->skin->getTemplate($this->page);

        // Page Templates
        if (!empty($templates)) {
            echo '<div class="boxed">' . PHP_EOL;

            // Pick Template

            echo '<div class="tplChooser">' . PHP_EOL;
            echo '<h4>Page Template</h4>' . PHP_EOL;
            echo '<div class="tpl_options">' . PHP_EOL;
            foreach ($templates as $template) {
                $thumbnail = $template->getThumb();
                $checked = ($pageTemplate == $template->getName()) ? ' checked' : '';
                echo '<label class="field-label ' . (!empty($checked) ? ' current' : '') . '">' . PHP_EOL;
                echo (!empty($thumbnail) ? '<img src="' . $thumbnail . '" alt="">' : '');
                echo '<strong>' . $template->getTitle() . '</strong>' . PHP_EOL;
                echo '<span>' . $template->getDescription() . '</span>' . PHP_EOL;
                echo '<input type="radio" name="template" value="' . $template->getName() . '"' . $checked . '>' . PHP_EOL;
                echo '</label>' . PHP_EOL;
            }
            echo '</div>' . PHP_EOL;
            echo '</div>' . PHP_EOL;

            // Page Variables
            foreach ($templates as $template) {
                $current = ($pageTemplate == $template->getName());
                echo '<div class="grid variables' . (!empty($current) ? '' : ' hidden') . '" id="variables-' . $template->getName() . '">' . PHP_EOL;

                $variables = $template->getVariables();
                if (!empty($variables) && is_array($variables)) {
                    foreach ($variables as $variable) {
                        // Disabled variable
                        if (!$variable->isEnabled()) {
                            continue;
                        }
                        // Check variable dependency settings
                        if (!$this->checkVariableDependency($variable)) {
                            continue;
                        }
                        // Set Current Value
                        if (!empty($current)) {
                            $value = $pageVariables[$variable->getName()];
                            if (!is_null($value)) {
                                $variable->setValue($value);
                            }
                        }
                        // Children Variables
                        $children = $variable->getChildren();
                        if (!empty($children)) {
                            $variable->setTitleElement('h2');
                        }
                        // Show Field
                        echo '<div' . ($variable->getEnabled() != '1' ? ' data-hideon="' . Format::htmlspecialchars(json_encode($variable->getEnabled())) . '"' : '') . ' class="field">' . PHP_EOL;
                        $variable->display($this->page, empty($current));
                        echo '</div>' . PHP_EOL;
                        // Show Children
                        if (!empty($children)) {
                            $name = $variable->getName();
                            $value = $variable->getValue();
                            foreach ($children as $child) {
                                // Set Current Value
                                if (!empty($current)) {
                                    $val = $pageVariables[$child->getName()];
                                    if (!is_null($val)) {
                                        $child->setValue($val);
                                    }
                                }
                                if ($this->checkVariableDependency($child)) {
                                    $hidden = empty($value) || !$child->isEnabled();
                                    $enabled = $child->getEnabled();
                                    $enabledValue = json_encode($enabled);
                                } else {
                                    $hidden = true;
                                    $enabled = false;
                                    $enabledValue = null;
                                }
                                $enabledAttr = ' data-enabled=' . (is_array($enabled) ? '"' . Format::htmlspecialchars($enabledValue) . '"' : $enabledValue);
                                $classAttr = ' class="field child-' . $name . ($hidden ? ' hidden' : '') . '"';
                                echo '<div' . $classAttr . $enabledAttr . '>' . PHP_EOL;
                                $child->display($this->page, !empty($hidden) || empty($current));
                                echo '</div>' . PHP_EOL;
                            }
                        }
                    }
                }
                echo '</div>' . PHP_EOL;
            }

            echo '</div>' . PHP_EOL;

            // Add required javascript
            $this->requireJavascript();
        }
    }

    /**
     * Add required javascript to page
     * @return void
     */
    protected function requireJavascript()
    {

        // Start JavaScript
        ob_start();

        ?>
        /* <script> */
        (function () {

            // Select Page Template
            var $templates = $('.tpl_options').find('input[name="template"]').on('change', function() {
                var $this = $(this)
                    , value = $this.val()
                    , checked = $this.prop('checked')
                    , $variables = $('#variables-' + value)
                ;
                $this.closest('label').toggleClass('current', checked);
                if (checked) {
                    $templates.not($this).closest('label').removeClass('current');

                    // Enable template's available variables
                    $variables.find(':input').prop('disabled', false);
                    $variables.find(':input[data-var]').trigger('change');
                    $variables.find(':ui-rew_uploader').rew_uploader('enable');

                    // Disable all other template variables
                    var $otherVars = $('.variables').not($variables.removeClass('hidden')).addClass('hidden');
                    $otherVars.find(':ui-rew_uploader').rew_uploader('disable');
                    $otherVars.find(':input').prop('disabled', true);

                } else {
                    $variables.addClass('hidden').find(':input').prop('disabled', true);
                    $variables.find(':ui-rew_uploader').rew_uploader('disable');
                }
                return false;
            });

            // Trigger change on label check (to fix IE11 bug)
            $('.tpl_options').on('click', 'img', function () {
                var $input = $(this).parent().find('input[name="template"]');
                $input.prop('checked', true).trigger('change');
                return false;
            });

            // Toggle Nested Variables
            $('.variables').on('change', ':input[data-var]', function () {
                var $this = $(this)
                    , name = $this.data('var')
                    , $toggle = $('.child-' + name)
                    , $hideOns = $('fieldset[data-hideon]')
                    , value = $this.val()
                    , $diable_options = $('select option[data-disabled]')
                    ;


                if ($this.is(':radio')) value = (value === '1' ? true : false);

                // Disable Options
                if ($diable_options.length > 0) {
                    $diable_options.each(function () {
                        var $child = $(this)
                            , $selector = $child.data('disabled')
                            , find = name + '.' + value
                            , hidden = $child.prop('disabled')
                            ;

                        if ($selector.indexOf(name) < 0) {
                            return;
                        }

                        if ($selector.indexOf('!') == 0) {
                            hidden = find !== $selector;
                        } else {
                            hidden = find === $selector;
                        }
                        $child.prop('disabled', hidden);
                    });
                }

                // Hide On selector code
                if ($hideOns.length > 0) {
                    $hideOns.each(function () {
                        var $child = $(this)
                            , $selector = $child.data('hideon')
                            , hidden = !value || value.length < 1
                            , find = name + '.' + value
                            ;

                        if (typeof $selector === 'string') {
                            hidden = find !== $selector;
                            if (hidden && $selector.indexOf(name) < 0) {
                                return;
                            }
                        } else if ($.isArray($selector)) {
                            hidden = $.inArray(find, $selector) === -1;
                            if (hidden && $.inArray(name, $selector) === -1) {
                                return;
                            }
                        }
                        $child.toggleClass('hidden', hidden);
                        $child.find(':input').prop('disabled', hidden);
                    });
                }

                // Toggle show/hide code
                if ($toggle.length > 0) {
                    $toggle.each(function () {
                        var $child = $(this)
                            , enabled = $child.data('enabled')
                            , hidden = !value || value.length < 1
                            , ctaValue = $('[name="variables[seller][cta]"]:checked').val()
                            ;
                        if (typeof enabled === 'boolean' && enabled === false) {
                            if (typeof value === 'boolean') {
                                hidden = enabled !== value;
                            } else {
                                hidden = true;
                            }
                        } else if (typeof enabled === 'string') {
                            hidden = value !== enabled;
                        } else if ($.isArray(enabled)) {
                            hidden = $.inArray(value, enabled) === -1;
                        }

                        $child.toggleClass('hidden', hidden);
                        $child.find(':input').prop('disabled', hidden);

                        if (ctaValue == 0) {
                            $child.addClass('hidden');
                            $child.find(':input').prop('disabled', true);
                        }
                    });
                }
            });

        })();
        /* </script> */
        <?php

        // Write JavaScript
        $this->page->writeJS(ob_get_clean());
    }

    /**
     * Check variable's dependency settings
     * @param Page_Variable $variable
     * @return bool
     */
    protected function checkVariableDependency(Page_Variable $variable)
    {
        $dependency = $variable->getDependency();
        if (!empty($dependency)) {
            if (is_array($dependency)) {
                list ($type, $cfgKey, $requiredValue) = $dependency;
                if ($type === 'module') {
                    return $this->settings['MODULES'][$cfgKey] === $requiredValue;
                }
                return false;
            }
            return false;
        }
        return true;
    }
}
