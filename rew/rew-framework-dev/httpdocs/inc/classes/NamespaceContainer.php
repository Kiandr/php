<?php

use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\NamespaceContainerInterface;

class NamespaceContainer implements NamespaceContainerInterface
{
    /**
     * @var SkinInterface
     */
    private $skin;

    /**
     * @var array
     */
    private $namespaces = [];

    /**
     * @var bool
     */
    private $sorted = false;

    /**
     * NamespaceContainer constructor.
     * @param SkinInterface $skin
     */
    public function __construct(SkinInterface $skin)
    {
        $this->skin = $skin;

        foreach ($this->skin->getModuleNamespaces() as $absolutePath => $namespace) {
            $this->registerModuleNamespace(static::PRIORITY_NORMAL, $absolutePath, $namespace);
        }
    }

    /**
     * @param int $priority
     * @param string $absolutePath
     * @param string $namespace
     */
    public function registerModuleNamespace($priority, $absolutePath, $namespace)
    {
        if (!isset($this->namespaces[$priority])) {
            $this->namespaces[$priority] = array();
        }

        $this->namespaces[$priority][$absolutePath] = $namespace;
        $this->sorted = false;
    }

    /**
     * Gets registered module namespaces in order from highest to lowest priority.
     * @return array
     */
    public function getModuleNamespaces()
    {
        if (!$this->sorted) {
            $this->sorted = krsort($this->namespaces);
        }

        $namespaces = [];
        foreach ($this->namespaces as $priority => $prioritizedNamespaces) {
            $namespaces = array_merge($namespaces, $prioritizedNamespaces);
        }

        return $namespaces;
    }
}
