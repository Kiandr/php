<?php

namespace REW\Core\Asset;

use REW\Core\Asset\Exception\InvalidManifestException;
use REW\Core\Asset\Exception\MissingManifestException;
use REW\Core\Asset\Interfaces\ManifestInterface;
use REW\Core\Asset\Interfaces\LoaderInterface;

class Loader implements LoaderInterface
{

    /**
     * Base path
     * @var string
     */
    protected $basePath;

    /**
     * CSS manifest file
     * (app.css => app.[hash].css)
     * @var ManifestInterface
     */
    protected $styleManifest;

    /**
     * JS manifest file
     * (bundle.js => bundle.[hash].js)
     * @var ManifestInterface
     */
    protected $scriptManifest;

    /**
     * @param string $basePath
     * @param ManifestInterface $styleManifest
     * @param ManifestInterface $scriptManifest
     * @throws \InvalidArgumentException if $basePath doesn't exist
     */
    public function __construct(
        $basePath,
        ManifestInterface $styleManifest = null,
        ManifestInterface $scriptManifest = null
    ) {

        // Require base path to exist
        if (!$this->basePath = realpath($basePath)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid basePath: %s',
                $basePath
            ));
        }

        // Asset manifest files
        $this->styleManifest = $styleManifest;
        $this->scriptManifest = $scriptManifest;
    }

    /**
     * @param string $routePath
     * @return string|NULL
     */
    public function getControllerFile($routePath)
    {
        return $this->checkFileExists(
            'inc/php/pages',
            $routePath,
            'php'
        );
    }

    /**
     * @param string $routePath
     * @return string|NULL
     */
    public function getTemplateFile($routePath)
    {
        return $this->checkFileExists(
            'inc/tpl/pages',
            $routePath,
            'tpl'
        );
    }

    /**
     * @param string $fileName
     * @return string|NULL
     */
    public function getJavascriptFile($fileName)
    {
        $fileName = sprintf('%s.js', $fileName);
        if (isset($this->scriptManifest[$fileName])) {
            return $this->scriptManifest[$fileName];
        }
        return null;
    }

    /**
     * @param string $fileName
     * @return string|NULL
     */
    public function getStylesheetFile($fileName)
    {
        $fileName = sprintf('%s.css', $fileName);
        if (isset($this->styleManifest[$fileName])) {
            return $this->styleManifest[$fileName];
        }
        return null;
    }

    /**
     * @param string $path
     * @param string $route
     * @param string $ext
     * @return string|NULL
     */
    protected function checkFileExists($path, $route, $ext)
    {
        $path = sprintf('%s/%s', $this->basePath, $path);
        $file = sprintf('%s/%s.%s', $path, $route, $ext);
        if (!$file = $this->checkFileExistsInPath($file, $path)) {
            $file = sprintf('%s/%s/default.%s', $path, $route, $ext);
            return $this->checkFileExistsInPath($file, $path);
        }
        return $file;
    }

    /**
     * @param string $file
     * @param string $path
     * @return string|NULL
     */
    protected function checkFileExistsInPath($file, $path)
    {
        if (strpos(realpath($file), realpath($path)) === 0 && is_file($file)) {
            return $file;
        }
        return null;
    }

    /**
     * @return \REW\Core\Asset\Interfaces\ManifestInterface
     */
    public function getJavascriptManifest()
    {
        return $this->scriptManifest;
    }

    /**
     * @return \REW\Core\Asset\Interfaces\ManifestInterface
     */
    public function getStylesheetManifest()
    {
        return $this->styleManifest;
    }
}
