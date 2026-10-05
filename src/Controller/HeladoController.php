<?php

namespace App\Controller;

use App\Entity\Helado;
use App\Form\HeladoType;
use App\Repository\HeladoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panel/helado')]
final class HeladoController extends AbstractController
{
    #[Route(name: 'app_helado_index', methods: ['GET'])]
    public function index(HeladoRepository $heladoRepository): Response
    {
        return $this->render('helado/index.html.twig', [
            'helados' => $heladoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_helado_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $helado = new Helado();
        $form = $this->createForm(HeladoType::class, $helado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($helado);
            $entityManager->flush();

            return $this->redirectToRoute('app_helado_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('helado/new.html.twig', [
            'helado' => $helado,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_helado_show', methods: ['GET'])]
    public function show(Helado $helado): Response
    {
        return $this->render('helado/show.html.twig', [
            'helado' => $helado,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_helado_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Helado $helado, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HeladoType::class, $helado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_helado_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('helado/edit.html.twig', [
            'helado' => $helado,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_helado_delete', methods: ['POST'])]
    public function delete(Request $request, Helado $helado, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$helado->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($helado);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_helado_index', [], Response::HTTP_SEE_OTHER);
    }
}
