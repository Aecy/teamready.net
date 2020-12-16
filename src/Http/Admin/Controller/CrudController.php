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

abstract class CrudController extends BaseController
{

    protected string $template = '';
    protected string $entity = '';
    protected string $menu = '';
    protected array $events = [
        'update' => '',
        'delete' => '',
        'create' => ''
    ];

    protected EntityManagerInterface $em;
    private PaginatorInterface $paginator;
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
            $data->hydrate($data->getEntity(), $this->em);
            $this->em->flush();
            $this->eventDispatcher->dispatch(new $this->events['update']($data->getEntity()));
            $this->addFlash('success', "Content has been updated");
        }
        return $this->render("admin/{$this->template}/edit.html.twig", [
            'form' => $form->createView(),
            'entity' => $data->getEntity(),
            'menu' => $this->menu
        ]);
    }

    public function getRepository(): EntityRepository
    {
        /** @var EntityRepository $repository */
        $repository = $this->em->getRepository($this->entity);
        return $repository;
    }

}
