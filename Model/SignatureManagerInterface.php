<?php

namespace Documatic\Bundle\DocumaticBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

interface SignatureManagerInterface
{
    const SIGNATURE_STATUS_SIGNED_LATEST = 1;
    const SIGNATURE_STATUS_SIGNED_PREVIOUS = 2;
    const SIGNATURE_STATUS_IGNORED = 3;
    const SIGNATURE_STATUS_SIGNED_NONE = 0;

    /**
     * Checks whether the user has signed all relevant agreements.
     *
     * @param UserInterface $user
     *
     * @return true if the user has signed all relevant agreements; false otherwise
     */
    public function checkUser(UserInterface $user);

    public function signAll(UserInterface $user);

    public function signAgreement(UserInterface $user, AgreementInterface $agreement);

    public function getSignatureStatus(UserInterface $user, AgreementInterface $agreement);
}
