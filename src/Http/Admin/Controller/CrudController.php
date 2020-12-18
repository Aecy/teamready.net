<?php

namespace App\Http\Admin\Controller;

use App\Domain\Auth\User;
use App\Http\Admin\Data\CrudDataInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * @template E
 *
 * @method \App\Domain\Auth\User getUser()
 */
abstract class CrudController extends BaseController
{

    /** @var class-string<E> */
    protected string $entity = '';
    protected string $template = '';
    protected string $menu = '';
    protected string $routePrefix = '';
    protected string $searchFiled = 'title';
    protected array $events = [
        'update' => '',
        'delete' => '',
        'create' => ''
    ];

    protected EntityManagerInterface $em;
    protected PaginatorInterface $paginator;
    private EventDispatcherInterface $eventDispatcher;
    private RequestStack $request;

    public function __construct(
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $stack
    )
    {
        $this->em = $em;
        $this->paginator = $paginator;
        $this->eventDispatcher = $eventDispatcher;
        $this->request = $stack;
    }

    public function crudIndex(): Response
    {
        $request = $this->request->getCurrentRequest();
        $query = $this->getRepository()->createQueryBuilder('u')->orderBy('u.createdAt', 'DESC');
        if ($request->get('q')) {
            $query = $query->where('u.username LIKE :username')->setParameter('username', "%" . $request->get('q') . "%");
        }
        $page = $request->query->getInt('page', 1);
        $rows = $this->paginator->paginate($query->getQuery(), $page, 25);
        return $this->render("admin/{$this->template}/index.html.twig", [
            'rows' => $rows,
            'page' => $page,
            'menu' => $this->menu
        ]);
    }

    public function crudEdit(CrudDataInterface $data): Response
    {
        $request = $this->request->getCurrentRequest();
        $form = $this->createForm($data->getFormClass(), $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var E $entity */
            $entity = $data->getEntity();
            $old = clone $entity;
            $data->hydrate();
            $this->em->flush();
            if ($this->events['update'] ?? null) {
                $this->eventDispatcher->dispatch(new $this->events['update']($entity, $old));
            }
            $this->addFlash('success', "Content successfully updated");
            return $this->redirectToRoute($this->routePrefix.'_index');
        }
        return $this->render("admin/{$this->template}/edit.html.twig", [
            'form' => $form->createView(),
            'entity' => $data->getEntity(),
            'menu' => $this->menu
        ]);
    }

    public function getRepository(): EntityRepository
    {
        return $this->em->getRepository($this->entity);
    }

}
