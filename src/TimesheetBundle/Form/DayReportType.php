<?php

namespace TimesheetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use TimesheetBundle\Entity\DayReportForm;

class DayReportType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('start', TimeType::class, array(
            'widget' => 'choice'
        ))
            ->add('end', TimeType::class, array(
                'widget' => 'choice'
            ))
            ->add('comment', TextareaType::class, array(
                'attr' => array('class' => 'tinymce'),
            ))
            

            ->add('date', DateType::class, array(
                'widget' => 'choice',
            ))


            ->add('projectId', ChoiceType::class, array(
                'choices' => $options['projects'],
            ))

        ->add('timeSpent', TimeType::class, array(
        'widget' => 'choice'
        ));

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DayReportForm::class,
            'projects' => array(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timesheetbundle_dayreport';
    }



}
