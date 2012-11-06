<?php

namespace Entvalley\AppBundle\Form;

use
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface
;

class CommandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'textarea')
            ->add('contextId', 'hidden', array('attr' => array(
                'data-bind' => 'value: contextId'
            )))
            ->add('contextType', 'hidden', array('attr' => array(
                'data-bind' => 'value: contextType'
            )))
            ->add('contextProject', 'hidden', array('attr' => array(
                'data-bind' => 'value: contextProject'
            )))
        ;
    }

    public function getName()
    {
        return 'command';
    }
}