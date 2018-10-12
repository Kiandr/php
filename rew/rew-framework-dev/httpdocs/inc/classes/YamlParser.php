<?php

use REW\Core\Interfaces\YamlParserInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlParser
 * Simple wrapper for Symfony Yaml parser so it can be used for DI without being referenced directly anywhere but in
 * this wrapper.
 */
class YamlParser implements YamlParserInterface
{
    /**
     * @param string $input
     * @param int $flags
     * @return mixed
     */
    public function parse($input, $flags = 0)
    {
        return Yaml::parse($input, $flags);
    }
}
