<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
    }

    // ______________________________________________________________________
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                // NOTE:
                // 'empty_class' => ''
                ])
            ->add('slug', TextType::class, [
                'required' => false,
                // NOTE:
                // 'constraints' => new Sequentially(
                //     [
                //     new Length(min: 10),
                //     new Regex(pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: "Ceci n'est pas un slug valide") ,
                //     ]
                // )
            ])
            ->add('content', TextareaType::class, [
                // NOTE:
                // 'empty_class' => ''
                ])
            ->add('duration')
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->autoSlug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timeStamps())
        ;
    }

    // ______________________________________________________________________
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            // 'validation_groups' => ['Default', 'Extra'],
            // 'validation_groups' => ['Default'],
        ]);
    }
}
