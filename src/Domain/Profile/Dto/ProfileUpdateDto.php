<?php

namespace App\Domain\Profile\Dto;

use App\Core\Validator\Unique;
use App\Domain\Auth\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Unique(entityClass="App\Domain\Auth\User", field="email")
 */
class ProfileUpdateDto
{
    /**
     * @Assert\NotBlank()
     */
    public string $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Country()
     */
    public ?string $country;

    public User $user;
    public bool $mailNotification;
    public bool $useSystemTheme;
    public bool $useDarkTheme;

    public function __construct(User $user)
    {
        $this->email = $user->getEmail();
        $this->country = $user->getCountry();
        $this->user = $user;
        $this->mailNotification = $user->isMailNotification();
        $this->useSystemTheme = $user->getTheme() === null;
        $this->useDarkTheme = $user->getTheme() === 'dark';
    }

    public function getId(): int
    {
        return $this->user->getId() ?: 0;
    }

}
