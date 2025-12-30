<?php

/*
 * @copyright   2024 Tax Academy Prof. Dr. Wolfgang Kessler GmbH. All rights reserved
 * @author      Daniel Band
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

declare(strict_types=1);

use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\FriendlyCaptchaIntegration;
use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\Support\ConfigSupport;

return [
    'name'        => 'Friendly Captcha',
    'description' => 'Enables Friendly Captcha integration.',
    'version'     => '3.0.0',
    'author'      => 'Daniel Band',
    'services'    => [
        'integrations' => [
            'mautic.integration.friendlycaptcha' => [
                'class'     => FriendlyCaptchaIntegration::class,
                'tags'      => [
                    'mautic.basic_integration',
                ],
            ],
            // Provides the form types to use for the configuration UI
            'mautic.integration.friendlycaptcha.configuration' => [
                'class'     => ConfigSupport::class,
                'arguments' => [],
                'tags'      => [
                    'mautic.config_integration',
                ],
            ],
        ],
    ],
];
