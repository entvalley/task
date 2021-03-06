<?php

namespace Entvalley\AppBundle\Form;

use
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface
;

class ProjectInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invitations', 'collection', [
                    'type' => new ProjectInvitationEmailType(),
                    'allow_add' => true,
                    'label' => 'Email',
                    'required' => false
                ])
        ;
    }

    public function getName()
    {
        return 'project_invitation';
    }
}