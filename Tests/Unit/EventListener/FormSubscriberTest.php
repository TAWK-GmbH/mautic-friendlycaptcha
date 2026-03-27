<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Tests\Unit\EventListener;

use Mautic\CoreBundle\Translation\Translator;
use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\ValidationEvent;
use Mautic\FormBundle\FormEvents;
use MauticPlugin\MauticFriendlyCaptchaBundle\EventListener\FormSubscriber;
use MauticPlugin\MauticFriendlyCaptchaBundle\FriendlyCaptchaEvents;
use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\Config;
use MauticPlugin\MauticFriendlyCaptchaBundle\Service\FriendlyCaptchaClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class FormSubscriberTest extends TestCase
{
    private FormBuilderEvent $formBuildEvent;
    private MockObject $config;
    private MockObject $fcClient;
    private MockObject $translator;
    private MockObject $logger;
    private FormSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->config          = $this->createMock(Config::class);
        $this->fcClient        = $this->createMock(FriendlyCaptchaClient::class);
        $this->translator      = $this->createMock(Translator::class);
        $this->logger          = $this->createMock(LoggerInterface::class);
        $this->formBuildEvent  = new FormBuilderEvent($this->translator);

        $this->subscriber = new FormSubscriber(
            $this->config,
            $this->fcClient,
            $this->translator,
            $this->logger
        );
    }

    public function testSubscribeEvents(): void
    {
        $events = $this->subscriber->getSubscribedEvents();
        $this->assertEqualsCanonicalizing([
            FormEvents::FORM_ON_BUILD                              => ['onFormBuild', 0],
            FriendlyCaptchaEvents::ON_FORM_CUSTOM_FIELD_VALIDATION => ['onFormValidateCustomField', 0],
        ], $events);
    }

    public function testDontBuildFormWhenNotPublished(): void
    {
        $this->config->method('isPublished')->willReturn(false);

        $this->logger->expects($this->never())->method('error');

        $this->subscriber->onFormBuild($this->formBuildEvent);
    }

    public function testDontBuildFormOnConfigError(): void
    {
        $this->config->method('isConfigured')->willReturn(false);
        $this->config->method('isPublished')->willReturn(true);

        $this->logger->expects($this->once())->method('error');

        $this->subscriber->onFormBuild($this->formBuildEvent);
    }

    public function testOnFormBuild(): void
    {
        $this->config->method('isConfigured')->willReturn(true);
        $this->config->method('isPublished')->willReturn(true);

        $this->config->method('getVersion')->willReturn('a');
        $this->config->method('getApiKeys')->willReturn(['site_key' => 'b']);
        $this->config->method('getEmbedType')->willReturn('legacy');
        $this->config->method('getLoadDelay')->willReturn('timeout');

        $this->subscriber->onFormBuild($this->formBuildEvent);

        $fields = $this->formBuildEvent->getFormFields();
        $this->arrayHasKey('plugin.friendlycaptcha')->evaluate($fields);

        $pluginField = $fields['plugin.friendlycaptcha'];
        $this->arrayHasKey('site_key')->evaluate($pluginField);
        $this->assertEquals($this->config->getApiKeys()['site_key'], $pluginField['site_key']);
        $this->assertEquals($this->config->getVersion(), $pluginField['version']);
        $this->arrayHasKey('version')->evaluate($pluginField);
        $this->arrayHasKey('embed_type')->evaluate($pluginField);
        $this->arrayHasKey('load_delay')->evaluate($pluginField);
        $this->assertEquals($this->config->getEmbedType(), $pluginField['embed_type']);
        $this->assertEquals($this->config->getLoadDelay(), $pluginField['load_delay']);

        $validators = $this->formBuildEvent->getValidators();
        $this->arrayHasKey('plugin.friendlycaptcha')->evaluate($validators);
        $this->assertEquals($validators['plugin.friendlycaptcha'], FriendlyCaptchaEvents::ON_FORM_CUSTOM_FIELD_VALIDATION);
    }

    public function testDontValidateFormOnConfigError(): void
    {
        $this->config->method('isConfigured')->willReturn(false);
        $this->config->method('isPublished')->willReturn(true);

        $this->logger->expects($this->once())->method('error');
        $this->fcClient->expects($this->never())->method('verify');

        $event = $this->createMock(ValidationEvent::class);
        $event->expects($this->never())->method('failedValidation');

        $this->subscriber->onFormValidateCustomField($event);
    }

    public function testOnFormValidateSuccess(): void
    {
        $this->config->method('isConfigured')->willReturn(true);
        $this->config->method('isPublished')->willReturn(true);

        $captchaResult = 'atesttoken';

        $this->fcClient
            ->expects($this->once())
            ->method('verify')
            ->with($captchaResult)
            ->willReturn(true);

        $event = $this->createMock(ValidationEvent::class);
        $event->method('getValue')->willReturn($captchaResult);
        $event->expects($this->never())->method('failedValidation');

        $this->subscriber->onFormValidateCustomField($event);
    }

    public function testOnFormValidateFailure(): void
    {
        $this->config->method('isConfigured')->willReturn(true);
        $this->config->method('isPublished')->willReturn(true);

        $captchaResult = 'atesttoken';

        $this->fcClient
            ->expects($this->once())
            ->method('verify')
            ->with($captchaResult)
            ->willReturn(false);

        $event = $this->createMock(ValidationEvent::class);
        $event->method('getValue')->willReturn($captchaResult);
        $event->expects($this->once())->method('failedValidation');

        $this->subscriber->onFormValidateCustomField($event);
    }
}
