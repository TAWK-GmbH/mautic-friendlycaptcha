<?php

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class FriendlyCaptchaType.
 */
class FriendlyCaptchaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'mode',
            ChoiceType::class,
            [
                'label'      => 'mautic.friendlycaptcha.mode',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control',
                    'tooltip' => 'mautic.friendlycaptcha.mode.tooltip',
                ],
                'choices' => [
                    'mautic.friendlycaptcha.mode.auto' => 'auto',
                    'mautic.friendlycaptcha.mode.focus' => 'focus',
                    'mautic.friendlycaptcha.mode.none' => 'none'
                ],
                'data' => isset($options['data']['mode']) ? $options['data']['mode'] : 'focus',
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'formfield_friendlycaptcha';
    }
}
