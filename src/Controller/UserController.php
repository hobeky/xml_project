<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\XmlHandlerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'app_user.')]
class UserController extends AbstractController
{
    public function __construct(private XmlHandlerService $xmlHandlerService)
    {}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
//        dd($this->xmlHandlerService->indexLogic());
        return $this->render('user/index.html.twig', [
            'users' => $this->xmlHandlerService->indexLogic()
        ]);

    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->xmlHandlerService->createNewUser($user);

            return $this->redirectToRoute('app_user.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $this->xmlHandlerService->findUser($id),
        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, string $id, XmlHandlerService $xmlHandlerService): Response
    {
        $user = $xmlHandlerService->findUser($id);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $xmlHandlerService->editUserLogic($id, $user);

            return $this->redirectToRoute('app_user.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(string $id): Response
    {
        $this->xmlHandlerService->deleteUser($id);

        return $this->redirectToRoute('app_user.index', [], Response::HTTP_SEE_OTHER);
    }


}
