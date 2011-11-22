<?php

system(sprintf('php %s', escapeshellarg(__DIR__.'/bin/vendors')));

require_once __DIR__.'/'.$_SERVER['SYMFONY'].'/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => array(
        __DIR__.'/'.$_SERVER['SYMFONY'], 
        __DIR__.'/'.$_SERVER['SYMFONY'].'/../tests'
    ),
    'Ivory'   => __DIR__.'/../../..'
));
$loader->register();
