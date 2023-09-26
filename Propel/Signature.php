<?php

namespace Documatic\Bundle\DocumaticBundle\Propel;

use DateTime;
use Documatic\Bundle\DocumaticBundle\Propel\om\BaseSignature;
use PropelPDO;

class Signature extends BaseSignature
{
    public function getDocument()
    {
        return $this->getVersion()
            ->getDocument();
    }

    public function preInsert(PropelPDO $con = null)
    {
        if ($this->getAgreedAt() == null) {
            $this->setAgreedAt(new DateTime());
        }

        return true;
    }
}
