<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\Config;

class ConfigFeatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'version',
            ChoiceType::class,
            [
                'choices' => [
                    'mautic.friendlycaptcha.v1' => Config::FC_API_V1,
                    'mautic.friendlycaptcha.v2' => Config::FC_API_V2,
                ],
                'label'      => 'mautic.friendlycaptcha.version',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                ],
                'required'    => false,
                'placeholder' => false,
                'empty_data'  =>  Config::FC_API_V2,
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
                'empty_data'  => Config::FC_EMBED_LEGACY,
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
                'empty_data'  => Config::FC_LOAD_DELAY_TIMEOUT,
            ]
        );
    }
}