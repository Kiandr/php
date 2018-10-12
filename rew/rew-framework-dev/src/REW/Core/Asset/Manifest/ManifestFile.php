<?php

namespace REW\Core\Asset\Manifest;

use REW\Core\Asset\Exception\InvalidManifestException;
use REW\Core\Asset\Exception\MissingManifestException;

class ManifestFile extends AbstractManifest
{

    /**
     * @param string $manifestFile
     * @throws MissingManifestException If manifest file does not exist
     * @throws InvalidManifestException If manifest file does not contain valid JSON
     */
    public function __construct($manifestFile)
    {

        // Ensure manifest file exists
        if (!is_file($manifestFile)) {
            throw new MissingManifestException(sprintf(
                'Manifest file not found: %s',
                $manifestFile
            ));
        }

        // Load JSON data from manifest file
        $json = file_get_contents($manifestFile);
        if (($this->data = json_decode($json)) === null) {
            throw new InvalidManifestException(sprintf(
                'Manifest file not valid: %s',
                json_last_error_msg()
            ));
        }

        // Cast stdObject as array
        $this->data = (array) $this->data;
    }
}
