<?php

namespace App\Domain\Password\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class TokenExpiredException extends AuthenticationException
{

    public function __construct()
    {
        parent::__construct('', 0, null);
    }

    public function getMessageKey()
    {
        return 'Ongoing password reset.';
    }

}
