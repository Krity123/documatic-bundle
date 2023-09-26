<?php

namespace Documatic\Bundle\DocumaticBundle\Propel;

use Criteria;
use Documatic\Bundle\DocumaticBundle\Model\AbstractSignatureManager;
use Documatic\Bundle\DocumaticBundle\Model\AgreementInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SignatureManager extends AbstractSignatureManager
{
    const ROLE = 'ROLE_DOCUMATIC';

    protected $agreement_manager;
    protected $role_hierarchy;

    public function __construct(AgreementManager $agreement_manager, RoleHierarchyInterface $role_hierarchy)
    {
        $this->agreement_manager = $agreement_manager;
        $this->role_hierarchy = $role_hierarchy;
    }

    /**
     * {@inheritdoc}
     */
    public function checkuser(UserInterface $user)
    {
        if ($this->checkRole($user)) {
            return true;
        }

        $agreements = $this->getFinalizedAgreements();

        foreach ($agreements as $agreement) {
            $signature = SignatureQuery::create()
                ->filterByVersion($agreement->getLatestVersion())
                ->filterByEntityId($this->getUserId($user))
                ->findOne();

            if (!$signature) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function signAll(UserInterface $user)
    {
        $agreements = $this->getFinalizedAgreements();

        foreach ($agreements as $agreement) {
            $this->signAgreement($user, $agreement);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function signAgreement(UserInterface $user, AgreementInterface $agreement)
    {
        $latest_version = $agreement->getLatestVersion();

        if ($latest_version) {
            $signature = SignatureQuery::create()
                ->filterByVersion($latest_version)
                ->filterByEntityId($this->getUserId($user))
                ->findOneOrCreate();

            if ($signature->isNew()) {
                $signature->save();
            }
        }
    }

    public function getSignatureStatus(UserInterface $user, AgreementInterface $agreement)
    {
        if ($this->checkRole($user)) {
            return self::SIGNATURE_STATUS_IGNORED;
        }

        $latest_version = $agreement->getLatestVersion();

        if (!$latest_version) {
            return self::SIGNATURE_STATUS_IGNORED;
        }

        $signature = $this->getLastSignature($user, $agreement);
        $status = self::SIGNATURE_STATUS_SIGNED_LATEST;

        if (!$signature) {
            $status = self::SIGNATURE_STATUS_SIGNED_NONE;
        } elseif ($signature->getVersion() != $latest_version) {
            $status = self::SIGNATURE_STATUS_SIGNED_PREVIOUS;
        }

        return $status;
    }

    public function getLastSignature(UserInterface $user, AgreementInterface $agreement)
    {
        return SignatureQuery::create()
            ->filterByEntityId($this->getUserId($user))
            ->useVersionQuery()
                ->filterByDocument($agreement)
            ->endUse()
            ->orderByAgreedAt(Criteria::DESC)
            ->findOne();
    }

    protected function getFinalizedAgreements()
    {
        return $this->getAgreementManager()->getFinalizedAgreements();
    }

    protected function getAgreementManager()
    {
        return $this->agreement_manager;
    }

    protected function checkRole(UserInterface $user)
    {
        foreach ($user->getRoles() as $name) {
            $reachable_roles = $this->role_hierarchy->getReachableRoles(array(new Role($name)));

            foreach ($reachable_roles as $role) {
                if ($role->getRole() == static::ROLE) {
                    return true;
                }
            }
        }

        return false;
    }
}
