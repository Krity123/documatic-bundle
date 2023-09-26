<?php

namespace Documatic\Bundle\DocumaticBundle\Controller;

use Documatic\Bundle\DocumaticBundle\Form\Type\AgreementFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

class FrontendController extends Controller
{
    /**
     * @Route(
     *      "/agreements",
     *      name="documatic_frontend_agreement"
     * )
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('User not supported.');
        }

        $form = $form = $this->createForm(
            AgreementFormType::class,
            $user,
            array(
                'action' => $this->generateUrl('documatic_frontend_agreement'),
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getSignatureManager()->signAll($user);
        }

        return array(
            'finalized_agreements' => $this->getFinalizedAgreements(),
            'form' => !$this->getSignatureManager()->checkUser($user) ? $form->createView() : null,
        );
    }

    /**
     * @Route(
     *      "/{stub}",
     *       name="documatic_frontend_agreement_show",
     *      requirements={
     *          "stub": "[a-z0-9-_]+"
     *      }
     * )
     * @Template()
     */
    public function showAction(Request $request, $stub)
    {
        $agreement = $this->getAgreement($stub);

        $latest_version = $agreement->getLatestVersion();

        return array(
            'document' => $agreement,
            'version' => $latest_version,
            'section' => $latest_version ? $latest_version->getRoot() : null,
        );
    }

    /**
     * @Route(
     *      "/{stub}/diff",
     *       name="documatic_frontend_agreement_diff",
     *      requirements={
     *          "stub": "[a-z0-9-_]+"
     *      }
     * )
     * @Template()
     */
    public function diffAction(Request $request, $stub)
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('User not supported.');
        }

        $agreement = $this->getAgreement($stub);
        $latest_version = $agreement->getLatestVersion();

        if (!$latest_version) {
            throw new BadRequestHttpException('Agreement has not been finalized.');
        }

        $signature = $this->getSignatureManager()
            ->getLastSignature($user, $agreement);

        if (!$signature) {
            throw new BadRequestHttpException('User has not signed the agreement.');
        }

        return array(
            'version' => $signature->getVersion(),
            'target' => $latest_version,
        );
    }

    protected function getSignatureManager()
    {
        return $this->get('documatic.signature.manager');
    }

    protected function getFinalizedAgreements()
    {
        return $this->get('documatic.agreement.manager')->getFinalizedAgreements();
    }

    protected function getAgreement($stub)
    {
        $agreement = $this->get('documatic.agreement.manager')->getDocumentByStub($stub);

        if (!$agreement) {
            throw new NotFoundHttpException('Agreement not found');
        }

        return $agreement;
    }
}
