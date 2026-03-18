<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class MailingSubscriber implements EventSubscriberInterface
{
    // INFO: CONSTRUCTOR ────────────────────────────────────────────────────
    public function __construct(
        private readonly MailerInterface $mailer
    ) {
    }

    // ______________________________________________________________________
    public function onLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user instanceof User) {
            return;
        }

        $email = (new Email())
            ->to($user->getEmail())
            ->from('support@demo.fr')
            ->subject('⚠️ Connexion !')
            ->text('Vous vous êtes connectez !');
        $this->mailer->send($email);
    }

    // ______________________________________________________________________
    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        $data = $event->data;
        $email = (new TemplatedEmail())
            ->to($data->service)
            ->from($data->email)
            ->subject('Demande de contact')
            ->htmlTemplate('emails / contact . html . twig')
            ->context([ 'data' => $data ]);
        $this->mailer->send($email);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContactRequestEvent::class => 'onContactRequestEvent',
            InteractiveLoginEvent::class => 'onLogin',
        ];
    }
}
