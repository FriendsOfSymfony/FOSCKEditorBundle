<?php

system(sprintf('php %s', escapeshellarg(__DIR__.'/bin/vendors')));

require_once __DIR__.'/'.$_SERVER['SYMFONY'].'/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => array(
        __DIR__.'/'.$_SERVER['SYMFONY'], 
        __DIR__.'/'.$_SERVER['SYMFONY'].'/../tests'
    )
));
$loader->register();

spl_autoload_register(function($class)
{
    if(strpos($class, 'Ivory\\CKEditorBundle\\') === 0) 
    {
        $path = __DIR__.'/../'.implode('/', array_slice(explode('\\', $class), 2)).'.php';

        if(!stream_resolve_include_path($path))
            return false;
        
        require_once $path;
        return true;
    }
});
