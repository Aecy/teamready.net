<?php

namespace App\Http\Admin\Data;

use App\Domain\Auth\User;
use App\Http\Admin\Form\UserForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class UserCrudData implements CrudDataInterface
{

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="3", max="255")
     */
    public string $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email;

    public User $entity;

    public static function makeUser(User $user): self
    {
        $data = new self();
        $data->username = $user->getUsername();
        $data->email = $user->getEmail();
        $data->entity = $user;
        return $data;
    }

    public function hydrate(User $user, EntityManagerInterface $em): User
    {
        return $user
            ->setUsername($this->username)
            ->setEmail($this->email);
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return UserForm::class;
    }

}
