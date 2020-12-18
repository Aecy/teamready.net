<?php

namespace App\Domain\Attachment\Validator;

use App\Domain\Attachment\Attachment;

class AttachmentNotFound extends Attachment
{

    public function __construct(int $expectedId)
    {
        $this->id = $expectedId;
    }

}
