<?php

namespace App\Domain\Auth\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Throwable;

class UserNotFoundException extends AuthenticationException
{

    public function __construct()
    {
        parent::__construct('', 0, null);
    }

    public function getMessageKey(): string
    {
        return 'User not found.';
    }

}
