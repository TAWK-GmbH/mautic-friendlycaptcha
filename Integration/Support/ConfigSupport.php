<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Integration\Support;

use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormAuthInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormFeatureSettingsInterface;
use MauticPlugin\MauticFriendlyCaptchaBundle\Form\Type\ConfigAuthType;
use MauticPlugin\MauticFriendlyCaptchaBundle\Form\Type\ConfigFeatureType;
use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\FriendlyCaptchaIntegration;

class ConfigSupport extends FriendlyCaptchaIntegration implements ConfigFormInterface, ConfigFormAuthInterface, ConfigFormFeatureSettingsInterface
{
    use DefaultConfigFormTrait;

    public function getDisplayName(): string
    {
        return 'Friendly Captcha';
    }

    public function getAuthConfigFormName(): string
    {
      return ConfigAuthType::class;
    }

    public function getFeatureSettingsConfigFormName(): string
    {
      return ConfigFeatureType::class;
    }
}