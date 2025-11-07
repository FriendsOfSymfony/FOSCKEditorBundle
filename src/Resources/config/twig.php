<?php

declare(strict_types=1);

use FOS\CKEditorBundle\Twig\CKEditorExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
            ->public()
            ->autowire();

    $services->set('fos_ck_editor.twig_extension', CKEditorExtension::class)
        ->args([
            'fos_ck_editor.renderer',
        ])
        ->tag('twig.extension');
};
