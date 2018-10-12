<?php
/**
 * Config.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * Configuration utility class to handle config items and extend or expose the items of it.
 *
 * @package MOXMAN_Util
 */
class MOXMAN_Util_Config extends MOXMAN_Util_NameValueCollection {
	/**
	 * Exports the config to the public by checking the allow_export property. This property will tell
	 * what items is to be expored or now.
	 *
	 * @param string $groups Comma separated list of groups to export.
	 * @return Array Name/value array with config items that got expored.
	 */
	public function export($groups = "*") {
		$groups = explode(",", $groups);
		$outputConfig = array();

		foreach (array_keys($this->items) as $key) {
			$pos = strpos($key, ".allow_export");
			if ($pos !== false) {
				$prefix = substr($key, 0, $pos);

				if ($groups[0] === '*' || in_array($prefix, $groups)) {
					$allowed = $this->items[$prefix . ".allow_export"];

					if ($allowed === '*') {
						foreach (array_keys($this->items) as $name) {
							if (strpos($name, $prefix) === 0) {
								if (strpos($name, ".allow_export") === false) {
									$outputConfig[$name] = $this->items[$name];
								}
							}
						}
					} else {
						$names = explode(',', $allowed);
						foreach ($names as $name) {
							if (strpos($name, ".allow_export") === false) {
								$exportKey = $prefix . "." . $name;
								$outputConfig[$exportKey] = $this->items[$exportKey];
							}
						}
					}
				}
			}
		}

		return $outputConfig;
	}

	/**
	 * Replaces the specified variable by name with the specfied value.
	 *
	 * @param string $name Variable name to replace in config values.
	 * @param string $value Value for the variable.
	 */
	public function replaceVariable($name, $value) {
		$search = '${' . $name . '}';

		foreach ($this->items as $configKey => $configValue) {
			if (is_string($configValue)) {
				$this->items[$configKey] = str_replace($search, $value, $configValue);
			}
		}
	}

	/**
	 * Returns a new config object based on the specified files path.
	 * This will match the file path with the path.overrides in the config.
	 *
	 * @param MOXMAN_Vfs_IFile $file File to match agains path.overrides.
	 * @return MOXMAN_Util_Config New path specific config instance.
	 */
	public function getFileConfig(MOXMAN_Vfs_IFile $file) {
		$config = new MOXMAN_Util_Config($this->items);
		$pathOverrides = $this->get("filesystem.directories");
		if (is_array($pathOverrides) && !empty($pathOverrides)) {
			if ($file->isFile()) {
				$file = $file->getParentFile();
				if (!$file) {
					return $config;
				}
			}

			$path = $file->getPublicPath();
			foreach ($pathOverrides as $pattern => $overrides) {
				if (strpos($pattern, 'regexp:') === 0) {
					// regexp:/pattern/
					if (preg_match(substr($pattern, 7), $path)) {
						$config->extend($overrides);
					}
				} else {
					foreach (explode(',', $pattern) as $pattern) {
						if (strpos($pattern, '/') === false) {
							// Directory name
							$pattern = "/\\/" . preg_quote($pattern) . "(\\/|$)/";
							if (preg_match($pattern, $path)) {
								$config->extend($overrides);
							}
						} else {
							if (strrchr($pattern, '/') === '/') {
								$pattern = substr($pattern, 0, strlen($pattern) - 1);
							}

							if (preg_match("/\\/\\.$/", $pattern)) {
								// Directory path with /. at the end
								if ($path === substr($pattern, 0, strlen($pattern) - 2)) {
									$config->extend($overrides);
								}
							} else if ($path === $pattern || strpos($path, $pattern . '/') === 0) {
								// Directory path
								$config->extend($overrides);
							}
						}
					}
				}
			}
		}

		return $config;
	}
}

?>