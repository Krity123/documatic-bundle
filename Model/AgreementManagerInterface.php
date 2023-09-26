<?php

namespace Documatic\Bundle\DocumaticBundle\Model;

use Documatic\Bundle\EditorBundle\Model\DocumentManagerInterface;

interface AgreementManagerInterface extends DocumentManagerInterface
{
    public function getAgreements();

    public function getFinalizedAgreements();
}
