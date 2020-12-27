<?php

namespace App\Domain\Profile\Exception;

use App\Domain\Profile\EmailVerification;

class TooManyEmailChangeException extends \Exception
{
    public EmailVerification $emailVerification;

    public function __construct(EmailVerification $emailVerification)
    {
        parent::__construct();
        $this->emailVerification = $emailVerification;
    }
}
