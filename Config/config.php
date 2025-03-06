<?php

/*
 * @copyright   2024 Tax Academy Prof. Dr. Wolfgang Kessler GmbH. All rights reserved
 * @author      Daniel Band
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
    'name'        => 'Friendly Captcha',
    'description' => 'Enables Friendly Captcha integration.',
    'version'     => '1.0.1',
    'author'      => 'Daniel Band',

    'routes' => [

    ],

    'services' => [
        'events' => [
            'mautic.friendlycaptcha.event_listener.form_subscriber' => [
                'class'     => \MauticPlugin\MauticFriendlyCaptchaBundle\EventListener\FormSubscriber::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.integration',
                    'mautic.friendlycaptcha.service.friendlycaptcha_client',
                    'mautic.lead.model.lead',
                    'translator'
                ],
            ],
        ],
        'models' => [

        ],
        'others'=>[
            'mautic.friendlycaptcha.service.friendlycaptcha_client' => [
                'class'     => \MauticPlugin\MauticFriendlyCaptchaBundle\Service\FriendlyCaptchaClient::class,
                'arguments' => [
                    'mautic.helper.integration',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.friendlycaptcha' => [
                'class'     => \MauticPlugin\MauticFriendlyCaptchaBundle\Integration\FriendlyCaptchaIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session',
                    'request_stack',
                    'router',
                    'translator',
                    'logger',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                ],
            ],
        ],
    ],
    'parameters' => [

    ],
];
