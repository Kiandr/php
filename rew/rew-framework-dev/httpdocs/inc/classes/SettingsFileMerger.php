<?php

use REW\Core\Interfaces\YamlParserInterface;
use REW\Core\Interfaces\SettingsFileMergerInterface;
use REW\Core\Interfaces\CacheInterface;

class SettingsFileMerger implements SettingsFileMergerInterface
{

    /**
     *
     * @var integer
     */
    const CACHE_EXPIRES = 300; // 5 Minutes in Seconds

    /**
     * @var YamlParserInterface
     */
    private $parser;

    /**
     * Settings Cache Array
     * @var array
     */
    private $flash = [];

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * SettingsFileMerger constructor.
     * @param YamlParserInterface $parser
     */
    public function __construct(YamlParserInterface $parser, CacheInterface $cache)
    {
        $this->parser = $parser;
        $this->cache = $cache;
    }

    /**
     * Load Cached Settings Config
     * @return array | NULL
     */
    protected function loadCachedConfig($index)
    {

        if ($this->cache instanceof CacheInterface && !empty($index)) {
            $cached = $this->cache->getCache(__CLASS__ . ':' . $index);

            return $cached;
        }

        return null;
    }

    /**
     * Cache's The Parsed Settings Config
     * @param array $event
     */
    protected function cacheConfig($index, array $config)
    {
        if ($this->cache instanceof CacheInterface && !empty($config)) {
            // Cache Event
            $this->cache->setCache(__CLASS__ . ':' . $index, $config, false, self::CACHE_EXPIRES);
        }
    }

    /**
     * @param string $fileName The filename relative to $root
     * @param array $dataStore The array to store data in
     * @return array
     */
    public function merge($fileName, $dataStore = array())
    {

        $index = md5($fileName);

        // If Flash Cached OR In Cache And The Data To Be Merged Is Empty Then Return The Cache
        if ((!empty($this->flash[$index]) || $this->flash[$index] = $this->loadCachedConfig($index)) &&
            empty($dataStore)
        ) {
            return $this->flash[$index];
        }

        // If Not Cached, Get the Contents And Cache It
        if (empty($this->flash[$index])) {
            $this->flash[$index] = $this->parser->parse(file_get_contents($fileName));
            $this->cacheConfig($index, $this->flash[$index]);
        }

        // Merge The Data Store With The Cached Contents
        return $this->recursiveMerge($dataStore, $this->flash[$index]);
    }

    /**
     * Merges an array recursively. Unlike array_merge_recursive, this will replace each level instead of trying to
     * combine.
     * @param array $base
     * @param array $delta
     * @return array
     */
    public function recursiveMerge(array $base, array $delta)
    {
        $merged = $base;

        foreach ($delta as $key => $value) {
            if (!isset($base[$key])) {
                $merged[$key] = $value;
            } else if (is_array($base[$key]) && is_array($value)) {
                $merged[$key] = $this->recursiveMerge($base[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Import and merge all the YAML files specified in $sources and return the resulting array
     * @param array $sources
     * @param string|null $loadLabel The label to load
     * @return array
     */
    public function importAndMergeSources(array $sources, $loadLabel = null)
    {

        $index = md5(serialize($sources));

        if (!empty($this->flash[$index])) {
            return $this->flash[$index];
        } else if ($this->flash[$index] = $this->loadCachedConfig($index)) {
            return $this->flash[$index];
        }

        foreach ($sources as $source) {
            $label = isset($source['label']) ? $source['label'] : null;
            if (!$loadLabel || $label == $loadLabel) {
                if (isset($source['yaml'])) {
                    $sourceFileName = $source['yaml'];

                    if (file_exists($sourceFileName)) {
                        $source = file_get_contents($sourceFileName);
                        if ($settings = $this->parser->parse($source)) {
                            $sources = $this->recursiveMerge($sources, $settings);
                        }
                    }
                } else if (isset($source['php'])) {
                    $sourceFileName = $source['php'];

                    if (file_exists($sourceFileName)) {
                        $config = require $sourceFileName;
                        if (!is_array($config)) {
                            throw new Exception('Failed to load config file.');
                        }
                        $sources = $this->recursiveMerge($sources, $config);
                    }
                }
            }
        }

        $this->flash[$index] = $sources;

        $this->cacheConfig($index, $this->flash[$index]);

        return $this->flash[$index];
    }
}
