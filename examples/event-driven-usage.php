<?php

use Neuron\Events\AbstractEvent;

class UserRegisteredEvent extends AbstractEvent {
    public function __construct(public string $username) {
        parent::__construct(['username' => $username]);
    }
}

$eventDispatcher->listen(UserRegisteredEvent::class, function (UserRegisteredEvent $event) use ($store) {
    $mailer = $store->get(SMTPMailer::class);
    $mailer->sendWelcomeEmail($event->username);
});

$eventDispatcher->dispatch(new UserRegisteredEvent("john_doe"));
