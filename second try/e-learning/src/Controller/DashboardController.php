<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard_path')]
    public function index(): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('dashboard/dashboardAdmin.html.twig');
        }
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'user'=>$this->getUser(),
            'classes'=>null
        ]);
    }
}
