<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Panier;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


final class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        $panier = $user->getPanier();
        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($user);
            $entityManager->persist($panier);
            $entityManager->flush();
        }

        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'panier' => $panier,
        ]);
    }


    #[Route('/paiement', name: 'paiement')]
    public function paiement(StripeService $stripeService): Response
    {
        $session = $stripeService->createCheckoutSession([
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => 2000,
                'product_data' => [
                    'name' => 'Nom du produit',
                ],
            ],
            'quantity' => 1,
        ], 
        $this->generateUrl('paiement_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
        $this->generateUrl('paiement_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->redirect($session->url, 303);
    }

    #[Route('/paiement/success', name: 'paiement_success')]
    public function success(): Response
    {
        return $this->render('paiement/success.html.twig');
    }

    #[Route('/paiement/cancel', name: 'paiement_cancel')]
    public function cancel(): Response
    {
        return $this->render('paiement/cancel.html.twig');
    }


}
