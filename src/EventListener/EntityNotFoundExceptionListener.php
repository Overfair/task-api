<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class EntityNotFoundExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {
            $message = 'Ресурс не найден';

            if (str_contains($exception->getMessage(), 'App\Entity\Task')) {
                $message = 'Задача не найдена';
            }

            $response = new JsonResponse([
                'error' => $message,
            ], 404);

            $event->setResponse($response);
        }
    }
}
