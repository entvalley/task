<?php

namespace Entvalley\AppBundle\Form;

use
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface
;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'textarea', array(
                'required' => true
            ))
        ;
    }

    public function getName()
    {
        return 'comment';
    }
}