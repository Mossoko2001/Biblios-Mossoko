<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Enum\CommentStatus;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/comment')]
#[IsGranted('ROLE_ADMIN')]
final class CommentController extends AbstractController
{
    #[Route('', name: 'app_admin_comment_index', methods: ['GET'])]
    public function index(CommentRepository $repository): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $repository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/{id}/approve', name: 'app_admin_comment_approve', methods: ['POST'])]
    public function approve(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $comment->setStatus(CommentStatus::Published);
        $comment->setPublishedAt(new \DateTimeImmutable());
        $entityManager->flush();

        $this->addFlash('success', 'Le commentaire a été approuvé.');
        return $this->redirectToRoute('app_admin_comment_index');
    }

    #[Route('/{id}/reject', name: 'app_admin_comment_reject', methods: ['POST'])]
    public function reject(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $comment->setStatus(CommentStatus::Moderated);
        $entityManager->flush();

        $this->addFlash('success', 'Le commentaire a été rejeté.');
        return $this->redirectToRoute('app_admin_comment_index');
    }
}