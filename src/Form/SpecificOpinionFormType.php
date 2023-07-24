<?php

namespace App\Form;

use App\Entity\Opinion;
use App\Entity\Subject;
use App\Entity\Professor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecificOpinionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextareaType::class, ['required' => false])
            // ->add('keywords')
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
            // ->add('subject', EntityType::class, [
            //     'class' => Subject::class,
            //     'required' => false
            //     ])
            // ->add('professor', EntityType::class, [
            //     'class' => Professor::class,
            //     'required' => false
            //     ])
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
