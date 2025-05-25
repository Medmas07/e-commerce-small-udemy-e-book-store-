<?php
// src/Controller/TestMailController.php

namespace App\Controller;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestMailController extends AbstractController
{
#[Route('/test-mail', name: 'test_mail')]
public function test(MailerInterface $mailer)
{
$email = (new Email())
->from('no-reply@example.com')
->to('you@example.com')
->subject('Test MailHog Email')
->text('If you see this in MailHog, your setup works!');

$mailer->send($email);

return $this->json(['message' => 'Email sent']);
}
}
