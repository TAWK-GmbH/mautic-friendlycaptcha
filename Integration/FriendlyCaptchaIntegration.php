<?php

/*
 * @copyright   2024 Tax Academy Prof. Dr. Wolfgang Kessler GmbH. All rights reserved
 * @author      Daniel Band
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilder;

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

    public function appendToForm(FormBuilder &$builder, array $data, string $formArea)
    {
        if ($formArea === 'keys') {
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
                    'data'=> isset($data['version']) ? $data['version'] : 'v1'
                ]
            );
        }
    }
}
