<?php

namespace App\Form;

use App\Entity\Opinion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

class GenericOpinionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('university', ChoiceType::class, [
                'choices' => [],
            ])
            ->add('degree', ChoiceType::class, [
                'choices' => [],
            ])
            ->add('year', ChoiceType::class, [
                'choices' => [
                    '' => null,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                ],
            ])
            ->add('subject', ChoiceType::class, [
                'choices' => [],
            ])
            ->add('professor', ChoiceType::class, [
                'choices' => [],
            ])
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
            ->add('Enviar', SubmitType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                
                if (array_key_exists('university', $data)) {
                    $university = $data['university'];
                    $form->add('university', ChoiceType::class, ['choices' => [$university => $university]]);
                }
                if (array_key_exists('degree', $data)) {
                    $degree = $data['degree'];
                    $form->add('degree', ChoiceType::class, ['choices' => [$degree => $degree]]);
                }
                if (array_key_exists('subject', $data)) {
                    $subject = $data['subject'];
                    $form->add('subject', ChoiceType::class, ['choices' => [$subject => $subject]]);
                }
                if (array_key_exists('professor', $data)) {
                    $professor = $data['professor'];
                    $form->add('professor', ChoiceType::class, ['choices' => [$professor => $professor]]);
                }
            })
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'constraints' => [
                new NotBlank([
                    'message' => 'Este campo es requerido.',
                    'groups' => ['university', 'degree', 'subject']
                ]),
            ],
        ]);
    }
}
