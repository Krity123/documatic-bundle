<?php

namespace Documatic\Bundle\DocumaticBundle\Twig\Extension;

use ReflectionObject;
use Twig_Extension;
use Twig_Extension_GlobalsInterface;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use Documatic\Bundle\DocumaticBundle\Model\AgreementInterface;
use Documatic\Bundle\DocumaticBundle\Model\AgreementManagerInterface;
use Documatic\Bundle\DocumaticBundle\Model\SignatureManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DocumaticExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
    protected $agreement_manager;

    protected $signature_manager;

    protected $token_storage;

    public function __construct(
        AgreementManagerInterface $agreement_manager,
        SignatureManagerInterface $signature_manager,
        TokenStorageInterface $token_storage
    ) {
        $this->agreement_manager = $agreement_manager;

        $this->signature_manager = $signature_manager;

        $this->token_storage = $token_storage;
    }

    public function getGlobals()
    {
        $reflection_signature_manager = new ReflectionObject($this->getSignatureManager());

        return array(
            'documatic_signature_status_signed_latest' => $reflection_signature_manager->getConstant('SIGNATURE_STATUS_SIGNED_LATEST'),
            'documatic_signature_status_signed_previous' => $reflection_signature_manager->getConstant('SIGNATURE_STATUS_SIGNED_PREVIOUS'),
            'documatic_signature_status_ignored' => $reflection_signature_manager->getConstant('SIGNATURE_STATUS_IGNORED'),
            'documatic_signature_status_signed_none' => $reflection_signature_manager->getConstant('SIGNATURE_STATUS_SIGNED_NONE'),
        );
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction(
                'all_agreements_signed',
                array($this, 'checkUser'),
                array(
                    'is_safe' => array('html'),
                )
            ),
            new Twig_SimpleFunction(
                'agreements',
                array($this, 'agreements'),
                array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter(
                'signature_status',
                array($this, 'getSignatureStatus'),
                array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    public function getSignatureStatus(AgreementInterface $agreement, UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getUser();
        }

        if ($user instanceof UserInterface) {
            return $this->getSignatureManager()->getSignatureStatus($user, $agreement);
        }

        return SignatureManagerInterface::SIGNATURE_STATUS_IGNORED;
    }

    public function agreements()
    {
        return $this->getAgreementManager()
            ->getFinalizedAgreements();
    }

    public function checkUser()
    {
        $user = $this->getUser();

        $result = false;

        if ($user instanceof UserInterface) {
            $result = $this->getSignatureManager()->checkUser($user);
        }

        return $result;
    }

    public function getName()
    {
        return 'signature';
    }

    protected function getAgreementManager()
    {
        return $this->agreement_manager;
    }

    protected function getSignatureManager()
    {
        return $this->signature_manager;
    }

    protected function getUser()
    {
        $token = $this->token_storage->getToken();

        if ($token) {
            return $token->getUser();
        }
    }
}
