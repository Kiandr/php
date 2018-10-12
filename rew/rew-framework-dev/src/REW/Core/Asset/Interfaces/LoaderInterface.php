<?php

namespace REW\Core\Asset\Interfaces;

use REW\Core\Asset\Interfaces\ManifestInterface;

interface LoaderInterface
{

    /**
     * @param string $routePath
     * @return string|NULL
     */
    public function getControllerFile($routePath);

    /**
     * @param string $routePath
     * @return string|NULL
     */
    public function getTemplateFile($routePath);

    /**
     * @param string $fileName
     * @return string|NULL
     */
    public function getStylesheetFile($fileName);

    /**
     * @param string $fileName
     * @return string|NULL
     */
    public function getJavascriptFile($fileName);

    /**
     * Returns the stylesheet manifest that was passed into the loader.
     * @return ManifestInterface
     */
    public function getStylesheetManifest();

    /**
     * Returns the javascript manifest that was passed into the loader.
     * @return ManifestInterface
     */
    public function getJavascriptManifest();
}
