<?php

namespace App\Http\Admin\Data;

use App\Domain\Attachment\Attachment;
use App\Domain\Auth\User;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @property User $entity
 */
class UserCrudData extends AutomaticCrudData
{

    public string $username;

    public string $email;

    public array $roles;

    public ?string $country;

    public ?string $confirmationToken;

    public ?string $theme;

    public ?string $avatarFile;

    public ?string $avatarName;

    public ?bool $mailNotification;

    public ?\DateTimeInterface $updatedAt;

    public ?\DateTimeInterface $createdAt;

    public ?\DateTimeInterface $lastActivityAt;

    public ?\DateTimeInterface $bannedAt;

}
