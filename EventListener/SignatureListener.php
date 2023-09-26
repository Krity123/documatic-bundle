<?php

namespace Documatic\Bundle\DocumaticBundle\EventListener;

use Documatic\Bundle\DocumaticBundle\Controller\FrontendController;
use Documatic\Bundle\DocumaticBundle\Exception\SignatureException;
use Documatic\Bundle\DocumaticBundle\Model\SignatureManagerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\HttpUtils;

class SignatureListener
{
    protected $http_utils;
    protected $signature_manager;
    protected $token_storage;

    public function __construct(
        HttpUtils $http_utils,
        SignatureManagerInterface $signature_manager,
        TokenStorageInterface $token_storage
    ) {
        $this->http_utils = $http_utils;
        $this->signature_manager = $signature_manager;
        $this->token_storage = $token_storage;
    }

    public function onFilterController(FilterControllerEvent $event)
    {
        $token = $this->getTokenStorage()->getToken();

        if ($token) {
            $user = $token->getUser();

            if ($user instanceof UserInterface) {
                $controller = $event->getController();

                if (!is_array($controller)) {
                    return;
                }

                if ($controller[0] instanceof FrontendController) {
                    return;
                }

                foreach ($this->getIgnoredControllers() as $ignored_controller) {
                    if ($controller[0] instanceof $ignored_controller) {
                        return;
                    }
                }

                if (!$this->getSignatureManager()->checkUser($user)) {
                    throw new SignatureException('User has not agreed to all agreements');
                }
            }
        }
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!($exception instanceof SignatureException)) {
            return;
        }

        $response = $this->getHttpUtils()->createRedirectResponse($event->getRequest(), $this->getRedirectRoute());

        $event->setResponse($response);
    }

    protected function getHttpUtils()
    {
        return $this->http_utils;
    }

    protected function getSignatureManager()
    {
        return $this->signature_manager;
    }

    protected function getTokenStorage()
    {
        return $this->token_storage;
    }

    protected function getIgnoredControllers()
    {
        return array(
            'Symfony\Bundle\TwigBundle\Controller\ExceptionController',
            'Symfony\Bundle\AsseticBundle\Controller\AsseticController',
            'Symfony\Bundle\WebProfilerBundle\Controller\ProfilerController',
        );
    }

    protected function getRedirectRoute()
    {
        return 'documatic_frontend_agreement';
    }
}
