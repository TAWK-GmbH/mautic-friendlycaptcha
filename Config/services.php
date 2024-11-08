<?php

declare(strict_types=1);

use Mautic\CoreBundle\DependencyInjection\MauticCoreExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()->defaults()->autowire()->autoconfigure()->public();

    $excludes = [];

    $services->load('MauticPlugin\\MauticFriendlyCaptchaBundle\\', '../')->exclude('../{'.implode(',', array_merge(MauticCoreExtension::DEFAULT_EXCLUDES, $excludes)).'}');
    $services->alias('mautic.integration.friendlycaptcha', \MauticPlugin\MauticFriendlyCaptchaBundle\Integration\FriendlyCaptchaIntegration::class);
};
