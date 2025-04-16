<?php
//copia de autoload manual (en desuso)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';  // Prefijo del espacio de nombres
    $base_dir = __DIR__ . '/../';  

    if (strpos($class, $prefix) === 0) {

        $relative_class = substr($class, strlen($prefix)); // Eliminar el prefijo 'App'
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

     
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
