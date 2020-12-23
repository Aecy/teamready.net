<?php

namespace App\Core\Type;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType as SymfonyDateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeType extends SymfonyDateTime
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
