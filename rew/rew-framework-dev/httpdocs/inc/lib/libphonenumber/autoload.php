<?php

// Autoloader for \libphonenumber
spl_autoload_register(function ($className) {
	$namespace = 'libphonenumber\\';
	if (strpos($className, $namespace) === 0) {
		$className = substr($className, strlen($namespace));
		$classFile = __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';
		if (file_exists($classFile)) {
			require_once $classFile;
		}
	}
});