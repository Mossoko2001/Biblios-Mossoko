<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('', name: 'app_book_index', methods: ['GET'])]
    public function index(
        Request $request,
        BookRepository $repository,
        AuthorRepository $authorRepository,
        EditorRepository $editorRepository
    ): Response {
        $search = $request->query->get('q');
        $author = $request->query->get('author') ? (int) $request->query->get('author') : null;
        $editor = $request->query->get('editor') ? (int) $request->query->get('editor') : null;

        $books = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->findBySearch($search, $author, $editor)),
            $request->query->get('page', 1),
            10
        );

        $authors = $authorRepository->findBy([], ['name' => 'ASC']);
        $editors = $editorRepository->findBy([], ['name' => 'ASC']);

        return $this->render('book/index.html.twig', [
            'books' => $books,
            'authors' => $authors,
            'editors' => $editors,
            'current_author' => $author,
            'current_editor' => $editor,
            'current_search' => $search,
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function show(?Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setBook($book);
        $comment->setCreatedAt(new \DateTimeImmutable());
        $comment->setStatus(\App\Enum\CommentStatus::Pending);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été envoyé et sera publié après modération.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
            'comment_form' => $form,
        ]);
    }
}