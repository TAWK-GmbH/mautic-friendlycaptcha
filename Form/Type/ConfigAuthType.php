<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigAuthType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $siteKey = null;
        $secretKey = null;

        $integration = $options['integration'];
        if ($integration && $integration->getIntegrationConfiguration()) {
            $creds = $integration->getIntegrationConfiguration()->getApiKeys();
            $siteKey = $creds['site_key'] ?? null;
            $secretKey = $creds['secret_key'] ?? null;
        }

        $builder->add(
          'site_key',
          TextType::class,
          [
              'label'      => 'mautic.integration.friendlycaptcha.site_key',
              'label_attr' => ['class' => 'control-label'],
              'required'   => true,
              'attr'       => [
                  'class'   => 'form-control',
              ],
              'empty_data' => $siteKey,
          ]
        );

        $builder->add(
          'secret_key',
          TextType::class,
          [
              'label'      => 'mautic.integration.friendlycaptcha.secret_key',
              'label_attr' => ['class' => 'control-label'],
              'required'   => true,
              'attr'       => [
                  'class'   => 'form-control',
              ],
              'empty_data' => $secretKey,
          ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'integration' => null
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'integration_friendlycaptcha';
    }
}