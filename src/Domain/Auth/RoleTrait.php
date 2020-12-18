<?php

namespace App\Domain\Auth;

trait RoleTrait
{

    public static array $rolesCrud = [
        1 => 'ROLE_USER',
        2 => 'ROLE_ADMIN',
    ];

}
