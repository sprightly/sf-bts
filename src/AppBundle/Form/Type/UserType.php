<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $timeZones = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, 'EC');
        $timeZones = array_combine($timeZones, $timeZones);

        $builder
            ->add('email')
            ->add('username')
            ->add('fullName')
            ->add(
                'password',
                PasswordType::class,
                array(
                    'mapped' => false,
                    'required' => false,
                    'label' => 'Password (leave empty if not needed to change)',
                )
            )
            ->add(
                'timezone',
                ChoiceType::class,
                array(
                    'choices' => $timeZones,
                    'choices_as_values' => true,
                    'required' => true,
                )
            );

        if ($options['current_user_admin']) {
            $builder->add(
                'roles',
                ChoiceType::class,
                array(
                    'choices' => array(
                        'user' => User::USER_ROLE,
                        'operator' => User::OPERATOR_ROLE,
                        'manager' => User::MANAGER_ROLE,
                        'admin' => User::ADMIN_ROLE,
                    ),
                    'choices_as_values' => true,
                    'required' => true,
                    'multiple' => true,
                )
            );
        }

        $builder->add('submit', SubmitType::class, array('label' => 'Submit'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\User',
                'current_user_admin' => null
            )
        );
    }
}
