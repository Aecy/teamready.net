<?php

namespace App\Domain\Attachment;


use App\Domain\Attachment\Validator\AttachmentNotFound;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class AttachmentGenerator
{

    private UploaderHelper $helper;

    public function __construct(UploaderHelper $helper)
    {
        $this->helper = $helper;
    }

    public function generate(?Attachment $attachment): ?string
    {
        if ($attachment === null || $attachment instanceof AttachmentNotFound) {
            return null;
        }
        return $this->helper->asset($attachment);
    }

}
