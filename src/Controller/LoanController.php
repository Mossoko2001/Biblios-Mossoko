<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Loan;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/loan')]
class LoanController extends AbstractController
{
    #[Route('/', name: 'app_loan_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(LoanRepository $loanRepository): Response
    {
        $user = $this->getUser();
        return $this->render('loan/index.html.twig', [
            'active_loans' => $loanRepository->findUserActiveLoans($user),
            'loan_history' => $loanRepository->findUserLoanHistory($user),
        ]);
    }

    #[Route('/borrow/{id}', name: 'app_loan_borrow', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function borrow(Book $book, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        // Vérifier si l'utilisateur n'a pas déjà emprunté ce livre
        $existingLoan = $entityManager->getRepository(Loan::class)->findOneBy([
            'user' => $user,
            'book' => $book,
            'status' => 'borrowed'
        ]);

        if ($existingLoan) {
            $this->addFlash('error', 'Vous avez déjà emprunté ce livre.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        $loan = new Loan();
        $loan->setUser($user)
             ->setBook($book);

        $entityManager->persist($loan);
        $entityManager->flush();

        $this->addFlash('success', 'Livre emprunté avec succès.');
        return $this->redirectToRoute('app_loan_index');
    }

    #[Route('/return/{id}', name: 'app_loan_return', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function return(Loan $loan, EntityManagerInterface $entityManager): Response
    {
        if ($loan->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas retourner ce livre.');
        }

        $loan->markAsReturned();
        $entityManager->flush();

        $this->addFlash('success', 'Livre retourné avec succès.');
        return $this->redirectToRoute('app_loan_index');
    }

    #[Route('/admin', name: 'app_loan_admin', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(LoanRepository $loanRepository): Response
    {
        return $this->render('loan/admin.html.twig', [
            'current_loans' => $loanRepository->findCurrentLoans(),
            'overdue_loans' => $loanRepository->findOverdueLoans(),
        ]);
    }
}