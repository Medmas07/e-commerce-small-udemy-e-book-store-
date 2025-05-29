<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RedirectionProcessController extends AbstractController
{
    #[Route('/redirection/process', name: 'app_redirection_process')]
    public function index(): Response
    {

        /** @var User $user */
         $user=$this->getUser();
        if ($user) {
            if ($user->isVerified()) {

                return $this->redirectToRoute('dashboard_path');
            } else {
                return $this->render('redirection_process/access_denied.html.twig', [
                ]);

            }
        }
        return $this->redirectToRoute('app_login');
    }
    }
