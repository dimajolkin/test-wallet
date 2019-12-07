<?php

namespace App\EventListener;

use App\Exception\DomainException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionEventListener implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();
        if ($e instanceof HttpExceptionInterface || $e instanceof DomainException) {
            $response = new JsonResponse([
                    'message' => $e->getMessage(),
                ],
                $e->getStatusCode()
            );
        } else {
            $this->logger->warning($e->getMessage());
            $response = new JsonResponse([
                    'message' => $e->getMessage(),
                ],
                500
            );
        }

        $response->headers->set('Content-Type', 'application/problem+json');
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException'
        );
    }


}
