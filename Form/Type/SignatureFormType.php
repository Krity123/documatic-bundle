<?php

namespace Documatic\Bundle\DocumaticBundle\Form\Type;

use Documatic\Bundle\DocumaticBundle\Propel\AgreementManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\EqualTo;

class SignatureFormType extends AbstractType
{
    protected $agreement_manager;

    public function __construct(AgreementManager $agreement_manager)
    {
        $this->agreement_manager = $agreement_manager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'hide_widget' => false,
            'mapped' => false,
            'translation_domain' => 'documatic',
            'validation_groups' => array('Register', 'Default'),
            'constraints' => array(
                new EqualTo(
                    array(
                        'value' => true,
                        'message' => 'signature.unsigned',
                    )
                ),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $agreements = $this->getAgreementManager()->getFinalizedAgreements();

        $view->vars = array_merge(
            $view->vars,
            array(
                'agreements' => $agreements,
                'hide_widget' => $options['hide_widget'],
            )
        );

        if (count($agreements) == 0) {
            $view->vars = array_merge(
                $view->vars,
                array(
                    'checked' => true,
                    'attr' => array_merge(
                        $options['attr'],
                        array(
                            'hidden' => 'hidden',
                        )
                    ),
                    'label_attr' => array_merge(
                        $options['label_attr'],
                        array(
                            'hidden' => 'hidden',
                        )
                    ),
                )
            );
        } else {
            if ($options['hide_widget']) {
                $view->vars = array_merge(
                    $view->vars,
                    array(
                        'checked' => true,
                        'attr' => array_merge(
                            $options['attr'],
                            array(
                                'hidden' => 'hidden',
                            )
                        ),
                    )
                );
            }
        }
    }

    public function getParent()
    {
        return CheckboxType::class;
    }

    public function getBlockPrefix()
    {
        return 'documatic_signature';
    }

    protected function getAgreementManager()
    {
        return $this->agreement_manager;
    }
}
