<?php

namespace App\Domain\Attachment\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AttachmentExistValidator extends ConstraintValidator
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (! $value instanceof AttachmentNotFound) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ id }}', (string) $value->getId())
            ->addViolation();
    }
}
