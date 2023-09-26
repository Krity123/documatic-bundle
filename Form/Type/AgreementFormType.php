<?php

namespace Documatic\Bundle\DocumaticBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgreementFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Symfony\Component\Security\Core\User\UserInterface',
            'translation_domain' => 'documatic',
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('agree', 'submit', array(
            'label' => 'agreements.form.submit',
        ));
    }
}
