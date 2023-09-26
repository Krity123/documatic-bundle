<?php

namespace Documatic\Bundle\DocumaticBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractSignatureManager implements SignatureManagerInterface
{
    protected function getUserId(UserInterface $user)
    {
        if (is_callable(array($user, 'getId'))) {
            return $user->getId();
        }
    }
}
