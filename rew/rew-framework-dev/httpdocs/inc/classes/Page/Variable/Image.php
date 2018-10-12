<?php

use REW\Core\Interfaces\PageInterface;

/**
 * Page_Variable_Image
 */
class Page_Variable_Image extends Page_Variable
{

    /**
     * Placeholder Text
     * @var string
     */
    protected $placeholder = 'Upload a photo';

    /**
     * Thumbnail URL
     * @var string
     */
    protected $thumbnail;

    /**
     * @see Page_Variable::__construct()
     */
    public function __construct($name, $options = array())
    {
        parent::__construct($name, $options);
        if (!empty($options['placeholder'])) {
            $this->setPlaceholder($options['placeholder']);
        }
        if (!empty($options['thumbnail'])) {
            $this->setThumbnail($options['thumbnail']);
        }
    }

    /**
     * Set placeholder text
     * @param string $placeholder
     * @return self
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * Set thumbnail URL
     * @param string $thumbnail
     * @return self
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /**
     * Disable file uploader
     * @param bool $disabled
     * @return self
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (bool) $disabled;
        return $this;
    }

    /**
     * Get placeholder text
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * Get thumbnail URL
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Return Uploaded File
     * @see Page_Variable::getValue()
     */
    public function getValue($default = true)
    {
        // Return Upload
        if (!empty($default)) {
            $upload = $this->getUpload();
            if (!empty($upload)) {
                return $upload['path'];
            } else {
                unset($this->value);
            }
        }
        // Return Value
        return parent::getValue($default);
    }

    /**
     * Get Uploaded File from CMS Database
     * @throws PDOException
     */
    public function getUpload()
    {
        // Load File from Upload ID
        if (is_numeric($this->value)) {
            // Fetch Upload from Database
            $upload = DB::get('cms')->getCollection('cms_uploads')->getRow($this->value);
            if (!empty($upload)) {
                $upload['path'] = '/uploads/' . $upload['file'];
                // Thumbnail size
                if ($thumbs = $this->getThumbnail()) {
                    $upload['path'] = '/thumbs/' . $thumbs . $upload['path'];
                }
                return $upload;
            }
        }
        // Return Value
        return null;
    }

    /**
     * Check if uploader is disabled
     * @return bool
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * @see Page_Variable::display()
     */
    public function display(PageInterface $page = null, $disabled = false)
    {

        // Field Attributes
        $attrs = ' data-var="' . $this->getName() . '" name="' . $this->getField() . '"';

        // Disabled Field
        $disabled = $this->setDisabled($disabled)->isDisabled() ? ' disabled': '';

        // Get Uploaded File
        $upload = $this->getUpload();

        // Display Title
        $this->displayTitle();

        // Display Field
        echo sprintf('<div id="%s" data-uploader=\'%s\'>', $this->getId(), json_encode($this->getUploaderOptions())) . PHP_EOL;
        if (!empty($upload)) {
            echo '<div class="file-manager">' . PHP_EOL;
            echo '<ul>' . PHP_EOL;
            echo '<li>' . PHP_EOL;
            echo '<div class="wrap">' . PHP_EOL;
            echo '<img src="/thumbs/95x95' . Format::htmlspecialchars($upload['path']) . '" border="0">' . PHP_EOL;
            echo '<input type="hidden"' . $attrs . ' value="' . Format::htmlspecialchars($upload['id']) . '"' . $disabled . '>' . PHP_EOL;
            echo '</div>' . PHP_EOL;
            echo '</li>' . PHP_EOL;
            echo '</ul>' . PHP_EOL;
            echo '</div>' . PHP_EOL;
        }
        echo '</div>' . PHP_EOL;

        // Display Tooltip
        $this->displayTooltip();
    }

    /**
     * Get options for rew_uploader
     * @return array
     */
    public function getUploaderOptions()
    {
        $options = array(
            'disabled'    => $this->isDisabled(),
            'required'    => $this->isRequired(),
            'inputName'   => $this->getField(),
            'multiple'    => false,
            'inputName'   => $this->getField(),
            'multiple'    => false,
            'extraParams' => array(
                'type' => 'variable',
                'name' => $this->getName()
            )
        );
        if ($placeholder = $this->getPlaceholder()) {
            $options['placeholder'] = $placeholder;
        }
        return $options;
    }
}
