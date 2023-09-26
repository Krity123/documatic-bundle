<?php

namespace Documatic\Bundle\DocumaticBundle\Model;

use Documatic\Bundle\DocumaticBundle\Propel\Signature;
use Documatic\Bundle\EditorBundle\Model\VersionInterface;

interface SignatureEntityInterface
{
    public function getId();

    /**
     * Returns true if entity has agreed to all the latest finalized version of all Agreements.
     *
     * @return bool
     */
    public function hasSignedAllAgreements();

    /**
     * Returns a last Signature by entity for specified Agreement.
     *
     * @param AgreementInterface $agreement
     *
     * @return Signature
     */
    public function getLastSignature(AgreementInterface $agreement);
    /**
     * Returns collection of new Signature the entity has yet to agree to.
     *
     * @return \PropelObjectCollection
     */
    public function getPendingSignatures();

    public function addSignature(Signature $signature);

    public function hasSigned(VersionInterface $version);
}
