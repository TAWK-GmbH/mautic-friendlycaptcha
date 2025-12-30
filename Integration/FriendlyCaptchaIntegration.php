<?php

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class FriendlyCaptchaIntegration.
 */
class FriendlyCaptchaIntegration extends BasicIntegration
{
    public const INTEGRATION_NAME = 'FriendlyCaptcha';

    public const DISPLAY_NAME = 'Friendly Captcha';

    public function getName(): string
    {
        return self::INTEGRATION_NAME;
    }

    public function getDisplayName(): string
    {
        return self::DISPLAY_NAME;
    }

    public function getIcon(): string
    {
        return 'plugins/MauticFriendlyCaptchaBundle/Assets/img/FriendlyCaptcha.svg';
    }
}
