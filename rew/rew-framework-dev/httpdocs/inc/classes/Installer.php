<?php

use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\InstallerInterface;

/**
 * Installer utilities
 */
class Installer implements InstallerInterface
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * Installer constructor.
     * @param SettingsInterface $settings
     */
    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get installer file for framework snippet
     * @param string $snippetName Snippet name to match
     * @param bool $subdomain Is subdomain snippet
     * @param NULL|string $skin Skin to check
     * @return string|NULL
     */
    public function getSnippet($snippetName, $subdomain = false, $skin = null)
    {
        if (!$this instanceof self) {
            return call_user_func_array(
                [Container::getInstance()->get(InstallerInterface::class), __FUNCTION__],
                func_get_args()
            );
        }

        $snippetPath = '_snippets' . ($subdomain ? '/subdomain' : null);
        $snippetPaths = $this->_getPaths($skin, $snippetPath);
        foreach ($snippetPaths as $snippetPath) {
            $snippetFile = sprintf('%s/%s.txt', $snippetPath, $snippetName);
            if (file_exists($snippetFile)) {
                return $snippetFile;
            }
        }
        return null;
    }

    /**
     * Get installer files for framework snippets
     * @param bool $subdomain Only subdomain snippets
     * @param NULL|string $skin Skin to check
     * @return array
     */
    public function getSnippets($subdomain = false, $skin = null)
    {
        if (!$this instanceof self) {
            return call_user_func_array(
                [Container::getInstance()->get(InstallerInterface::class), __FUNCTION__],
                func_get_args()
            );
        }

        $snippetFiles = [];
        $snippetPath = '_snippets' . ($subdomain ? '/subdomain' : null);
        $snippetPaths = $this->_getPaths($skin, $snippetPath);
        foreach ($snippetPaths as $snippetPath) {
            $snippets = glob(sprintf('%s/*.txt', $snippetPath));
            if (!empty($snippets) && is_array($snippets)) {
                foreach ($snippets as $snippet) {
                    $snippetName = basename($snippet, '.txt');
                    if (!isset($snippetFiles[$snippetName])) {
                        $snippetFiles[$snippetName] = $snippet;
                    }
                }
            }
        }
        return $snippetFiles;
    }

    /**
     * Get install paths for skin
     * - install/:skin/(/:path)/:locale
     * - install/:skin/(/:path)
     * @param NULL|string $skin
     * @param NULL|string $path
     * @return array
     */
    protected function _getPaths($skin = null, $path = '')
    {
        if (!$this instanceof self) {
            return call_user_func_array(
                [Container::getInstance()->get(InstallerInterface::class), __FUNCTION__],
                func_get_args()
            );
        }

        $paths = [];
        $skin = Skin::getClass($skin);
        $siteLang = $this->settings['LANG'];
        $installDir = $this->settings['DIRS']['INSTALL'];
        while (is_subclass_of($skin, SkinInterface::class)) {
            $installDir = $skin::getInstallDirectory($skin);
            $skinInstall = sprintf('%s%s', $installDir, $path);
            if (is_dir($skinInstall)) {
                $skinInstallLocale = sprintf('%s/%s', $skinInstall, $siteLang);
                if (is_dir($skinInstallLocale)) {
                    $paths[] = $skinInstallLocale;
                }
                $paths[] = $skinInstall;
            }
            $skin = get_parent_class($skin);
        }
        return $paths;
    }
}
