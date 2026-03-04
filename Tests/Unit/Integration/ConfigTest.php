<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Tests\Unit\Integration;

use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\PluginBundle\Entity\Integration;
use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\Config;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private MockObject $integrationObject;
    private MockObject $integrationHelper;
    private MockObject $integration;

    protected function setUp(): void
    {
        $this->integrationHelper = $this->createMock(IntegrationsHelper::class);
        $this->integrationObject = $this->createMock(BasicIntegration::class);
        $this->integration       = $this->createMock(Integration::class);
        $this->integrationObject->method('getIntegrationConfiguration')->willReturn($this->integration);
        $this->integrationHelper->method('getIntegration')->willReturn($this->integrationObject);
    }

    /**
     * @return array<string, array{0: array<string, string>}>
     */
    public static function getPluginNotConfiguredDataProvider(): array
    {
        return [
            'empty array' => [
                [],
            ],
            'empty secret key' => [
                ['site_key' => 'a', 'secret_key' => ''],
            ],
            'empty site key' => [
                ['site_key' => '', 'secret_key' => 'b'],
            ],
        ];
    }

    /**
     * @dataProvider getPluginNotConfiguredDataProvider
     *
     * @param array<string, string> $options
     */
    public function testPluginNotConfigured(array $options): void
    {
        $this->integration
            ->method('getApiKeys')
            ->willReturn($options);
        $config = new Config($this->integrationHelper);

        $this->assertFalse($config->isConfigured());
    }

    public function testDefault(): void
    {
        $this->integration
            ->method('getApiKeys')
            ->willReturn([]);
        $this->integration
            ->method('getFeatureSettings')
            ->willReturn([]);
        $config = new Config($this->integrationHelper);

        $this->assertFalse(isset($config->getApiKeys()['secret_key']));
        $this->assertFalse(isset($config->getApiKeys()['site_key']));
        $this->assertEquals(Config::FC_API_V2, $config->getVersion());
        $this->assertEquals(Config::FC_EMBED_LEGACY, $config->getEmbedType());
        $this->assertEquals(Config::FC_LOAD_DELAY_TIMEOUT, $config->getLoadDelay());
    }
}
