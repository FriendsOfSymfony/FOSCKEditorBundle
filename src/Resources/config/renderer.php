<?php

declare(strict_types=1);

use FOS\CKEditorBundle\Renderer\CKEditorRenderer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
            ->public()
            ->autowire();

    $services->set('fos_ck_editor.renderer', CKEditorRenderer::class)
        ->args([
            service('fos_ck_editor.builder.json_builder'),
            service('router'),
            service('assets.packages'),
            service('request_stack'),
            service('twig'),
            expr("container.hasParameter('locale') ? parameter('locale') : null"),
        ])
    ;
};
