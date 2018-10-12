<?php

use REW\Core\Interfaces\ResourceInterface;
use REW\Core\Interfaces\ConfigurableInterface;

/**
 * Resource
 * @package REW
 * @subpackage Resource
 */
abstract class Resource implements ConfigurableInterface, ResourceInterface
{

    /**
     * Name
     * @var string
     */
    protected $name;

    /**
     * Directory Path
     * @var string
     */
    protected $path;

    /**
     * Controller
     * @var string
     */
    protected $controller;

    /**
     * Template
     * @var string
     */
    protected $template;

    /**
     * Sources
     * @var array
     */
    protected $sources = array();

    /**
     * Configuration Settings
     * @var array
     */
    protected $config  = array();

    /**
     * Create Resource
     * @param string $path
     */
    public function __construct($path)
    {
        if ($path) {
            if (!$this->name) {
                $this->setName(basename($path));
            }
            $this->setPath($path);
        }
    }

    /**
     * Add Stylesheet
     * @param string $source
     * @param string $group Group Name
     * @param bool $minify
     * @uses Resource::addSource
     */
    public function addStylesheet($source, $group = 'static', $minify = true)
    {
        return $this->addSource(Source_Type::STYLESHEET, $source, $group, $minify);
    }

    /**
     * Add Javascript
     * @param string $source
     * @param string $group Group Name
     * @param bool $minify
     * @param string Use $load Source_Type::LOAD["none"] or Source_Type::LOAD["async"] or Source_Type::LOAD["defer"]
     * @see Source_Type::load
     * @uses Resource::addSource
     */
    public function addJavascript($source, $group = 'static', $minify = true, $load = "none")
    {
        return $this->addSource(Source_Type::JAVASCRIPT, $source, $group, $minify, $load);
    }

    /**
     * Add Source
     * @param string $type Source_Type::JAVASCRIPT or Source_Type::STYLESHEET
     * @param Source|string $source
     * @param string $group
     * @param bool $minify
     * @param string $load Use $load Source_Type::LOAD["none"] or Source_Type::LOAD["async"] or Source_Type::LOAD["defer"]
     * @return Resource
     */
    public function addSource($type, $source, $group = 'static', $minify = true, $load = "none")
    {
        // Load source
        if (!$source instanceof Source) {
            // Skip Empty Source
            if (empty($source)) {
                return $this;
            }
            // External source
            if ($group === 'external') {
                $source = new Source_Link($source, $type, $load);
            } else {
                // Check for File
                $file = strpos($source, '/') === 0 || substr($source, 0, 7) == 'http://' || substr($source, 0, 8) == 'https://' ? false : $this->checkFile($source);
                if (!empty($file)) {
                    $source = $file;
                }
                // Load Source
                $source = Source::load($type, $source, ($group === 'critical'), $load);
                // All links are to be added to the 'external' group
                if ($source instanceof Source_Link) {
                    $group = 'external';
                }
            }
        }
        // Add Source
        if (!empty($source)) {
            $source->setMinified(!$minify);
            $this->sources[$group][] = $source;
        }
        return $this;
    }

    /**
     * Set Name
     * @param string $name
     * @return Resource
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set Directory Path
     * @param string $path
     * @return Resource
     */
    public function setPath($path)
    {
        $this->path = realpath($path);
        return $this;
    }

    /**
     * Get Name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Directory Path
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get Stylesheet Sources
     * @return array
     */
    public function getStylesheets()
    {
        return array_filter($this->getSources(), function ($source) {
            return ($source->getType() == Source_Type::STYLESHEET);
        });
    }

    /**
     * Get Javascript Sources
     * @return array
     */
    public function getJavascripts()
    {
        return array_filter($this->getSources(), function ($source) {
            return ($source->getType() == Source_Type::JAVASCRIPT);
        });
    }

    /**
     * Get Sources
     * @return array
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Get Controller File
     * @return array
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get Template File
     * @return array
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set Template File
     * @param string $template
     * @return void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Display Resource
     * @param bool $return Return Output
     */
    public function display($return = false)
    {
        // Start profile
        $timer = Profile::timer()->stopwatch('<code>' . get_class($this) . '::' . __FUNCTION__ . '</code>')->start();
        // Start Output Buffer
        if ($return) {
            ob_start();
        }
        // Load Controller
        $this->includeFile($this->getController());
        // Build Sources
        $this->buildSources();
        // Load Template
        $this->includeFile($this->getTemplate());
        // End profile
        $timer->stop();
        // Return Output
        if ($return) {
            return ob_get_clean();
        }
    }

    /**
     * Include Resource File
     * @param string $file
     */
    public function includeFile($file)
    {
        $file = $this->checkFile($file);
        if (!empty($file)) {
            include_once $file;
        }
    }

    /**
     * Check If Resource File Exists
     * @param string $file
     * @param string $path
     * @return string|null
     */
    public function checkFile($file, $path = null)
    {
        $path = !is_null($path) ? $path : $this->getPath();
        // Check Absolute Path
        if (strpos(realpath($file), realpath($path)) === 0 && is_file($file)) {
            return $file;
        }
        // Prepend Resource Path
        $file = $path . DIRECTORY_SEPARATOR . $file;
        if (strpos(realpath($file), realpath($path)) === 0 && is_file($file)) {
            return $file;
        }
        return null;
    }

    /**
     * Check If Directory Exists
     * @param string $dir
     * @param string $path
     * @return string|null
     */
    public function checkDir($dir, $path = null)
    {
        $path = !is_null($path) ? $path : $this->getPath();
        $dir = $path . DIRECTORY_SEPARATOR . $dir;
        if (strpos(realpath($dir), realpath($path)) === 0 && is_dir($dir)) {
            return $dir;
        }
        return null;
    }

    /**
     * Build Sources
     */
    public function buildSources()
    {

        // Profile start
        $timer = Profile::timer()->stopwatch('<code>' . __METHOD__ . '</code>')->start();
        if (!empty($this->sources)) {
            // Sources
            $sources = $this->sources;
            $this->sources = array();

            // Source Groups
            $groups = array();

            // Build Sources
            foreach ($sources as $group => $collection) {
                foreach ($collection as $source) {
                    // Source Hash
                    $hash = false;

                    // Source Type
                    $type = $source->getType();

                    // Build Source
                    switch (get_class($source)) {
                        // External Link (Use as Is)
                        case Source::LINK:
                            $hash = md5($source->getLink());
                            break;

                        // Inline Code
                        case Source::CODE:
                            $hash = md5($source->getData());
                            break;

                        // Local File (Build Single Source)
                        case Source::FILE:
                            if ($source->checkExists()) {
                                $hash = md5_file($source->getFile());
                            }
                            break;
                    }

                    // Build as Group
                    if (!empty($hash)) {
                        $ext = $type::$extension;
                        $groups[$ext][$group][$hash] = $source;
                    }
                }
            }

            // Re-Order Source Groups
            $order = array('global', 'external', 'map', 'static', 'dynamic', 'modules', 'page', 'critical');
            foreach ($order as $name) {
                foreach ($groups as $ext => $group) {
                    if (isset($group[$name])) {
                        $reorder = $group[$name];
                        unset($group[$name]);
                        $groups[$ext] = array_merge($group, array($name => $reorder));
                    }
                }
            }

            // Profile details
            if (Profile::getMode() === Profile::PROFILE_MODE_DEVELOPMENT) {
                $timer_details = '';
                foreach ($groups as $ext => $group) {
                    $timer_details .= '<h4>' . $ext . '</h4>';
                    foreach ($group as $name => $sources) {
                        if (empty($sources)) {
                            continue;
                        }
                        $timer_details .= '<h5>' . $name . '</h5><ul>';
                        foreach ($sources as $source) {
                            switch (get_class($source)) {
                                // External Link (Use as Is)
                                case Source::LINK:
                                    $timer_details .= '<li>' . $source->getLink() . '</li>';
                                    break;

                                    // Inline Code
                                case Source::CODE:
                                    $timer_details .= '<li>' . Format::filesize(mb_strlen($source->getData())) . ' of inline source</li>';
                                    break;

                                    // Local File (Build Single Source)
                                case Source::FILE:
                                    if ($source->checkExists()) {
                                        $timer_details .= '<li><code>' . $source->getFile() . '</code></li>';
                                    }
                                    break;
                            }
                        }
                        $timer_details .= '</ul>';
                    }
                }
                $timer->setDetails($timer_details);
            }

            // Inline size
            $inlineGroup = array('dynamic', 'critical', 'static');

            if (Settings::isREW() && isset($_GET['REBUILD_SOURCES'])) {
                $force_rebuild = true;
            }

            // Build Groups (Minify & Cache)
            foreach ($groups as $ext => $group) {
                foreach ($group as $name => $sources) {
                    // Include external sources
                    if ($name === 'external') {
                        $this->sources = array_merge($this->sources, array_values($sources));
                        continue;
                    }

                    // Build unique hash of groups sources
                    $build = Settings::getInstance()->APP_REVISION;
                    $optimized_hash = md5(implode(array_keys($sources)));
                    $optimized_base = $name . '.' . $optimized_hash;
                    $optimized_file = $ext . DIRECTORY_SEPARATOR . $optimized_base . (!empty($build) ? '.' . $build : '') . '.' . $ext;

                    // Build timer
                    $build_timer = Profile::timer()->stopwatch('<code>' . $optimized_base . '.' . $ext . '</code>')->start();

                    // Expire time for cache (365 days)
                    $cacheExpires = (60 * 60 * 24 * 365);

                    // Check cached file
                    $cache = new Cache(array(
                        'name'      => $optimized_file,
                        'expires'   => $cacheExpires
                    ));

                    // Resource type
                    if ($ext == 'js') {
                        $type = Source_Type::JAVASCRIPT;
                        $inlineSize = 1024 * 32; // 32 KB
                    }
                    if ($ext == 'css') {
                        $type = Source_Type::STYLESHEET;
                        $inlineSize = 1024 * 256; // 256 KB
                    }

                    // File is cached
                    if (!isset($force_rebuild) && $cache->checkCache()) {
                        $size = $cache->getSize();

                        // Should should be inline
                        if ($name !== 'defer' && (in_array($name, $inlineGroup) && $size < $inlineSize)) {
                            $this->sources[] = new Source_Code($cache->get(), $type, ($name === 'critical'));

                            // Stop timer and continue to next source
                            $build_timer->setDetails('<code>[CACHED:INLINE]</code> ' . Format::filesize($size))->stop();
                            continue;
                        } else {
                            // Add file from cache
                            $cached = $cache->getPath() . $cache->getName();
                            $newSource = new Source_File($cached, $type);
                            $this->sources[] = $newSource;

                            // Stop timer and continue to next source
                            $build_timer->setDetails('<code>[CACHED]</code> ' . Format::filesize($size))->stop();
                            continue;
                        }
                    }

                    // Get Source Code
                    $code = array();
                    $less = array();
                    $files = array();
                    $order = 0;
                    foreach ($sources as $source) {
                        $type = $source->getType();
                        $minify = false;
                        ++$order;

                        // Stylesheet
                        $is_file = (get_class($source) == Source::FILE);
                        if ($is_file && $type == Source_Type::STYLESHEET) {
                            if ($source->getExtension() == 'less') {
                                $less[$order] = Minify_CSS_Linearizer::linearize($source->getFile());
                            } else {
                                $code[$order] = Minify_CSS_Linearizer::linearize($source->getFile());
                                $minify = $source->getFile();
                            }
                            $files[$source->getFile()] = Format::filesize($source->getSize());

                        // File
                        } elseif ($is_file) {
                            $code[$order] = $source->getData();
                            $files[$source->getFile()] = Format::filesize($source->getSize());
                            $minify = !$source->isMinified(true) ? $source->getFile() : false;

                        // Inline
                        } else {
                            // Assume inline LESS
                            if ($type == Source_Type::STYLESHEET) {
                                $less[$order] = $source->getData();
                            } else {
                                $code[$order] = $source->getData();
                            }
                            $files['inline'] = +$source->getSize();
                            if (!$source->isMinified()) {
                                $minify = 'INLINE';
                            }
                        }

                        // Minify source
                        $block = $code[$order];
                        if ($minify && !empty($block)) {
                            $size_before = mb_strlen($block);
                            $timer_minify = Profile::timer()->stopwatch('<code>' . $type . '::minify</code>')->start();
                            $block = $type::minify($block);
                            $size_after = mb_strlen($block);
                            $percent = number_format(100 - (($size_after / $size_before) * 100), 2);
                            $timer_minify->setDetails('<code>' . $minify . '</code><br>From <strong>' . Format::filesize($size_before) . '</strong> to <strong>' . Format::filesize($size_after) . '</strong> - <strong>' . $percent . '%</strong>')->stop();
                            $code[$order] = $block;
                            unset($block);

                        // Remove /* <script> */ comments
                        } elseif (!empty($block) && $type === Source_Type::JAVASCRIPT) {
                            $code[$order] = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $code[$order]);
                        }
                    }

                    // LESS
                    if (!empty($less)) {
                        reset($less);
                        $less_key = key($less);
                        $code[$less_key] = $type::minify(implode(PHP_EOL, $less), array('less' => true));
                        ksort($code);
                        unset($less);
                    }

                    // No code, next group
                    if (empty($code)) {
                        $build_timer->setDetails('[EMPTY]')->stop();
                        continue;
                    }
                    $code = implode(PHP_EOL, $code);

                    // Save cached resource
                    if ($cache->save($cache->getName(), $code)) {
                        // Should should be inline
                        if (in_array($name, $inlineGroup) || $cache->getSize() < $inlineSize) {
                            $this->sources[] = new Source_Code($cache->get(), $type);
                        } else {
                            // Add source to page
                            $this->sources[] = new Source_File($cache->getPath() . $cache->getName(), $type);
                        }
                    }


                    // Stop timer
                    $build_timer->setDetails('<code>' . $cache->getName() . '</code> <pre>' . print_r($files, true) . '</pre>')->stop();
                }
            }
        }

        // Profile end
        Profile::memory()->snapshot(__METHOD__);
        $timer->stop();
    }

    /**
     * @see ConfigurableInterface::config()
     */
    public function config($option, $value = null)
    {
        // Get Configuration Setting
        if (is_string($option) && is_null($value)) {
            if (!isset($this->config[$option])) {
                return null;
            }
            return $this->config[$option];
        }
        // Set Configuration Setting
        if (is_string($option) && !is_null($value)) {
            $this->config[$option] = $value;
            return $this;
        }
        // Overwrite Configuration
        if (is_array($option)) {
            foreach ($option as $setting => $value) {
                $this->config[$setting] = $value;
            }
            return $this;
        }
    }

    /**
     * @see ConfigurableInterface::getConfig()
     */
    public function getConfig()
    {
        return $this->config;
    }
}
