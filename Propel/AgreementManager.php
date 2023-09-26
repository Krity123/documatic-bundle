<?php

namespace Documatic\Bundle\DocumaticBundle\Propel;

use Criteria;
use Documatic\Bundle\DocumaticBundle\Model\AgreementManagerInterface;
use Documatic\Bundle\EditorBundle\Propel\DocumentManager;

class AgreementManager extends DocumentManager implements AgreementManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function createDocument()
    {
        return new Agreement();
    }

    public function getAgreements()
    {
        return $this->getBaseQuery()
            ->find();
    }

    public function getFinalizedAgreements()
    {
        return $this->getBaseQuery()
            ->useDocumentQuery()
                ->useVersionQuery()
                    ->filterByFinalizedAt(null, Criteria::ISNOTNULL)
                ->endUse()
                ->groupBy('id')
            ->endUse()
            ->find();
    }

    protected function getBaseQuery()
    {
        return AgreementQuery::create();
    }
}
