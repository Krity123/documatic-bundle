<?php

namespace Documatic\Bundle\DocumaticBundle\Propel;

use Documatic\Bundle\DocumaticBundle\Model\AgreementInterface;
use Documatic\Bundle\DocumaticBundle\Propel\om\BaseAgreement;

class Agreement extends BaseAgreement implements AgreementInterface
{
    /**
     * Create or Update the parent Document object
     * And return its primary key.
     *
     * @return int The primary key of the parent object
     */
    public function getSyncParent($con = null)
    {
        $parent = parent::getSyncParent($con);

        $parent->setVersions($this->getVersions());

        return $parent;
    }
}
