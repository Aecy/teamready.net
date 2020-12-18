<?php

namespace App\Infrastructure\Type;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType as DateTimeTypeAlias;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeType extends DateTimeTypeAlias
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'html5' => false,
            'widget' => 'single_text',
            'attr' => [
                'is' => 'date-picker'
            ]
        ]);
    }

}
