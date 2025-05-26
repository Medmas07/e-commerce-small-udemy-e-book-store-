<?php

namespace App\Controller;

use App\Entity\FormateurRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserAccessController extends AbstractController
{
    #[Route('dashboard/demande-formateur', name: 'user_demand')]
    public function requestFormateur(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $existing = $em->getRepository(FormateurRequest::class)->findOneBy(['user' => $user]);
        if ($existing) {
            $this->addFlash('warning', 'Vous avez déjà fait une demande.');
            return $this->redirectToRoute('dashboard_path');
        }

        $demande = new FormateurRequest();
        $demande->setUser($user);

        $em->persist($demande);
        $em->flush();

        $this->addFlash('success', 'Demande envoyée avec succès.');
        return $this->redirectToRoute('dashboard');
    }
    #[Route('/dashboard/info', name: 'user_info')]
    public function Info(): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        return $this->render('userRoles/index.html.twig', [
            'controller_name' => 'UserAccessController',
        ]);
    }
}
