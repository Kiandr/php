<?php

/**
 * Profile_Trait_Parent
 *
 */
trait Profile_Trait_Parent
{

    /**
     * Collection of adopted children
     * @var array
     */
    private $_children = array();

    /**
     * Remove a child
     * @param mixed $child
     */
    public function removeChild($child)
    {
        $children = $this->getChildren();
        foreach ($children as $k => $c) {
            if ($c === $child) {
                unset($children[$k]);
            }
        }
        $this->_children = $children;
    }

    /**
     * Add a new child
     * @param mixed $child
     */
    public function addChild($child)
    {
        foreach ($this->getChildren() as $c) {
            if ($c === $child) {
                return;
            }
        }
        $this->_children[] = $child;
    }

    /**
     * Get the parent's children
     * @return array
     */
    public function getChildren()
    {
        return $this->_children;
    }
}
