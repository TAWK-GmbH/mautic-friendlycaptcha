<?php

/*
 * @copyright   2024 Tax Academy Prof. Dr. Wolfgang Kessler GmbH. All rights reserved
 * @author      Daniel Band
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticFriendlyCaptchaBundle\EventListener;

use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\ValidationEvent;
use Mautic\FormBundle\FormEvents;
use Mautic\LeadBundle\Event\LeadEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticFriendlyCaptchaBundle\Form\Type\FriendlyCaptchaType;
use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\FriendlyCaptchaIntegration;
use MauticPlugin\MauticFriendlyCaptchaBundle\FriendlyCaptchaEvents;
use MauticPlugin\MauticFriendlyCaptchaBundle\Service\FriendlyCaptchaClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FormSubscriber implements EventSubscriberInterface
{
    const MODEL_NAME_KEY_LEAD = 'lead.lead';

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var FriendlyCaptchaClient
     */
    protected $friendlyCaptchaClient;

    /**
     * @var string
     */
    protected $siteKey;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var boolean
     */
    private $friendlyCaptchaClientIsConfigured = false;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string|null
     */
    private $version;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param IntegrationHelper        $integrationHelper
     * @param FriendlyCaptchaClient    $friendlyCaptchaClient
     * @param LeadModel                $leadModel
     * @param TranslatorInterface      $translator
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        IntegrationHelper $integrationHelper,
        FriendlyCaptchaClient $client,
        LeadModel $leadModel,
        TranslatorInterface $translator
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->friendlyCaptchaClient = $client;
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
        $this->leadModel = $leadModel;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD         => ['onFormBuild', 0],
            FriendlyCaptchaEvents::ON_FORM_VALIDATE => ['onFormValidate', 0],
        ];
    }

    /**
     * @param FormBuilderEvent $event
     *
     * @throws \Mautic\CoreBundle\Exception\BadConfigurationException
     */
    public function onFormBuild(FormBuilderEvent $event)
    {
        if (!$this->friendlyCaptchaClientIsConfigured) {
            return;
        }

        $event->addFormField('plugin.friendlycaptcha', [
            'label'          => 'mautic.plugin.actions.friendlycaptcha',
            'formType'       => FriendlyCaptchaType::class,
            'template'       => 'MauticFriendlyCaptchaBundle:Integration:friendlycaptcha.html.php',
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
            'eventName' => FriendlyCaptchaEvents::ON_FORM_VALIDATE,
            'fieldType' => 'plugin.friendlycaptcha',
        ]);
    }

    /**
     * @param ValidationEvent $event
     */
    public function onFormValidate(ValidationEvent $event)
    {
        if (!$this->friendlyCaptchaClientIsConfigured) {
            return;
        }

        if ($this->friendlyCaptchaClient->verify($event->getValue())) {
            return;
        }

        $event->failedValidation($this->translator === null ? 'FriendlyCaptchaClient was not successful.' : $this->translator->trans('mautic.integration.friendlycaptcha.failure_message'));

        $this->eventDispatcher->addListener(LeadEvents::LEAD_POST_SAVE, function (LeadEvent $event) {
            if ($event->isNew()){
                $this->leadModel->deleteEntity($event->getLead());
            }
        }, -255);
    }
}
