<?php

declare(strict_types = 1);

namespace App\Component\HttpKernel\EventListener;

use App\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Class HttpExceptionListener.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class HttpExceptionListener implements EventSubscriberInterface
{
    /**
     * Handle HTTP exceptions.
     *
     * Send JSON response with an error code, a message, and if it needed, a metadata
     * (e.g. validation errors).
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $e = $event->getException();

        if (!$e instanceof HttpException) {
            return;
        }

        $data = [
            'code' => $e->getStatusCode(),
            'message' => $e->getMessage(),
        ];
        if ($e instanceof UnprocessableEntityHttpException) {
            /** @var ConstraintViolationInterface $error */
            foreach ($e->getErrors() as $error) {
                $data['validationErrors'][] = [
                    'property' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }
        }

        $event->setResponse(
            new JsonResponse($data, $e->getStatusCode())
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
