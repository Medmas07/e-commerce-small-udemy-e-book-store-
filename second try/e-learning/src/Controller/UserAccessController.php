<?php

namespace App\Controller;

use App\Entity\FormateurRequest;
use App\Form\FormateurRequestType;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserAccessController extends AbstractController
{
    #[Route('dashboard/demande-formateur', name: 'user_demand')]
    public function requestFormateur(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $existing = $em->getRepository(FormateurRequest::class)->findOneBy(['user' => $user]);
        if ($existing) {
            $this->addFlash('warning', 'Vous avez déjà fait une demande.');
            return $this->redirectToRoute('dashboard_path');
        }

        $demande = new FormateurRequest();
        $demande->setUser($user);

        $form = $this->createForm(FormateurRequestType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($demande);
            $em->flush();

            $this->addFlash('success', 'Demande envoyée avec succès.');
            return $this->redirectToRoute('dashboard_path');
        }

        return $this->render('userRoles/Demande.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/dashboard/profile', name: 'user_profile')]
    public function editProfile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Profile updated!');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/edit_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
