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
            $builder->add(
                'default_embed_type',
                ChoiceType::class,
                [
                    'choices' => [
                        'mautic.friendlycaptcha.legacy' => 'legacy',
                        'mautic.friendlycaptcha.auto' => 'auto',
                        'mautic.friendlycaptcha.manual' => 'manual',
                    ],
                    'label'      => 'mautic.friendlycaptcha.default_embed_type',
                    'help'       => 'mautic.friendlycaptcha.embed_type_tooltip',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'required'    => false,
                    'placeholder' => false,
                    'data'        => isset($data['default_embed_type']) ? $data['default_embed_type'] : 'legacy',
                ]
            );
            $builder->add(
                'load_delay',
                ChoiceType::class,
                [
                    'choices' => [
                        'mautic.friendlycaptcha.timeout' => 'timeout',
                        'mautic.friendlycaptcha.on_script_load' => 'on_script_load',
                    ],
                    'label'      => 'mautic.friendlycaptcha.load_delay',
                    'help'       => 'mautic.friendlycaptcha.load_delay_tooltip',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'required'    => false,
                    'placeholder' => false,
                    'data'        => isset($data['load_delay']) ? $data['load_delay'] : 'timeout',
                ]
            );
        }
    }
}
