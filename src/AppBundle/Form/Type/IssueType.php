<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Issue;
use AppBundle\Type\IssuePriorityType;
use AppBundle\Type\IssueStatusType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IssueType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['hideTypeInput']) {
            $builder
                ->add(
                    'type',
                    ChoiceType::class,
                    array(
                        'choices' => array(
                            'bug' => Issue::TYPE_BUG,
                            'story' => Issue::TYPE_STORY,
                            'task' => Issue::TYPE_TASK,
                            'subtask' => Issue::TYPE_SUBTASK
                        ),
                        'choices_as_values' => true,
                        'required' => true
                    )
                );
        }

        $builder
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    'open' => IssueStatusType::OPEN,
                    'in progress' => IssueStatusType::INPROGRESS,
                    'closed' => IssueStatusType::CLOSED
                ),
                'choices_as_values' => true,
                'required' => true
            ))
            ->add('priority', ChoiceType::class, array(
                'choices' => array(
                    'blocker' => IssuePriorityType::BLOCKER,
                    'major' => IssuePriorityType::MAJOR,
                    'critical' => IssuePriorityType::CRITICAL,
                    'minor' => IssuePriorityType::MINOR,
                    'trivial' => IssuePriorityType::TRIVIAL
                ),
                'choices_as_values' => true,
                'required' => true
            ))
            ->add('summary', TextType::class, array(
                'attr' => array('maxlength' => 150)
            ))
            ->add('description')
            ->add('assignee', EntityType::class, array(
                'class' => 'AppBundle:User',
                'choice_label' => 'fullName'
            ))
            ->add('reporter', EntityType::class, array(
                'class' => 'AppBundle:User',
                'choice_label' => 'fullName'
            ))
            ->add('submit', SubmitType::class, array('label' => 'Post Issue'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Issue',
            'hideTypeInput' => false,
            'editAction' => false
        ));
    }
}
