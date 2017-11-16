<?php

namespace AppBundle\Listener;

use Monolog\Logger;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ExceptionHandler
 * @package AppBundle\Handler
 */
class ExceptionListener
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $email;

    public function __construct(\Twig_Environment $twig, Logger $logger, $email)
    {
        $this->twig = $twig;
        $this->logger = $logger;
        $this->email = $email;
    }

    /**
     * This handles all the exceptions for 500 and exception for desktop & everything for the api.
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof HttpException &&
            $exception->getStatusCode() !== Response::HTTP_INTERNAL_SERVER_ERROR  &&
            $event->getRequest()->headers->get('Content-Type') != 'application/json') {
            return;
        }

        $lookupCode = time() . '-' . mt_rand(1, 100);

        $this->logger->error('LOOK UP ERROR CODE ' . $lookupCode .  '_lookup_code Message : ' . $exception->getMessage());
        $this->logger->error('LOOK UP ERROR CODE ' . $lookupCode .  '_lookup_code File : ' . $exception->getFile());
        $this->logger->error('LOOK UP ERROR CODE ' . $lookupCode .  '_lookup_code Line Number : ' . $exception->getLine());
        $this->logger->error('LOOK UP ERROR CODE ' . $lookupCode .  '_lookup_code Stack Trace : ' . $exception->getTraceAsString());


        $response = new Response();
        $response->setContent($this->twig->render('error500.html.twig', ['error_number' => $lookupCode, 'email' => $this->email]));

        if ($event->getRequest()->headers->get('Content-Type') == 'application/json') {
            $response = new JsonResponse([
                'meta' => [
                    'exceptionCode' =>  $exception->getCode(),
                    'type' => 'exception',
                    'lookupCode' => $lookupCode,
                    'instance' => get_class($exception)
                ],
                'data' => [
                    'message' => $exception->getMessage()
                ]
            ]);
        }


        $event->setResponse($response);
    }
}