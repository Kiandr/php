<?php

namespace REW\Test\Backend\Asset;

use REW\Backend\Asset\Manifest\ManifestFile;
use \Codeception\Test\Unit;

/**
 * ManifestFileTest
 * @package REW\Test
 */
class ManifestFileTest extends Unit
{

    /**
     * @var ManifestFile
     */
    protected $manifest;

    /**
     * @return ManifestFile
     */
    protected function getManifestFile()
    {
        if (!$this->manifest) {
            $file = __DIR__ . '/fixtures/manifest.json';
            $this->manifest = new ManifestFile($file);
        }
        return $this->manifest;
    }

    /**
     * @covers \REW\Backend\Asset\Manifest\ManifestFile::__construct
     */
    public function testManifestConstruct()
    {
        $this->getManifestFile();
    }

    /**
     * @covers \REW\Backend\Asset\Manifest\ManifestFile::__construct
     * @expectedException \REW\Core\Asset\Exception\MissingManifestException
     */
    public function testMissingManifestFile()
    {
        new ManifestFile(__DIR__ . '/fixtures/missing.json');
    }

    /**
     * @covers \REW\Backend\Asset\Manifest\ManifestFile::__construct
     * @expectedException \REW\Core\Asset\Exception\InvalidManifestException
     */
    public function testInvalidManifestFile()
    {
        new ManifestFile(__DIR__ . '/fixtures/invalid.json');
    }

    /**
     * @covers \REW\Backend\Asset\Manifest\ManifestFile::offsetExists
     */
    public function testManifestValueIsset()
    {
        $manifest = $this->getManifestFile();
        $this->assertTrue(isset($manifest['foo.js']));
        $this->assertTrue(isset($manifest['foo.css']));
        $this->assertFalse(isset($manifest['bar.js']));
        $this->assertFalse(isset($manifest['bar.css']));
    }

    /**
     * @covers \REW\Backend\Asset\Manifest\ManifestFile::offsetGet
     */
    public function testManifestValueGetter()
    {
        $manifest = $this->getManifestFile();
        $this->assertSame('foo.abc123.js', $manifest['foo.js']);
        $this->assertSame('foo.abc123.css', $manifest['foo.css']);
    }

    /**
     * @covers \REW\Backend\Asset\Manifest\ManifestFile::offsetSet
     * @expectedException \BadMethodCallException
     */
    public function testManifestReadOnlySetter()
    {
        $manifest = $this->getManifestFile();
        $manifest['foo.js'] = mt_rand();
    }

    /**
     * @covers \REW\Backend\Asset\Manifest\ManifestFile::offsetUnset
     * @expectedException \BadMethodCallException
     */
    public function testManifestReadOnlyUnset()
    {
        $manifest = $this->getManifestFile();
        unset($manifest['foo.js']);
    }

    /**
     * @covers \REW\Backend\Asset\Manifest\ManifestFile::jsonSerialize
     */
    public function testManifestJsonSerialize()
    {
        $manifest = $this->getManifestFile();
        $manifestJson = json_encode($manifest);
        $json = '{"foo.js":"foo.abc123.js","foo.css":"foo.abc123.css"}';
        $this->assertSame($json, $manifestJson);
    }
}
