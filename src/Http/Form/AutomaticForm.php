<?php

namespace App\Http\Form;

use App\Domain\Auth\User;
use App\Infrastructure\Type\DateTimeType;
use App\Infrastructure\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AutomaticForm extends AbstractType
{

    const TYPES = [
        'string' => TextType::class,
        'bool' => SwitchType::class,
        'int' => NumberType::class,
        'float' => NumberType::class,
        'array' => ChoiceType::class
    ];

    const NAMES = [
        'username' => TextType::class,
        'country' => TextType::class,
        'mailNotification' => SwitchType::class,
        'theme' => TextType::class,
        'avatarFile' => TextType::class,
        'avatarName' => TextType::class,
        'confirmationToken' => TextType::class,
        'password' => TextType::class,
        'email' => EmailType::class,
        'roles' => ChoiceType::class,
        'createdAt' => DateTimeType::class,
        'updatedAt' => DateTimeType::class,
        'bannedAt' => DateTimeType::class,
        'lastActivityAt' => DateTimeType::class,
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $options['data'];
        $reflexion = new \ReflectionClass($data);
        $properties = $reflexion->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $name = $property->getName();
            /** @var \ReflectionNamedType|null $type */
            $type = $property->getType();
            if (null === $type) {
                return;
            }
            if ('roles' === $name) {
                $builder->add($name, ChoiceType::class, [
                    'multiple' => true,
                    'choices' => array_flip(User::$rolesCrud)
                ]);
            } elseif (array_key_exists($name, self::NAMES)) {
                $builder->add($name, self::NAMES[$name], [
                    'required' => false
                ]);
            } elseif (array_key_exists($name, self::TYPES)) {
                $builder->add($name, self::TYPES[$type->getName()], [
                    'required' => !$type->allowsNull() && 'bool' !== $type->getName()
                ]);
            } else {
                throw new \RuntimeException(sprintf('Can find the associed type "%s" file in %s::%s', $type->getName(), get_class($data), $name));
            }
        }
    }

}
