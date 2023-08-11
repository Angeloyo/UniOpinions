<?php

namespace App\Form;

use App\Entity\Opinion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecificSubjectOpinionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextareaType::class, ['required' => false])
            ->add('keywords', ChoiceType::class, [
                'choices' => [
                    'Mucha teoría' => 'mucha_teoria',
                    'Prácticas útiles' => 'practicas_utiles',
                    'Prácticas fáciles' => 'practicas_faciles',
                    'Prácticas difíciles' => 'practicas_dificiles',
                    'Necesario conocimientos previos' => 'necesario_conocimientos_previos',
                    'Evaluación continua' => 'evaluacion_continua',
                    'Examen final' => 'examen_final',
                    'Parciales' => 'parciales'
                ],
                'required' => false,
                'expanded' => true,  // Render as checkboxes
                'multiple' => true   // Allow multiple selections
            ])
            ->add('givenScore', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ],
                'required' => false,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('Enviar', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Opinion::class,
        ]);
    }
}
