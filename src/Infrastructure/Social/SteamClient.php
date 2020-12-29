<?php

namespace App\Infrastructure\Social;

use App\Infrastructure\Social\Resource\SteamResourceOwner;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vikas5914\SteamAuth;

class SteamClient
{

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function redirect(): RedirectResponse
    {
        $steam = $this->steamService();
        return new RedirectResponse($steam->loginUrl());
    }

    public function fetchUser(): ?SteamResourceOwner
    {
        $steam = $this->steamService();
        dd($steam);
        if (!$steam->loggedIn()) {
            return null;
        }
        return new SteamResourceOwner($steam);
    }

    protected function getDomain(): string
    {
        return $_SERVER['APP_URL'];
    }

    protected function getLoginUrl(): string
    {
        return "{$this->getDomain()}{$this->urlGenerator->generate('oauth_steam_check')}";
    }

    protected function getSteamApiKey(): string
    {
        return $_SERVER['STEAM_SECRET'];
    }

    private function loadConfiguration(): array
    {
        return [
            'apikey' => $this->getSteamApiKey(),
            'domainname' => $this->getDomain(),
            'loginpage' => $this->getLoginUrl(), // Returns to last page if not set
            'logoutpage' => '',
            'skipAPI' => false, // true = dont get the data from steam, just return the steamid64
        ];
    }

    private function steamService(): SteamAuth
    {
        return new SteamAuth($this->loadConfiguration());
    }

}
