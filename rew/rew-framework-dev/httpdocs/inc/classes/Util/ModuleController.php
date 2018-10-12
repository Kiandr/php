<?php

use REW\Core\Interfaces\ModuleInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\ModuleControllerInterface;
use REW\Core\Interfaces\NamespaceContainerInterface;
use REW\Core\Interfaces\Util\ModuleControllerInterface as UtilModuleControllerInterface;
use REW\View\Interfaces\FactoryInterface;

class Util_ModuleController implements UtilModuleControllerInterface
{
    /**
     * @var ModuleInterface
     */
    private $module;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var NamespaceContainerInterface
     */
    private $namespaceContainer;

    /**
     * @var FactoryInterface
     */
    private $viewFactory;

    /**
     * ModuleController constructor.
     * @param ModuleInterface $module
     * @param ContainerInterface $container
     * @param NamespaceContainerInterface $namespaceContainer
     * @param FactoryInterface $viewFactory
     */
    public function __construct(
        ModuleInterface $module,
        ContainerInterface $container,
        NamespaceContainerInterface $namespaceContainer,
        FactoryInterface $viewFactory
    ) {
        $this->module = $module;
        $this->container = $container;
        $this->namespaceContainer = $namespaceContainer;
        $this->viewFactory = $viewFactory;
    }

    /**
     * Renders this controller
     * @param array $data
     * @param bool $return
     * @return null|string
     */
    public function render(array $data, $return = false)
    {
        if ($return) {
            ob_start();
        }

        extract($data);

        foreach ($this->module->getRequiredFiles() as $requiredFile) {
            if ($this->viewFactory->exists($requiredFile)) {
                echo $this->viewFactory->render($requiredFile, get_defined_vars());
            } else {
                include $requiredFile;
            }
        }

        if ($return) {
            return ob_get_clean();
        }
        return null;
    }

    /**
     * Gets the parent controller.
     * @param string $controllerClassName The name of the class currently in use.
     * @return ModuleControllerInterface
     * @throws Exception if there is no parent
     */
    public function getParent($controllerClassName)
    {
        $foundMatchingNamespace = false;
        foreach ($this->namespaceContainer->getModuleNamespaces() as $skinNamespace) {
            $namespaceLength = strlen($skinNamespace);

            if (!$foundMatchingNamespace && substr($controllerClassName, 0, $namespaceLength) == $skinNamespace) {
                $foundMatchingNamespace = true;
                $controllerClassName = substr($controllerClassName, $namespaceLength);
            } else if ($foundMatchingNamespace) {
                $parentClass = $skinNamespace . $controllerClassName;
                if (class_exists($parentClass)) {
                    return $this->container->make($parentClass, ['module' => $this->module]);
                }
            }
        }

        throw new Exception('This controller has no parent class');
    }
}
