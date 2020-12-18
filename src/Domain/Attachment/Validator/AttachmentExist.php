<?php

namespace App\Domain\Attachment\Validator;

use Symfony\Component\Validator\Constraint;

class AttachmentExist extends Constraint
{

    public string $message = "No attachment exists with the id: {{ id }}";

}
