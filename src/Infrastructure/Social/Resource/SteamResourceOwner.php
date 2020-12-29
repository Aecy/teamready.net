<?php

namespace App\Infrastructure\Social\Resource;

use Vikas5914\SteamAuth;

class SteamResourceOwner
{

    private SteamAuth $steamAuth;

    public function __construct(SteamAuth $steamAuth)
    {
        $this->steamAuth = $steamAuth;
    }

    public function getSteamId(): string
    {
        return $this->steamAuth->steamid;
    }

}
