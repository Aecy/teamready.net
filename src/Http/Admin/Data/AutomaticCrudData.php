<?php

namespace App\Http\Admin\Data;

use App\Http\Form\AutomaticForm;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @IgnoreAnnotation()
 * @template E
 *
 * @property E $entity
 */
abstract class AutomaticCrudData implements CrudDataInterface
{

    protected object $entity;

    public function __construct(object $entity)
    {
        $this->entity = $entity;
        $reflexion = new \ReflectionClass($this);
        $properties = $reflexion->getProperties(\ReflectionProperty::IS_PUBLIC);
        $accessor = new PropertyAccessor();
        foreach ($properties as $property) {
            $name = $property->getName();
            $type = $property->getType();
            if ($type && UploadedFile::class === $type->getName()) {
                continue;
            }
            $accessor->setValue($this, $name, $accessor->getValue($entity, $name));
        }
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function hydrate(object $entity, EntityManagerInterface $em)
    {
        $reflexion = new \ReflectionClass($this);
        dd($reflexion);
    }

    public function getFormClass(): string
    {
        return AutomaticForm::class;
    }

    public function getId(): ?int
    {

    }

}
