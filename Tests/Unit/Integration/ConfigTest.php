<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Tests\Unit\Integration;

use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{    
    private $integrationObject;
    private $integrationHelper;

    protected function setUp(): void
    {
        $this->integrationHelper = $this->createMock(originalClassName: IntegrationHelper::class);
        $this->integrationObject = $this->createMock(AbstractIntegration::class);
        $this->integrationHelper->method('getIntegrationObject')->willReturn($this->integrationObject);
    }

    public function getPluginNotConfiguredDataProvider(): array
    {
        return [
            'empty array' => [
                []
            ],
            'empty secret key' => [
                ['site_key' => 'a', 'secret_key' => '']
            ],
            'empty site key' => [
                ['site_key' => '', 'secret_key' => 'b']
            ]
        ];
    }

    /**
     * @dataProvider getPluginNotConfiguredDataProvider
     */
    public function testPluginNotConfigured(array $options) {
        $this->integrationObject
            ->method('getKeys')
            ->willReturn($options);
        $config = new Config($this->integrationHelper);

        $this->assertFalse($config->isConfigured());
    }

    public function testDefault() {
        $this->integrationObject
            ->method('getKeys')
            ->willReturn([]);
        $config = new Config($this->integrationHelper);

        $this->assertEquals(null, $config->getSecretKey());
        $this->assertEquals(null, $config->getSiteKey());
        $this->assertEquals('v1', $config->getVersion());
    }
}