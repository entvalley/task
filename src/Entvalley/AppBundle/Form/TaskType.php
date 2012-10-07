<?php

namespace Entvalley\AppBundle\Form;

use
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface
;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'textarea')
            ->add('body', 'textarea', array(
                'required' => false
            ))
        ;
    }

    public function getName()
    {
        return 'task';
    }
}