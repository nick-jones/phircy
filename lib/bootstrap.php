<?php

namespace Phircy;

spl_autoload_register(function($className) {
    if (strpos($className, 'Phircy\\') === 0) {
        require strtr($className, '\\', DIRECTORY_SEPARATOR) . '.php';
    }
});