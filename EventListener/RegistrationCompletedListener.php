<?php

namespace Documatic\Bundle\DocumaticBundle\EventListener;

use Documatic\Bundle\DocumaticBundle\Model\SignatureManagerInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegistrationCompletedListener implements EventSubscriberInterface
{
    protected $signature_manager;

    public function __construct(SignatureManagerInterface $signature_manager)
    {
        $this->signature_manager = $signature_manager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationComplete',
        );
    }

    public function onRegistrationComplete(FilterUserResponseEvent $event)
    {
        $this->getSignatureManager()->signAll($event->getUser());
    }

    protected function getSignatureManager()
    {
        return $this->signature_manager;
    }
}
