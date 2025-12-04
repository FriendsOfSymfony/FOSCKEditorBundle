<?php

declare(strict_types=1);

use FOS\CKEditorBundle\Builder\JsonBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->set('fos_ck_editor.builder.json_builder', JsonBuilder::class)
        ->args([
            service('property_accessor'),
        ])
    ;
};
