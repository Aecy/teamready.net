<?php

namespace App\Domain\Auth\Event;

use App\Domain\Auth\User;

class UserUpdatedEvent
{

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}
