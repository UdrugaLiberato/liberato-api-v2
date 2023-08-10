<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class SendContactEmail extends AbstractController
{
    #[Route('/contact', methods: ['POST'])]
    public function sendEmail(MailerInterface $mailer, Request $request): JsonResponse
    {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $message = $request->request->get('message');

        $sendEmail = (new Email())
            ->from(new Address('no-reply@udruga-liberato.hr', 'Liberato'))
            ->to('no-reply@udruga-liberato.hr')
            ->replyTo($email)
            ->priority(Email::PRIORITY_HIGH)
            ->subject($name . ' je poslao poruku sa stranice')
            ->html('<p>Ime: ' . $name . '</p><p>Email: ' . $email . '</p><p>Poruka: ' . $message . '</p>');

        $mailer->send($sendEmail);

        return new JsonResponse(['status' => 'ok']);
        // ...
    }
}
