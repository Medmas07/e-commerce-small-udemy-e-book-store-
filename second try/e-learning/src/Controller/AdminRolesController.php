<?php

namespace App\Controller;

use App\Entity\FormateurRequest;
use App\Repository\FormateurRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminRolesController extends AbstractController
{
    #[Route('/dashboard/list', name: 'admin_user_list')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        return $this->render('admin_roles/UserDashboard.html.twig', [
            'controller_name' => 'AdminRolesController',
        ]);
    }
    #[Route('/dashboard/addUser', name: 'admin_user_add')]
    public function Add(): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        return $this->render('admin_roles/UserDashboard.html.twig', [
            'controller_name' => 'AdminRolesController',
        ]);
    }
    #[Route('/dashboard/FormatorRequests', name: 'admin_formateur_requests')]
    public function FormatorRequests(FormateurRequestRepository $repo): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $requests = $repo->findBy(['treated' => false]);

        return $this->render('admin_roles/formateur_requests.html.twig', [
            'requests' => $requests
        ]);
    }
    #[Route('/dashboard/accepter-formateur/{id}', name: 'admin_accept_formateur', methods: ['POST'])]
    public function acceptRequest(
        FormateurRequest $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $user = $request->getUser();

        // Ajoute le rôle
        $user->setRoles(array_merge($user->getRoles(), ['ROLE_FORMATEUR']));

        $request->setAccepted(true);
        $request->setTreated(true);

        $em->flush();

        // Envoi de l'email
        $email = (new \Symfony\Component\Mime\Email())
            ->from('admin@monsite.com')
            ->to($user->getEmail())
            ->subject('Votre demande a été acceptée')
            ->text("Félicitations ! Vous êtes maintenant formateur sur notre plateforme.");

        $mailer->send($email);

        $this->addFlash('success', 'Demande acceptée et email envoyé.');
        return $this->redirectToRoute('admin_formateur_requests');
    }




}
