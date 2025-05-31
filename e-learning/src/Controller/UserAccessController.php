<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Entity\FormateurRequest;
use App\Form\FormateurRequestType;
use App\Form\FormateurTypeForm;
use App\Form\UserTypeForm;
use App\Repository\FormateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
final class UserAccessController extends AbstractController
{
    #[Route('dashboard/demande-formateur', name: 'user_demand')]
    // src/Controller/YourController.php


    public function requestFormateur(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $existing = $em->getRepository(FormateurRequest::class)->findOneBy(['user' => $user]);
        if ($existing && $existing->isAccepted()) {
            $this->addFlash('warning', 'Vous avez déjà fait une demande.');
            return $this->redirectToRoute('dashboard_path');
        }

        $demande = new FormateurRequest();
        $demande->setUser($user);

        $form = $this->createForm(FormateurRequestType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pdfFile */
            $pdfFile = $form->get('cvPath')->getData();

            if ($pdfFile) {
                $newFilename = uniqid() . '.' . $pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('pdf_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors du téléchargement du fichier.');
                    return $this->redirectToRoute('user_demand');
                }

                $demande->setPdfFilename($newFilename);
            }

            $em->persist($demande);
            $em->flush();

            $this->addFlash('success', 'Demande envoyée avec succès.');
            return $this->redirectToRoute('dashboard_path');
        }

        return $this->render('userRoles/Demande.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/mon-profil', name: 'user_show_profile')]
    public function showProfile(FormateurRepository $formateurRepository): Response
    {
        $user = $this->getUser();
        $formateur = null;

        if (in_array('ROLE_FORMATEUR', $user->getRoles())) {
            $formateur = $formateurRepository->findOneBy(['user' => $user]);
        }

        return $this->render('userRoles/show_profile.html.twig', [
            'user' => $user,
            'formateur' => $formateur,
        ]);
    }


    #[Route('/dashboard/delete-profile', name: 'app_delete_user', methods: ['POST', 'DELETE'])]
    public function deleteProfile(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $this->container->get('security.token_storage')->setToken(null); // logout
        $formateurRequests = $em->getRepository(FormateurRequest::class)->findBy(['user' => $user]);
        foreach ($formateurRequests as $request) {
            $em->remove($request);
        }
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'Compte supprimé.');
        return $this->redirectToRoute('app_logout');
    }

    #[Route('/dashboard/profile', name: 'user_profile')]
    public function editProfile(Request $request, EntityManagerInterface $em, FormateurRepository $formateurRepository): Response
    {
        $user = $this->getUser();
        $formUser = $this->createForm(UserTypeForm::class, $user, ['is_admin' => false]);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profil utilisateur mis à jour !');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('userRoles/edit_profile.html.twig', [
            'form' => $formUser->createView(),
        ]);
    }
    #[Route('/dashboard/profile/formateur-description/edit', name: 'formateur_description_edit')]
    public function editDescription(Request $request, EntityManagerInterface $em, FormateurRepository $formateurRepository): Response
    {
        $user = $this->getUser();
        $formateur = $formateurRepository->findOneBy(['user' => $user]);

        if (!$formateur) {
            $this->addFlash('warning', 'Vous n\'êtes pas formateur.');
            return $this->redirectToRoute('user_profile');
        }

        if ($request->isMethod('POST')) {
            $description = $request->request->get('description', '');
            $formateur->setDescription($description);
            $em->persist($formateur);
            $em->flush();

            $this->addFlash('success', 'Description mise à jour.');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('userRoles/edit_formateur_description.html.twig', [
            'description' => $formateur->getDescription(),
        ]);
    }



}
