<?php

namespace Documatic\Bundle\DocumaticBundle\Controller;

use Documatic\Bundle\EditorBundle\Controller\DocumentController;

class AgreementController extends DocumentController
{
    protected function getEntityManager()
    {
        return $this->container->get('documatic.agreement.manager');
    }
}
