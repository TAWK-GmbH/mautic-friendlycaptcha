<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Integration;

use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\PluginBundle\Integration\AbstractIntegration;

class Config
{
    public const FC_API_V1 = 'v1';
    public const FC_API_V2 = 'v2';
    public const FC_EMBED_LEGACY = 'legacy';
    public const FC_LOAD_DELAY_TIMEOUT = 'timeout';

    private string $siteKey = '';

    private string $secretKey = '';

    private string $version = 'v1';

    private string $embedType = 'legacy';

    private string $loadDelay = 'timeout';

    public function __construct(
        private IntegrationHelper $integrationHelper,
    ) {
        $integrationObject = $integrationHelper->getIntegrationObject(FriendlyCaptchaIntegration::INTEGRATION_NAME);

        if ($integrationObject instanceof AbstractIntegration) {
            $keys            = $integrationObject->getKeys();
            $this->siteKey   = isset($keys['site_key']) ? $keys['site_key'] : '';
            $this->secretKey = isset($keys['secret_key']) ? $keys['secret_key'] : '';
            $this->version   = isset($keys['version']) ? $keys['version'] : $this::class::FC_API_V1;
            $this->embedType = isset($keys['default_embed_type']) ? $keys['default_embed_type'] : $this::class::FC_EMBED_LEGACY;
            $this->loadDelay = isset($keys['load_delay']) ? $keys['load_delay'] : $this::class::FC_LOAD_DELAY_TIMEOUT;
        }
    }

    public function getSiteKey(): string
    {
        return $this->siteKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getEmbedType(): string
    {
        return $this->embedType;
    }

    public function getLoadDelay(): string
    {
        return $this->loadDelay;
    }

    public function isConfigured(): bool
    {
        return !empty($this->siteKey) && !empty($this->secretKey);
    }
}
