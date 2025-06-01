<?php

namespace App\Controller;

use App\Entity\ProduitChoisi;
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
use App\Entity\Produit;
use App\Service\PanierService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;


final class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier_view')]
    public function index(PanierService $panierService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $panierService->getCurrentPanier();
        $total = $panierService->calculateTotal();
// return $this->render('panier/index.html.twig', [
        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'total' => $total,
        ]);
    }
    #[Route('/paiement', name: 'paiement')]
    public function paiement(StripeService $stripeService, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $originalPanier = $user->getPanier();

        // Clone the panier and its ProduitChoisis
        $panier = new Panier();

        foreach ($originalPanier->getProduitChoisis() as $produitChoisiOriginal) {
            $produitChoisiClone = new ProduitChoisi();
            $produitChoisiClone->setProduit($produitChoisiOriginal->getProduit());
            $produitChoisiClone->setQuantity($produitChoisiOriginal->getQuantity());
            $produitChoisiClone->setPanier($panier);
            $entityManager->persist($produitChoisiClone);
            $panier->addProduitChoisi($produitChoisiClone);
        }

        $entityManager->persist($panier);

        // Create the Commande
        $commande = new Commande();
        $commande->setUser($user);
        $commande->setPanier($panier);
        $commande->setStatut(OrderStatus::PENDING);
        $commande->setTotal($panier->getTotal());
        $commande->setDateCommande(new \DateTime());

        $entityManager->persist($commande);
        $entityManager->flush();

        // Clear the original panier safely
        foreach ($originalPanier->getProduitChoisis() as $produitChoisi) {
            $originalPanier->removeProduitChoisi($produitChoisi);
            $entityManager->remove($produitChoisi);
        }
        $entityManager->flush();

        // Stripe checkout
        $session = $stripeService->createCheckoutSession(
            [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int) round($panier->getTotal() * 100),
                    'product_data' => [
                        'name' => 'Nom du produit',
                    ],
                ],
                'quantity' => 1,
            ],
            $this->generateUrl('paiement_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->generateUrl('paiement_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

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
    public function cancel(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $commande = $entityManager->getRepository(Commande::class)->findOneBy(
            ['user' => $user],
            ['dateCommande' => 'DESC']
        );

        /*if ($commande && $commande->getStatut() === OrderStatus::PENDING) {
            $panierClone = $commande->getPanier();

            if ($panierClone) {
                $commande->setTotal($panierClone->getTotal()); 

                foreach ($panierClone->getProduitChoisis() as $produit) {
                    $entityManager->remove($produit);
                }

                $entityManager->remove($panierClone);
            }

            $commande->setStatut(OrderStatus::CANCELLED);
            $commande->setPanier(null); // on détache le panier
            $entityManager->flush();
        }
*/
        if ($commande) {
            $commande->setStatut(OrderStatus::CANCELLED);
            $entityManager->flush();
        }
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
    #[Route('/admin/commande/{id}', name: 'admin_commande_view')]
    public function viewCommande(int $id, CommandeRepository $commandeRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $commande = $commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        return $this->render('dashboard/commande_view.html.twig', [
            'commande' => $commande,
            'panier' => $commande->getPanier(), 
        ]);
    }





    #[Route('/ajouter/{id}', name: 'panier_add')]
    public function add(Produit $produit, Request $request, PanierService $panierService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $quantity = $request->query->getInt('quantity', 1);
        $panierService->addProduitToPanier($produit, $quantity);

        $this->addFlash('success', 'Produit ajouté au panier.');

        return $this->redirectToRoute('panier_view');
    }

    #[Route('/panier/retirer/{id}', name: 'panier_remove')]
    public function remove(Produit $produit, PanierService $panierService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panierService->removeProduitFromPanier($produit);

        $this->addFlash('info', 'Produit retiré du panier.');

        return $this->redirectToRoute('panier_view');
    }
}
