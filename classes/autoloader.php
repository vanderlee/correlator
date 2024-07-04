<?php

spl_autoload_register(function ($classname) {
    if (!class_exists($classname) && substr($classname, 0, 11) === 'classes\\') {
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
});
