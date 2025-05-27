<?php

namespace App\Controller;

use App\Entity\FormateurRequest;
use App\Entity\User;
use App\Form\UserTypeForm;
use App\Repository\FormateurRequestRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    #[Route('/dashboard/accepter-formateur/{id}', name: 'admin_formateur_accept', methods: ['POST'])]
    public function acceptRequest(
        FormateurRequest $requestEntity,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($requestEntity->isTreated()) {
            $this->addFlash('warning', 'Cette demande a déjà été traitée.');
            return $this->redirectToRoute('admin_formateur_requests');
        }

        $requestEntity->setAccepted(true);
        $requestEntity->setTreated(true);

        // Add ROLE_FORMATEUR to the user
        $user = $requestEntity->getUser();
        $roles = $user->getRoles();


        if (!in_array('ROLE_FORMATEUR', $roles)) {
            $roles[] = 'ROLE_FORMATEUR';
            $user->setRoles($roles);
        }

        $em->flush();

        // Send acceptance email
        $email = (new Email())
            ->from('noreply@example.com')
            ->to($user->getEmail())
            ->subject('Votre demande de formateur est acceptée')
            ->html('<p>Bonjour '.$user->getFirstname().',</p><p>Votre demande pour devenir formateur a été acceptée. Vous avez désormais accès aux outils nécessaires.</p>');

        $mailer->send($email);

        $this->addFlash('success', 'Demande acceptée et email envoyé.');

        return $this->redirectToRoute('admin_formateur_requests');
    }
    #[Route('/dashboard/accepter-formateur/{id}/refuse', name: 'admin_formateur_refuse', methods: ['POST'])]
    public function refuseRequest(
        FormateurRequest $requestEntity,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($requestEntity->isTreated()) {
            $this->addFlash('warning', 'Cette demande a déjà été traitée.');
            return $this->redirectToRoute('admin_formateur_requests');
        }

        $requestEntity->setAccepted(false);
        $requestEntity->setTreated(true);

        $em->flush();

        // Send refusal email
        $user = $requestEntity->getUser();
        $email = (new Email())
            ->from('noreply@example.com')
            ->to($user->getEmail())
            ->subject('Votre demande de formateur est refusée')
            ->html('<p>Bonjour '.$user->getFirstname().',</p><p>Nous sommes désolés, votre demande pour devenir formateur a été refusée.</p>');

        $mailer->send($email);

        $this->addFlash('info', 'Demande refusée et email envoyé.');

        return $this->redirectToRoute('admin_formateur_requests');
    }
    #[Route('/dashboard/add_user', name: 'admin_user_add')]
    public function addUser(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        $user = new User();

        $form = $this->createForm(UserTypeForm::class, $user, ['is_admin' => true ,'add'=>true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $hashedPassword =$hasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User created successfully!');
            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin_roles/user_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/user/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(User $user, EntityManagerInterface $em, Request $request): RedirectResponse
    {
        $currentUser = $this->getUser();

        // 🛑 Prevent self-deletion
        if ($user === $currentUser) {
            $this->addFlash('error', 'You cannot delete your own account.');
            return $this->redirectToRoute('admin_user_list'); // Replace with the actual route name
        }

        $formateurRequests = $em->getRepository(FormateurRequest::class)->findBy(['user' => $user]);
        foreach ($formateurRequests as $request) {
            $em->remove($request);
        }
        $em->remove($user);
        $em->flush();

            $this->addFlash('success', 'User deleted successfully.');

        return $this->redirectToRoute('admin_user_list'); // Replace with your route name
    }
    #[Route('/dashboard/user/{id}/edit', name: 'admin_user_edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserTypeForm::class, $user,['add'=>false]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('admin_user_list'); // Replace with your user list route name
        }

        return $this->render('admin_roles/edit_profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);}
    #[Route('/dashboard/users', name: 'admin_user_list')]
    public function listUsers(UserRepository $repo): Response
    {
        $users = $repo->findAll();

        return $this->render('admin_roles/user_list.html.twig', [
            'users' => $users
        ]);
    }



}
