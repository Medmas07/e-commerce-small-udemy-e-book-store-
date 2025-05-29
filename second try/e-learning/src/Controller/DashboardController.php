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
        $user=$this->getUser();
        if(!$user->isVerified()){
            return $this->redirectToRoute('app_redirection_process');
        }
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('dashboard/dashboardAdmin.html.twig');
        }

        if (in_array('ROLE_FORMATEUR', $user->getRoles())) {
            return $this->render('dashboard/UserDashboard.html.twig', [
                'user' => $user,
            ]);
        }
        
        return $this->render('dashboard/UserDashboard.html.twig', [
            'controller_name' => 'DashboardController',
            'user'=>$this->getUser(),
            'classes'=>null,

        ]);
    }
}
