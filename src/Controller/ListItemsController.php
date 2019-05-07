<?php

namespace App\Controller;

use App\Entity\ListItems;
use App\Form\ListItemsType;
use App\Repository\ListItemsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/list/items")
 */
class ListItemsController extends AbstractController
{
    /**
     * @Route("/", name="list_items_index", methods={"GET"})
     */
    public function index(ListItemsRepository $listItemsRepository): Response
    {
        return $this->render('list_items/index.html.twig', [
            'list_items' => $listItemsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="list_items_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $listItem = new ListItems();
        $form = $this->createForm(ListItemsType::class, $listItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($listItem);
            $entityManager->flush();

            return $this->redirectToRoute('list_items_index');
        }

        return $this->render('list_items/new.html.twig', [
            'list_item' => $listItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="list_items_show", methods={"GET"})
     */
    public function show(ListItems $listItem): Response
    {
        return $this->render('list_items/show.html.twig', [
            'list_item' => $listItem,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="list_items_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ListItems $listItem): Response
    {
        $form = $this->createForm(ListItemsType::class, $listItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('list_items_index', [
                'id' => $listItem->getId(),
            ]);
        }

        return $this->render('list_items/edit.html.twig', [
            'list_item' => $listItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="list_items_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ListItems $listItem): Response
    {
        if ($this->isCsrfTokenValid('delete'.$listItem->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($listItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('list_items_index');
    }
}
