<?php

/*
 * @copyright   2024 Tax Academy Prof. Dr. Wolfgang Kessler GmbH. All rights reserved
 * @author      Daniel Band
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

declare(strict_types=1);

namespace MauticPlugin\MauticFriendlyCaptchaBundle\EventListener;

use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\ValidationEvent;
use Mautic\FormBundle\FormEvents;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticFriendlyCaptchaBundle\Form\Type\FriendlyCaptchaType;
use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\FriendlyCaptchaIntegration;
use MauticPlugin\MauticFriendlyCaptchaBundle\FriendlyCaptchaEvents;
use MauticPlugin\MauticFriendlyCaptchaBundle\Service\FriendlyCaptchaClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Mautic\CoreBundle\Translation\Translator;

class FormSubscriber implements EventSubscriberInterface
{
    protected string $siteKey;

    protected string $secretKey;

    private $friendlyCaptchaClientIsConfigured = false;

    private string $version;

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private IntegrationHelper $integrationHelper,
        private FriendlyCaptchaClient $friendlyCaptchaClient,
        private Translator $translator
    ) {
        $integrationObject     = $integrationHelper->getIntegrationObject(FriendlyCaptchaIntegration::INTEGRATION_NAME);
        
        if ($integrationObject instanceof AbstractIntegration) {
            $keys            = $integrationObject->getKeys();
            $this->siteKey   = isset($keys['site_key']) ? $keys['site_key'] : null;
            $this->secretKey = isset($keys['secret_key']) ? $keys['secret_key'] : null;
            $this->version   = isset($keys['version']) ? $keys['version'] : null;

            if ($this->siteKey && $this->secretKey) {
                $this->friendlyCaptchaClientIsConfigured = true;
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD => ['onFormBuild', 0],
            FriendlyCaptchaEvents::ON_FORM_CUSTOM_FIELD_VALIDATION => ['onFormValidateCustomField', 0],
        ];
    }

    public function onFormBuild(FormBuilderEvent $event)
    {
        if (!$this->friendlyCaptchaClientIsConfigured) {
            return;
        }

        $event->addFormField('plugin.friendlycaptcha', [
            'label'          => 'mautic.plugin.actions.friendlycaptcha',
            'formType'       => FriendlyCaptchaType::class,
            'template'       => '@MauticFriendlyCaptcha/Field/friendlycaptcha.twig',
            'builderOptions' => [
                'addShowLabel' => false,
                'addLeadFieldList' => false,
                'addIsRequired'    => false,
                'addDefaultValue'  => false,
                'addSaveResult'    => true,
            ],
            'site_key' => $this->siteKey,
            'version'  => $this->version,
        ]);

        $event->addValidator('plugin.friendlycaptcha.validator', [
            'eventName' => FriendlyCaptchaEvents::ON_FORM_CUSTOM_FIELD_VALIDATION,
            'fieldType' => 'plugin.friendlycaptcha',
        ]);
    }

    public function onFormValidateCustomField(ValidationEvent $event)
    {
        if (!$this->friendlyCaptchaClientIsConfigured) {
            return;
        }

        if ($this->friendlyCaptchaClient->verify($event->getValue())) {
            return;
        }

        $event->failedValidation($this->translator === null ? 'FriendlyCaptchaClient was not successful.' : $this->translator->trans('mautic.integration.friendlycaptcha.failure_message'));
    }
}
