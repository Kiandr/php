<?php

/**
 * Profile_Trait_Adoptable
 *
 */
trait Profile_Trait_Adoptable
{

    /**
     * Owning parent implementing the Profile_Trait_Parent trait
     * @var mixed
     */
    private $_parent;

    /**
     * Get the owning parent
     * @return mixed
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Set the owning parent
     * @param mixed $parent
     */
    public function setParent($parent)
    {

        // Parent changed - remove from old parent
        if ($parent !== $this->_parent && !empty($this->_parent)) {
            $this->_parent->removeChild($this);
        }

        // Set parent
        $this->_parent = $parent;

        // Add to parent's children
        if (!empty($this->_parent)) {
            $this->_parent->addChild($this);
        }
    }
}
