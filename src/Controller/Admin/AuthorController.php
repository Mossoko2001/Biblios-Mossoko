<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/author')]
#[IsGranted('IS_AUTHENTICATED')]
final class AuthorController extends AbstractController
{
    #[Route('', name: 'app_admin_author_index', methods:['GET'])]
    public function index(Request $request, AuthorRepository $repository): Response
    {
        $dates = [];
        if($request->query->has('start')){
            $dates['start'] = $request->query->get('start');
        }
        if($request->query->has('end')){
            $dates['end'] = $request->query->get('end');
        }
        $authors = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->findByDateOfBirth()),
            $request->query->getInt('page', 1),
            8
        );
        return $this->render('admin/author/index.html.twig', [
            'controller_name' => 'AuthorController',
            'authors' => $authors,
        ]);
    }

    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    #[Route('/new', name: 'app_admin_author_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_author_edit', requirements:['id'=> '\d+'], methods: ['GET', 'POST'])]
    public function new( ?Author $author, Request $request, EntityManagerInterface $manager): Response
    {
        if($author){
            $this->denyAccessUnlessGranted('ROLE_EDITION_DE_LIVRE');
        }
        $author ??= new Author(); // If $author is null, create a new Author object
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($author);
            $manager->flush();

            $this->addFlash('success', 'L\'auteur a bien été ajouté !');
            return $this->redirectToRoute('app_admin_author_show', [
                'id' => $author->getId(),
            ]);
        }
        return $this->render('admin/author/new.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name:'app_admin_author_show', requirements:['id' => '\d+'], methods:['GET'])]
    public function show(Author $author): Response{
        return $this->render('admin/author/show.html.twig', [
            'author' => $author,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/delete', name: 'app_admin_author_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function delete(Author $author, EntityManagerInterface $manager): Response
    {
        $manager->remove($author);
        $manager->flush();
        $this->addFlash('success', 'L\'auteur a été supprimé avec succès.');
        return $this->redirectToRoute('app_admin_author_index');
    }
}
