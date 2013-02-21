<?php

namespace Entvalley\AppBundle\Form;

use
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface
;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectInvitationEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('inviteeEmail', 'email', [
                    'required' => false,
                    'attr' => [
                        'autocomplete' => 'off',
                        'class' => 'input-medium'
                    ]
               ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Entvalley\AppBundle\Entity\ProjectInvitation',
            ));
    }

    public function getName()
    {
        return 'project_invitation_email';
    }
}