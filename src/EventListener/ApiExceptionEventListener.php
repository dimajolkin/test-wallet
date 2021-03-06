<?php

namespace App\EventListener;

use App\Exception\DomainException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidatorException;

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
        if ($e instanceof HttpExceptionInterface) {
            $response = new JsonResponse([
                    'message' => $e->getMessage(),
                ],
                $e->getStatusCode()
            );
        } else if ($e instanceof ValidatorException) {
            $this->logger->warning($e->getMessage());
            $response = new JsonResponse([
                'message' => $e->getMessage(),
            ],
                Response::HTTP_BAD_REQUEST
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
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }


}
