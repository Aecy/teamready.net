<?php

namespace App\Http\Admin\Controller;

use App\Domain\Auth\Event\UserUpdatedEvent;
use App\Domain\Auth\User;
use App\Http\Admin\Data\UserCrudData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends CrudController
{

    protected string $template = 'user';
    protected string $menu = 'user';
    protected string $entity = User::class;
    protected array $events = [
        'update' => UserUpdatedEvent::class,
        'delete' => '',
        'create' => ''
    ];

    /**
     * @Route("/user", name="user_index")
     */
    public function index(): Response
    {
        return $this->crudIndex();
    }

    /**
     * @Route("/user/{user}", name="user_edit")
     */
    public function edit(User $user): Response
    {
        $data = new UserCrudData($user);
        return $this->crudEdit($data);
    }

}
