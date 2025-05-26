<?php
// src/Controller/SecurityController.php
namespace App\Controller;

use App\Form\RegistrationForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
#[Route(path: '/login', name: 'app_login')]
public function login(AuthenticationUtils $authenticationUtils): Response
{
    $form=$this->createForm(RegistrationForm::class,null);
// If user is already logged in, redirect somewhere
if ($this->getUser()) {
  return $this->redirectToRoute('dashboard_path');
}

// get the login error if there is one
$error = $authenticationUtils->getLastAuthenticationError();

// last username entered by the user
$lastUsername = $authenticationUtils->getLastUsername();

return $this->render('security/auth.html.twig', [
'last_username' => $lastUsername,
'error' => $error,
    'showRegister' => false,
    'registrationForm' => $form->createView(),

]);
}

#[Route(path: '/logout', name: 'app_logout')]
public function logout(): void
{
// controller can be blank: handled by Symfony Security
throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
}
}
