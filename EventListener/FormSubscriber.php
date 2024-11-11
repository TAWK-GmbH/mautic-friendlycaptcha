<?php

/*
 * @copyright   2024 Tax Academy Prof. Dr. Wolfgang Kessler GmbH. All rights reserved
 * @author      Daniel Band
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

declare(strict_types=1);

namespace MauticPlugin\MauticFriendlyCaptchaBundle\EventListener;

use Mautic\CoreBundle\Translation\Translator;
use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\ValidationEvent;
use Mautic\FormBundle\FormEvents;
use MauticPlugin\MauticFriendlyCaptchaBundle\Form\Type\FriendlyCaptchaType;
use MauticPlugin\MauticFriendlyCaptchaBundle\FriendlyCaptchaEvents;
use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\Config;
use MauticPlugin\MauticFriendlyCaptchaBundle\Service\FriendlyCaptchaClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormSubscriber implements EventSubscriberInterface
{
    public const VALIDATOR_KEY = 'plugin.friendlycaptcha.validator';

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private Config $config,
        private FriendlyCaptchaClient $friendlyCaptchaClient,
        private Translator $translator,
        private LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD                              => ['onFormBuild', 0],
            FriendlyCaptchaEvents::ON_FORM_CUSTOM_FIELD_VALIDATION => ['onFormValidateCustomField', 0],
        ];
    }

    public function onFormBuild(FormBuilderEvent $event)
    {
        if (!$this->config->isConfigured()) {
            $this->logger->error('FriendlyCaptcha: Please configure site_key and secret_key.');

            return;
        }

        $event->addFormField('plugin.friendlycaptcha', [
            'label'          => 'mautic.plugin.actions.friendlycaptcha',
            'formType'       => FriendlyCaptchaType::class,
            'template'       => '@MauticFriendlyCaptcha/Field/friendlycaptcha.twig',
            'builderOptions' => [
                'addShowLabel'     => false,
                'addLeadFieldList' => false,
                'addIsRequired'    => false,
                'addDefaultValue'  => false,
                'addSaveResult'    => true,
            ],
            'site_key' => $this->config->getSiteKey(),
            'version'  => $this->config->getVersion(),
        ]);

        $event->addValidator($this::class::VALIDATOR_KEY, [
            'eventName' => FriendlyCaptchaEvents::ON_FORM_CUSTOM_FIELD_VALIDATION,
            'fieldType' => 'plugin.friendlycaptcha',
        ]);
    }

    public function onFormValidateCustomField(ValidationEvent $event)
    {
        if (!$this->config->isConfigured()) {
            $this->logger->error('FriendlyCaptcha: Please configure site_key and secret_key. Accept form submission anyways.');

            return;
        }

        if ($this->friendlyCaptchaClient->verify($event->getValue())) {
            return;
        }

        $event->failedValidation(null === $this->translator ? 'Captcha verification failed' : $this->translator->trans('mautic.integration.friendlycaptcha.failure_message'));
    }
}
