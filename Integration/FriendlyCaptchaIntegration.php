<?php

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class FriendlyCaptchaIntegration.
 */
class FriendlyCaptchaIntegration extends AbstractIntegration
{
    public const INTEGRATION_NAME = 'FriendlyCaptcha';

    public function getName()
    {
        return self::INTEGRATION_NAME;
    }

    public function getDisplayName()
    {
        return 'Friendly Captcha';
    }

    public function getIcon(): string
    {
        return 'plugins/MauticFriendlyCaptchaBundle/Assets/img/FriendlyCaptcha.svg';
    }

    public function getAuthenticationType()
    {
        return 'none';
    }

    public function getRequiredKeyFields()
    {
        return [
            'site_key'   => 'mautic.integration.friendlycaptcha.site_key',
            'secret_key' => 'mautic.integration.friendlycaptcha.secret_key',
        ];
    }

    public function appendToForm(&$builder, $data, $formArea): void
    {
        if ('keys' === $formArea) {
            $builder->add(
                'version',
                ChoiceType::class,
                [
                    'choices' => [
                        'mautic.friendlycaptcha.v1' => 'v1',
                        'mautic.friendlycaptcha.v2' => 'v2',
                    ],
                    'label'      => 'mautic.friendlycaptcha.version',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'required'    => false,
                    'placeholder' => false,
                    'data'        => isset($data['version']) ? $data['version'] : 'v1',
                ]
            );
        }
    }
}
