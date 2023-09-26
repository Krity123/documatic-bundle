<?php

namespace Documatic\Bundle\DocumaticBundle\Model;

use Criteria;
use Documatic\Bundle\DocumaticBundle\Propel\AgreementQuery;
use Documatic\Bundle\DocumaticBundle\Propel\Signature;
use Documatic\Bundle\DocumaticBundle\Propel\SignatureQuery;
use Documatic\Bundle\EditorBundle\Model\VersionInterface;
use PropelObjectCollection;

trait SignatoryTrait
{
    protected $agreed = array();

    public function hasSignedAllAgreements()
    {
        $result = false;

        if (count($this->getPendingSignatures()) == 0) {
            $result = true;
        }

        return $result;
    }

    public function hasSigned(VersionInterface $version)
    {
        if (isset($this->agreed[$version->getDocumentId()][$version->getId()])) {
            return true;
        } else {
            $signature = SignatureQuery::create()
                ->filterByVersion($version)
                ->filterByEntity($this)
                ->findOne();

            if ($signature) {
                $this->agreed[$version->getDocumentId()][$version->getId()] = $signature;

                return true;
            }
        }

        return false;
    }

    public function getLastSignature(AgreementInterface $agreement)
    {
        return SignatureQuery::create()
            ->orderByAgreedAt(Criteria::DESC)
            ->filterByEntityId($this->getId())
            ->useVersionQuery()
                ->filterByDocument($agreement)
            ->endUse()
            ->findOne();
    }

    public function getPendingSignatures()
    {
        $signatures = new PropelObjectCollection();
        $signatures->setModel('Documatic\Bundle\DocumaticBundle\Propel\Signature');

        $agreements = AgreementQuery::create()
            ->find();

        foreach ($agreements as $agreement) {
            $latest_version = $agreement->getLatestVersion();

            if ($latest_version && !$this->hasSigned($latest_version)) {
                $signature = new Signature();

                $signature->setVersion($agreement->getLatestVersion());

                $signatures[$latest_version->getId()] = $signature;
            }
        }

        return $signatures;
    }

    public function setPendingSignatures(PropelObjectCollection $signatures)
    {
        foreach ($signatures as $signature) {
            $this->addSignature($signature);
        }
    }
}
