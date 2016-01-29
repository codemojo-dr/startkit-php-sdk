<?php

spl_autoload_register(function($class) {
    $prefix = '';

//    if ( ! substr($class, 0, 17) === $prefix) {
//        return;
//    }

    $class = substr($class, strlen($prefix));
    $location = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';

    if (is_file($location)) {
        require_once($location);
    }
});
