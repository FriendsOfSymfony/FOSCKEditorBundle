<?php

declare(strict_types=1);

use FOS\CKEditorBundle\Command\CKEditorInstallerCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
            ->public()
            ->autowire();

    $services->set('fos_ck_editor.command.installer', CKEditorInstallerCommand::class)
            ->args([
                service('fos_ck_editor.installer'),
            ])
            ->tag('console.command')
    ;
};
