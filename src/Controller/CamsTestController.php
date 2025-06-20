<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CamsTestController extends AbstractController
{
    #[Route('/camstest', name: 'app_cams_test')]
    public function index(): Response
    {
        return $this->render('cams_test/index.html.twig', [
            'controller_name' => 'CamsTestController',
        ]);
    }
}
