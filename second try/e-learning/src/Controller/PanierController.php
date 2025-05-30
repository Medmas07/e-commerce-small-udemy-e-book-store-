<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Panier;
use App\Entity\Commande;
use App\Service\StripeService;
use App\Enum\OrderStatus;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;


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
    public function paiement(StripeService $stripeService, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $panier = $user->getPanier();

        $commande = new Commande();
        $commande->setUser($user);
        $commande->setPanier($panier);
        $commande->setStatut(OrderStatus::PENDING);
        $commande->setTotal($panier->getTotal()); 
        $commande->setDateCommande(new \DateTime());

        $entityManager->persist($commande);
        $entityManager->flush();

        $session = $stripeService->createCheckoutSession([
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $panier->getTotal(),
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
    public function success(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $commande = $entityManager->getRepository(Commande::class)->findOneBy(
            ['user' => $user],
            ['dateCommande' => 'DESC']
        );

        if ($commande) {
            $commande->setStatut(OrderStatus::PAID);
            $entityManager->flush();
        }

        return $this->render('paiement/success.html.twig');
    }

    #[Route('/paiement/cancel', name: 'paiement_cancel')]
    public function cancel(): Response
    {
        return $this->render('paiement/cancel.html.twig');
    }

    #[Route('/mon-compte/commandes', name: 'mes_commandes')]
    public function commandes( CommandeRepository $commandeRepository): Response
    {
        /** @var User $user */
        $user=$this->getUser();

        $this->denyAccessUnlessGranted('ROLE_USER');
        $commandes = $commandeRepository->findBy(['user' => $user], ['dateCommande' => 'DESC']);

        return $this->render('commands/commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }



}
