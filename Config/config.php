<?php

/*
 * @copyright   2024 Tax Academy Prof. Dr. Wolfgang Kessler GmbH. All rights reserved
 * @author      Daniel Band
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

declare(strict_types=1);

use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\FriendlyCaptchaIntegration;

return [
    'name'        => 'Friendly Captcha',
    'description' => 'Enables Friendly Captcha integration.',
    'version'     => '1.2.0',
    'author'      => 'Daniel Band',
    'services'    => [
        'integrations' => [
            'mautic.integration.friendlycaptcha' => [
                'class'     => FriendlyCaptchaIntegration::class,
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
];
