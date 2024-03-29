<?php

namespace App\Core\Validator;

use App\Http\Admin\Data\CrudDataInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class UniqueValidator extends ConstraintValidator
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param object|null $obj
     * @param Unique|Constraint $constraint
     */
    public function validate($obj, Constraint $constraint)
    {
        if (null === $obj) {
            return;
        }
        if (! $constraint instanceof Unique) {
            throw new \RuntimeException(sprintf('%s cannot validate constraint of %s', self::class, get_class($constraint)));
        }
        if (! method_exists($obj, 'getId')) {
            throw new \RuntimeException(sprintf("%s cannot used object %s cuz the method getId() doesn't exists", self::class, get_class($obj)));
        }
        $accessor = new PropertyAccessor();
        /** @var class-string<stdClass> $entityClass */
        $entityClass = $constraint->entityClass;
        if ($obj instanceof CrudDataInterface) {
            $entityClass = get_class($obj->getEntity());
        }
        $value = $accessor->getValue($obj, $constraint->field);
        $result = $this->em->getRepository($entityClass)->findOneBy([
            $constraint->field => $value
        ]);
        if (null !== $result && (! method_exists($result, 'getId') || $result->getId() !== $obj->getId())) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->atPath($constraint->field)
                ->addViolation();
        }
    }
}
