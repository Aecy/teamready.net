<?php

namespace App\Domain\Profile\Exception;

use App\Domain\Profile\EmailVerification;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TooManyEmailChangeException extends AuthenticationException
{

    private EmailVerification $emailVerification;

    public function __construct(EmailVerification $emailVerification)
    {
        $this->emailVerification = $emailVerification;
        parent::__construct('You already changed your email less than a week ago.', 0, null);
    }

    public function getEmailVerification(): EmailVerification
    {
        return $this->emailVerification;
    }

    public function getMessageKey()
    {
        return 'You already changed your email less than a week ago.';
    }

}
