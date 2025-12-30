<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Integration;

use Mautic\IntegrationsBundle\Exception\IntegrationNotFoundException;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use Mautic\PluginBundle\Entity\Integration;

class Config
{
    public const FC_API_V1 = 'v1';
    public const FC_API_V2 = 'v2';
    public const FC_EMBED_LEGACY = 'legacy';
    public const FC_LOAD_DELAY_TIMEOUT = 'timeout';

    public function __construct(
        private IntegrationsHelper $integrationsHelper,
    ) {
    }
    
    public function isPublished(): bool
    {
        try {
            $integration = $this->getIntegrationEntity();

            return (bool) $integration->getIsPublished() ?: false;
        } catch (IntegrationNotFoundException $e) {
            return false;
        }
    }

    public function isConfigured(): bool
    {
        $apiKeys = $this->getApiKeys();

        return !empty($apiKeys['site_key']) && !empty($apiKeys['secret_key']);
    }

    public function getVersion(): string
    {
        $data = $this->getFeatureSettings();
        return $data['version'] ?? Config::FC_API_V2;
    }

    public function getEmbedType(): string
    {
        $data = $this->getFeatureSettings();
        return $data['default_embed_type'] ?? Config::FC_EMBED_LEGACY;
    }

    public function getLoadDelay(): string
    {
        $data = $this->getFeatureSettings();
        return $data['load_delay'] ?? Config::FC_LOAD_DELAY_TIMEOUT;
    }

    public function getApiKeys(): array
    {
        try {
            $integration = $this->getIntegrationEntity();

            return $integration->getApiKeys() ?: [];
        } catch (IntegrationNotFoundException $e) {
            return [];
        }
    }

    public function getFeatureSettings(): array
    {
        try {
            $integration = $this->getIntegrationEntity();

            return $integration->getFeatureSettings() ?: [];
        } catch (IntegrationNotFoundException $e) {
            return [];
        }
    }

    public function getIntegrationEntity(): Integration
    {
        $integrationObject = $this->integrationsHelper->getIntegration(FriendlyCaptchaIntegration::INTEGRATION_NAME);

        return $integrationObject->getIntegrationConfiguration();
    }
}
