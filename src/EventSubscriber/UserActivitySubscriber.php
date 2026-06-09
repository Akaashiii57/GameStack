<?php

namespace App\EventSubscriber;

use App\Entity\GameUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class UserActivitySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 0]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (str_starts_with($request->getPathInfo(), '/_')) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            return;
        }

        $user = $token->getUser();

        if (!$user instanceof GameUser) {
            return;
        }

        $lastActivityAt = $user->getLastActivityAt();
        $now = new \DateTimeImmutable();

        if ($lastActivityAt !== null && $lastActivityAt->getTimestamp() > $now->modify('-1 minute')->getTimestamp()) {
            return;
        }

        $user->setLastActivityAt($now);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}