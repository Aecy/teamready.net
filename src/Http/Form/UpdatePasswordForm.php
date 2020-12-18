<?php

namespace App\Http\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

class UpdatePasswordForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'first_options' => ['label' => false, 'attr' => ['placeholder' => 'New password', 'autocomplete' => true]],
            'second_options' => ['label' => false, 'attr' => ['placeholder' => 'New confirmed password', 'autocomplete' => true]],
        ]);
    }

}
